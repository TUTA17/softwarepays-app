<?php

namespace App\Console\Commands;

use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\Product;
use App\Services\KinguinService;
use Illuminate\Console\Command;

// Đồng bộ thẻ Steam Wallet thật từ catalog Kinguin (GET /v1/products?name=Steam+Wallet) — trước
// đây dùng endpoint sai (/api/v1/products) và không set product_type, khiến 8 dòng "mock_" cũ
// lẫn vào catalog game sau khi ShopController lọc theo product_type. Giờ dùng chung KinguinService
// thật (đã dùng cho kinguin:sync-games) và gắn đúng product_type=giftcard.
class FetchKinguinGiftcards extends Command
{
    protected $signature = 'kinguin:fetch-giftcards';
    protected $description = 'Đồng bộ danh sách Thẻ Steam Wallet thật từ catalog Kinguin';

    public function handle(KinguinService $kinguin)
    {
        $this->info('Đang lấy dữ liệu thẻ Steam Wallet từ Kinguin...');

        $eurRate = (float) Setting::getValue('kinguin_eur_rate', 28000);
        $data = $kinguin->searchProducts(['name' => 'Steam Wallet'], 1, 50);

        if ($data === null) {
            $this->error('Lỗi khi gọi API Kinguin.');
            return Command::FAILURE;
        }

        $items = $data['results'] ?? [];
        $count = 0;

        foreach ($items as $item) {
            $region = (stripos($item['name'], 'Vietnam') !== false || stripos($item['name'], 'VND') !== false) ? 'Vietnam' : 'Global';
            $priceEur = (float) ($item['price'] ?? 0);
            if ($priceEur <= 0) continue;

            $sellingPrice = round(($priceEur * $eurRate) / 1000) * 1000;

            Product::updateOrCreate(
                ['wholesale_product_id' => $item['productId']],
                [
                    'name' => mb_substr($item['name'], 0, 191),
                    'product_type' => Product::TYPE_GIFTCARD,
                    'price' => $sellingPrice,
                    'original_price' => $sellingPrice,
                    'header_image' => $kinguin->coverImage($item) ?: '/images/steam_wallet_default.png',
                    'description' => isset($item['description']) ? strip_tags($item['description']) : null,
                    // Tái dùng steam_data để hiện gallery ảnh + video trailer thật trên trang chi
                    // tiết (product.blade.php dùng chung cho cả game lẫn giftcard) — khớp cách làm
                    // cho game ở kinguin:sync-games, không viết template riêng cho giftcard.
                    'steam_data' => $kinguin->buildSteamDataFromKinguin($item),
                    'is_active' => true,
                    'genres' => json_encode([$region]),
                    'seo_title' => $item['name'] . ' - Nạp thẻ giá rẻ',
                    'seo_description' => 'Mua thẻ ' . $item['name'] . ' giao mã tự động tức thì.',
                ]
            );

            $this->info("Đã đồng bộ: {$item['name']} - Giá: " . number_format($sellingPrice) . " VNĐ");
            $count++;
        }

        $this->info("Đã cập nhật/đồng bộ thành công {$count} thẻ Steam Wallet vào hệ thống!");
        return Command::SUCCESS;
    }
}
