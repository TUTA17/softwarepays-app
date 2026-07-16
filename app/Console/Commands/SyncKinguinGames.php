<?php

namespace App\Console\Commands;

use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\Product;
use App\Services\KinguinService;
use Illuminate\Console\Command;

// Port của kinguinCatalogSync.service.js bên softwarepays — thay thế hoàn toàn nguồn Steam
// cho danh mục game: duyệt catalog thật của Kinguin (GET /v1/products, phân trang), tạo/cập
// nhật Product theo wholesale_product_id (= productId thật của Kinguin), giá quy đổi theo
// tỷ giá EUR->VND từ giá offer rẻ nhất, "giá gốc" lấy từ offer cao nhất hợp lý (không quá 3
// lần giá bán, tránh listing rác đội giá ảo).
class SyncKinguinGames extends Command
{
    protected $signature = 'kinguin:sync-games
        {--pages=50 : Số trang tối đa sẽ quét (mỗi trang 100 sản phẩm)}
        {--start-page=1 : Trang bắt đầu (dùng để chạy tiếp nếu lần trước bị dừng giữa chừng)}
        {--name= : Lọc theo tên}
        {--platform= : Lọc theo platform (vd: steam, origin, uplay...)}
        {--genre= : Lọc theo thể loại}
        {--tags= : Lọc theo tags}';

    protected $description = 'Đồng bộ danh mục game trực tiếp từ Kinguin catalog thật (thay thế nguồn Steam)';

    protected const DEFAULT_EUR_TO_VND = 28000;
    public const MAX_ORIGINAL_PRICE_MULTIPLIER = 3;
    protected const PAGE_SIZE = 100;

    // Chặn nội dung nhạy cảm/người lớn khỏi catalog công khai — khớp config/contentBlocklist.js.
    protected const SENSITIVE_KEYWORDS = [
        'hentai', 'sex simulator', 'sex game', 'porn', 'pornstar', 'nsfw', 'erotic',
        'adult only', 'nudity', 'nude', 'naked', 'succubus', 'harem hentai',
        'strip poker', 'strip game', 'waifu uncovered',
    ];
    protected const WHOLE_WORD_KEYWORDS = ['xxx'];

    // Các thương hiệu top-up/gift-card không được Kinguin gắn nhãn nhất quán qua genre/platform
    // — khớp theo tên sản phẩm như bộ lọc "brand" bên softwarepays.
    protected const BRAND_KEYWORDS = [
        'Steam Wallet', 'PlayStation Network', 'PSN', 'XBOX Live', 'Xbox Game Pass', 'Roblox',
        'Google Play', 'Amazon', 'Razer Gold', 'Apple', 'iTunes', 'Nintendo eShop', 'Battle.net',
        'Riot', 'Spotify',
    ];

    protected function detectBrand(string $name): ?string
    {
        $lower = mb_strtolower($name);
        foreach (self::BRAND_KEYWORDS as $keyword) {
            if (str_contains($lower, mb_strtolower($keyword))) return $keyword;
        }
        return null;
    }

    protected function isSensitiveName(string $name): bool
    {
        $lower = mb_strtolower($name);
        foreach (self::SENSITIVE_KEYWORDS as $kw) {
            if (str_contains($lower, $kw)) return true;
        }
        foreach (self::WHOLE_WORD_KEYWORDS as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $lower)) return true;
        }
        return false;
    }

    // "Giá gốc" = offer cao nhất trong số các người bán khác cho cùng sản phẩm, loại bỏ
    // các listing rác định giá vượt xa phần còn lại (vd: 1 offer giá gấp 50 lần) để
    // % giảm giá hiển thị là thật, không phải chiêu trò "giảm giá ảo".
    protected function highestOfferPrice(array $item): float
    {
        $cheapest = (float) ($item['price'] ?? 0);
        $offers = $item['offers'] ?? [];
        if (empty($offers) || $cheapest <= 0) return $cheapest;

        $sanePrices = array_filter(
            array_map(fn ($o) => (float) ($o['price'] ?? 0), $offers),
            fn ($p) => $p > 0 && $p <= $cheapest * self::MAX_ORIGINAL_PRICE_MULTIPLIER
        );

        return empty($sanePrices) ? $cheapest : max($sanePrices);
    }

    public function handle(KinguinService $kinguin)
    {
        $filters = array_filter([
            'name' => $this->option('name'),
            'platforms' => $this->option('platform'),
            'genres' => $this->option('genre'),
            'tags' => $this->option('tags'),
        ]);

        // Chạy hàng trăm trang trong 1 tiến trình PHP CLI dài hạn — model Eloquent + response
        // JSON tích lũy dần vượt 128M mặc định (đã crash thật ở lần chạy trước). Tăng giới hạn
        // và ép GC định kỳ thay vì đợi PHP tự dọn.
        ini_set('memory_limit', '512M');

        $maxPages = (int) $this->option('pages');
        $startPage = max(1, (int) $this->option('start-page'));
        $eurRate = (float) Setting::getValue('kinguin_eur_rate', self::DEFAULT_EUR_TO_VND);
        $productCount = 0;
        $skippedSensitive = 0;

        for ($page = $startPage; $page <= $maxPages; $page++) {
            if ($page % 20 === 0) {
                \Illuminate\Support\Facades\DB::flushQueryLog();
                gc_collect_cycles();
            }

            $data = $kinguin->searchProducts($filters, $page, self::PAGE_SIZE);

            if ($data === null) {
                $this->error("Trang {$page}: lỗi gọi API, dừng đồng bộ.");
                break;
            }

            $results = $data['results'] ?? [];
            if (empty($results)) {
                $this->info("Trang {$page}: hết dữ liệu.");
                break;
            }

            foreach ($results as $item) {
                $name = $item['name'] ?? null;
                if (!$name) continue;

                if ($this->isSensitiveName($name)) {
                    $skippedSensitive++;
                    continue;
                }

                $referencePriceEur = (float) ($item['price'] ?? 0);
                if ($referencePriceEur <= 0) continue;

                $originalPriceEur = $this->highestOfferPrice($item);

                $price = round(($referencePriceEur * $eurRate) / 1000) * 1000;
                $compareAtRaw = round(($originalPriceEur * $eurRate) / 1000) * 1000;
                $isRealisticDiscount = $compareAtRaw > $price && $originalPriceEur <= $referencePriceEur * self::MAX_ORIGINAL_PRICE_MULTIPLIER;

                // Catalog "game" của Kinguin thực ra lẫn cả thẻ quà tặng thật (Amazon/Google Play/
                // Steam Wallet/PSN...) — trước đây gán cứng product_type=game cho MỌI item, khiến
                // ~2.300 thẻ quà tặng thật bị xếp nhầm vào danh mục Game. Chỉ những tên có "Gift Card"
                // mới coi là giftcard thật (đã verify mẫu ngẫu nhiên 100% đúng) — brand keyword một
                // mình không đủ tin cậy vì có game thật tên trùng thương hiệu (vd "Amazon Download CD
                // Key" là game, không phải thẻ Amazon).
                $isRealGiftCard = str_contains($name, 'Gift Card');

                $attrs = [
                    // Vài tên sản phẩm Kinguin dài hơn 191 ký tự (cột name VARCHAR(191)) —
                    // gây crash INSERT nếu không cắt (đã crash thật ở lần chạy trước, trang 560).
                    'name' => mb_substr($name, 0, 191),
                    'product_type' => $isRealGiftCard ? Product::TYPE_GIFTCARD : Product::TYPE_GAME,
                    'description' => mb_substr($item['description'] ?? '', 0, 4000),
                    'price' => $price,
                    'original_price' => $isRealisticDiscount ? $compareAtRaw : $price,
                    'kinguin_reference_price_eur' => $referencePriceEur,
                    'kinguin_original_price_eur' => $originalPriceEur,
                    'header_image' => $kinguin->coverImage($item),
                    'genres' => !empty($item['genres']) ? json_encode($item['genres']) : null,
                    'kinguin_platform' => $item['platform'] ?? null,
                    'kinguin_brand' => $this->detectBrand($name),
                    'popularity' => (int) ($item['qty'] ?? $item['totalQty'] ?? 0),
                    'is_active' => true,
                ];

                // steam_app_id trống nghĩa là sản phẩm nguồn Kinguin thuần (không phải game Steam thật
                // còn dùng SteamApiService) — an toàn để gán steam_data từ Kinguin mà không đè dữ liệu
                // Steam thật của các game hiếm hoi còn steam_app_id. Tái dùng đúng cấu trúc steam_data
                // (screenshots/developers/publishers/pc_requirements) để UI product.blade.php hiện sẵn
                // gallery + bảng thông tin mà không cần sửa template, chỉ thêm 'videos' là field mới.
                $existingSteamAppId = Product::where('wholesale_product_id', $item['productId'])->value('steam_app_id');
                if (empty($existingSteamAppId)) {
                    $attrs['steam_data'] = $kinguin->buildSteamDataFromKinguin($item);
                }

                Product::updateOrCreate(
                    ['wholesale_product_id' => $item['productId']],
                    $attrs
                );
                $productCount++;
            }

            $itemCount = (int) ($data['item_count'] ?? 0);
            $this->info("Trang {$page}: +" . count($results) . " sản phẩm (tổng đã xử lý: {$productCount}/{$itemCount}).");

            if (count($results) < self::PAGE_SIZE || $productCount >= $itemCount) break;

            usleep(400_000); // 400ms throttle, khớp softwarepays
        }

        $this->info("Hoàn tất: {$productCount} game đã đồng bộ từ Kinguin, bỏ qua {$skippedSensitive} sản phẩm nhạy cảm.");
        return self::SUCCESS;
    }
}
