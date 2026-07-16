<?php

namespace App\Services;

use App\Modules\Client\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WholesaleProviderService
{
    /**
     * Bật tắt chế độ giả lập (Mock Mode).
     * Khi MOCK_MODE = true, hệ thống tự động sinh Key giả lập thay vì gọi API.
     */
    protected $mockMode = true;

    public function __construct()
    {
        // Lấy cấu hình từ Settings (giá trị mặc định là 1 - Bật Mock Mode)
        $this->mockMode = \App\Modules\Core\Models\Setting::where('name', 'wholesale_mock_mode')->value('value') ?? '1';
        $this->mockMode = (bool) $this->mockMode;
    }

    /**
     * Mua tự động một Key cho Product ID
     */
    public function purchaseKey($productId)
    {
        $product = Product::findOrFail($productId);

        if ($this->mockMode) {
            return $this->generateMockKey();
        }

        return $this->fetchFromRealApi($product);
    }

    /**
     * Sinh Key giả lập để test
     */
    private function generateMockKey()
    {
        // Ví dụ: ABCD-EFGH-IJKL
        return strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5));
    }

    /**
     * Giao tiếp thực tế với API Kinguin
     */
    private function fetchFromRealApi($product)
    {
        $apiUrl = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_endpoint')->value('value');
        $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
        
        if (!$apiUrl || !$apiKey) {
            \Illuminate\Support\Facades\Log::error('Kinguin API missing config');
            return null;
        }
        
        $apiUrl = rtrim($apiUrl, '/');

        // Gửi request mua hàng tới Kinguin
        // Kinguin API POST /api/v1/orders
        $response = Http::withHeaders([
            'X-Api-Key' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post($apiUrl . '/api/v1/orders', [
            'products' => [
                [
                    'productId' => $product->wholesale_product_id ?? $product->steam_app_id,
                    'qty' => 1
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // Tùy theo response của Kinguin, nếu có key ngay thì trả về, 
            // nếu không thì đợi Webhook OrderCompleted
            if (isset($data['keys']) && count($data['keys']) > 0) {
                return $data['keys'][0]['key'] ?? null;
            }
            return 'WAITING_FOR_WEBHOOK';
        }

        \Illuminate\Support\Facades\Log::error('Kinguin API Buy failed', ['body' => $response->body()]);
        return null;
    }

    /**
     * Lấy giá gốc từ nhà cung cấp API (Mock hoặc Thật)
     * Trả về mảng chứa [giá_gốc, giá_bán_đề_xuất]
     */
    public function getWholesalePrice($steamAppId)
    {
        // 1. Lấy Tỉ lệ lợi nhuận cấu hình trong Admin (Mặc định: 15%)
        $marginSetting = \App\Modules\Core\Models\Setting::where('name', 'wholesale_profit_margin')->value('value');
        $margin = is_numeric($marginSetting) && $marginSetting > 0 ? (float)$marginSetting : 15;

        // 2. Lấy giá gốc từ Nhà cung cấp API
        $baseWholesalePrice = 0;

        if ($this->mockMode) {
            $baseWholesalePrice = rand(6, 200) * 5000;
        } else {
            // Lấy giá thật từ Kinguin
            $apiUrl = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_endpoint')->value('value');
            $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
            
            if ($apiUrl && $apiKey) {
                $apiUrl = rtrim($apiUrl, '/');
                $response = Http::withHeaders([
                    'X-Api-Key' => $apiKey
                ])->get($apiUrl . '/api/v1/products/' . $steamAppId);
                
                if ($response->successful()) {
                    $priceEur = $response->json('price') ?? 0;
                    // Tỷ giá tạm tính 1 EUR = 27000 VNĐ
                    $baseWholesalePrice = $priceEur * 27000;
                }
            }
        }

        // 3. Tính toán giá bán cuối cùng (Cộng thêm % lợi nhuận)
        $sellingPrice = $baseWholesalePrice * (1 + ($margin / 100));

        // 4. Sinh giá gốc ảo (để hiển thị khuyến mãi chéo trên web)
        $hasDiscount = rand(1, 100) <= 30; // 30% có tag giảm giá
        $originalPrice = $hasDiscount ? $sellingPrice * (1 + (rand(1, 5) * 0.1)) : $sellingPrice;

        return [
            'wholesale_cost' => $baseWholesalePrice,
            'selling_price' => round($sellingPrice, -3), // Làm tròn đến hàng nghìn
            'original_price' => round($originalPrice, -3)
        ];
    }
}
