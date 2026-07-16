<?php

namespace App\Services;

use App\Modules\Core\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Port trực tiếp của kinguin.client.js bên softwarepays — dùng đúng gateway ESA thật
// (không phải api.kinguin.net cổ điển) và đúng 2 version endpoint (v1 cho catalog, v2 cho order).
class KinguinService
{
    protected function baseUrl(): string
    {
        $configured = Setting::where('name', 'wholesale_api_endpoint')->value('value');
        $base = rtrim($configured ?: 'https://gateway.kinguin.net/esa/api', '/');

        return str_ends_with($base, '/api') ? $base : $base . '/api';
    }

    protected function apiKey(): string
    {
        return Setting::where('name', 'wholesale_api_key')->value('value') ?? '';
    }

    protected function headers(): array
    {
        return ['X-Api-Key' => $this->apiKey(), 'Content-Type' => 'application/json'];
    }

    // GET /v1/products?platform=&genre=&tags=&name=&page=&limit= — catalog thật, trả về
    // { results: [...], item_count }. Đây là API duy nhất để duyệt/tìm catalog Kinguin,
    // không có endpoint lấy 1 sản phẩm theo id.
    public function searchProducts(array $filters = [], int $page = 1, int $limit = 100): ?array
    {
        $params = array_filter([
            'platform' => $filters['platforms'] ?? null,
            'genre' => $filters['genres'] ?? null,
            'tags' => $filters['tags'] ?? null,
            'name' => $filters['name'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $params['page'] = $page;
        $params['limit'] = min($limit, 100);

        $response = Http::withHeaders($this->headers())->timeout(30)->get($this->baseUrl() . '/v1/products', $params);

        if (!$response->successful()) {
            Log::error('Kinguin searchProducts failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    // Dùng chung cho mọi lệnh đồng bộ từ Kinguin (game, giftcard/Steam Wallet...) — trước đây bị
    // duplicate ở từng Command riêng, gây lặp lại đúng 1 bug (?? thay vì ?: khi cover.url = "").
    public function coverImage(array $item): ?string
    {
        $cover = $item['images']['cover'] ?? null;
        if (!$cover) return null;

        // API Kinguin đôi khi trả cover.url = "" (chuỗi rỗng, không phải null hay thiếu key) — dùng
        // ?? thì không rơi xuống thumbnail vì chuỗi rỗng vẫn được coi là "đã có giá trị", khiến
        // header_image bị lưu thành "" và hiện icon mặc định. Tách 2 bước với ?? (tránh lỗi
        // "Undefined array key" khi key thật sự không tồn tại) rồi mới kiểm tra rỗng bằng if.
        $url = $cover['url'] ?? null;
        if ($url) return $url;

        return $cover['thumbnail'] ?? null;
    }

    // Gom ảnh/video/thông số kỹ thuật thật từ Kinguin thành đúng cấu trúc steam_data mà
    // product.blade.php đã biết hiển thị (gallery thumbnail, dòng "Phát triển bởi", video trailer,
    // bảng cấu hình) — tránh phải viết thêm 1 bộ template riêng cho nguồn Kinguin. Áp dụng cho MỌI
    // loại sản phẩm lấy từ Kinguin (game, giftcard/Steam Wallet), không chỉ riêng game.
    public function buildSteamDataFromKinguin(array $item): array
    {
        $screenshots = collect($item['images']['screenshots'] ?? [])
            ->map(fn ($s) => $s['url'] ?? $s['thumbnail'] ?? null)
            ->filter()
            ->values()
            ->all();

        // videos[].video_id là mã YouTube thuần (không phải URL) — khớp cách nhúng iframe an toàn
        // qua youtube-nocookie.com, không tải tracking cookie khi trang chưa được bấm play.
        $videos = collect($item['videos'] ?? [])
            ->filter(fn ($v) => !empty($v['video_id']))
            ->map(fn ($v) => ['embed_url' => 'https://www.youtube-nocookie.com/embed/' . $v['video_id']])
            ->values()
            ->all();

        $windowsReq = collect($item['systemRequirements'] ?? [])->first(fn ($r) => ($r['system'] ?? '') === 'Windows');

        $steamData = [
            'screenshots' => $screenshots,
            'developers' => $item['developers'] ?? [],
            'publishers' => $item['publishers'] ?? [],
            'videos' => $videos,
        ];

        if ($windowsReq && !empty($windowsReq['requirement'])) {
            $steamData['pc_requirements'] = [
                'minimum' => implode('<br><br>', array_map('e', $windowsReq['requirement'])),
            ];
        }

        return $steamData;
    }

    // POST /v2/order — mua thật, trả về { orderId, ... }.
    public function placeOrder(array $products, string $orderExternalId): ?array
    {
        $response = Http::withHeaders($this->headers())->timeout(30)->post($this->baseUrl() . '/v2/order', [
            'products' => $products,
            'orderExternalId' => $orderExternalId,
        ]);

        if (!$response->successful()) {
            Log::error('Kinguin placeOrder failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function getOrder(string $orderId): ?array
    {
        $response = Http::withHeaders($this->headers())->timeout(20)->get($this->baseUrl() . "/v1/order/{$orderId}");

        return $response->successful() ? $response->json() : null;
    }

    // GET /v2/order/{orderId}/keys — trả về mảng { productId, serial, ... } khi key đã sẵn sàng.
    public function getOrderKeys(string $orderId): ?array
    {
        $response = Http::withHeaders($this->headers())->timeout(20)
            ->get($this->baseUrl() . "/v2/order/{$orderId}/keys", ['limit' => 100]);

        if (!$response->successful()) {
            Log::error('Kinguin getOrderKeys failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }
}
