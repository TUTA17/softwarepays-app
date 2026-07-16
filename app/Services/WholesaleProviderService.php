<?php

namespace App\Services;

use App\Modules\Theme\Models\Product;
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
     * Giao tiếp thực tế với API Kinguin — khớp kinguinFulfillment.service.js bên softwarepays:
     * POST /esa/api/v2/order rồi GET /esa/api/v2/order/{id}/keys. Key thường sẵn sàng gần như
     * ngay sau khi đặt hàng, nên thử lấy luôn 1 lần; nếu chưa có, trả 'WAITING_FOR_WEBHOOK' để
     * checkout không coi là thất bại (không có polling nền cho luồng key đơn cũ này).
     */
    private function fetchFromRealApi($product)
    {
        if (!$product->wholesale_product_id) {
            \Illuminate\Support\Facades\Log::error('Kinguin purchaseKey: sản phẩm chưa có wholesale_product_id', ['product_id' => $product->id]);
            return null;
        }

        $kinguin = app(\App\Services\KinguinService::class);
        $orderExternalId = 'kg-' . $product->id . '-' . time();

        $order = $kinguin->placeOrder([
            ['productId' => $product->wholesale_product_id, 'qty' => 1],
        ], $orderExternalId);

        if (!$order || empty($order['orderId'])) {
            return null;
        }

        $keys = $kinguin->getOrderKeys((string) $order['orderId']);
        if (is_array($keys)) {
            foreach ($keys as $k) {
                if (($k['productId'] ?? null) == $product->wholesale_product_id && !empty($k['serial'])) {
                    return $k['serial'];
                }
            }
        }

        return 'WAITING_FOR_WEBHOOK';
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

        $baseWholesalePrice = 0;
        $maxWholesalePrice = 0;

        if ($this->mockMode) {
            $baseWholesalePrice = rand(6, 200) * 5000;
            // Fake giá gốc cao hơn giá bán cho mock mode
            $maxWholesalePrice = $baseWholesalePrice * (1 + (rand(1, 5) * 0.1));
        } else {
            // Lấy giá thật từ Kinguin
            $apiUrl = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_endpoint')->value('value');
            $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
            
            if ($apiUrl && $apiKey) {
                $apiUrl = rtrim($apiUrl, '/');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-Api-Key' => $apiKey
                ])->get($apiUrl . '/api/v1/products/' . $steamAppId);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $priceEur = $data['price'] ?? 0;
                    $maxPriceEur = $priceEur;

                    // Thử quét giá gốc/giá cao nhất từ dữ liệu thực tế của Kinguin
                    if (isset($data['retailPrice']) && $data['retailPrice'] > $maxPriceEur) {
                        $maxPriceEur = $data['retailPrice'];
                    } elseif (isset($data['highestPrice']) && $data['highestPrice'] > $maxPriceEur) {
                        $maxPriceEur = $data['highestPrice'];
                    } elseif (isset($data['offers']) && is_array($data['offers'])) {
                        $prices = array_column($data['offers'], 'price');
                        if (!empty($prices)) {
                            $maxOffer = max($prices);
                            if ($maxOffer > $maxPriceEur) {
                                $maxPriceEur = $maxOffer;
                            }
                        }
                    }

                    // Tỷ giá tạm tính 1 EUR = 27000 VNĐ
                    $baseWholesalePrice = $priceEur * 27000;
                    $maxWholesalePrice = $maxPriceEur * 27000;
                } else {
                    \Illuminate\Support\Facades\Log::error('Kinguin Price Sync Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'url' => $apiUrl . '/api/v1/products/' . $steamAppId
                    ]);
                }
            }
        }

        // Tính toán giá bán cuối cùng và giá gốc (Cộng thêm % lợi nhuận)
        $sellingPrice = $baseWholesalePrice * (1 + ($margin / 100));
        
        // Nếu API trả về giá cao nhất > giá bán, áp dụng làm giá gốc. Nếu không thì bằng giá bán (không giảm).
        $originalPrice = $maxWholesalePrice > $baseWholesalePrice 
                            ? $maxWholesalePrice * (1 + ($margin / 100)) 
                            : $sellingPrice;

        return [
            'wholesale_cost' => $baseWholesalePrice,
            'selling_price' => round($sellingPrice, -3), // Làm tròn đến hàng nghìn
            'original_price' => round($originalPrice, -3)
        ];
    }

    /**
     * Kiểm tra số lượng tồn kho của đối tác
     */
    public function checkStock($productId)
    {
        if ($this->mockMode) {
            // Giả lập 80% trường hợp có hàng
            return rand(0, 10) > 2;
        }

        $apiUrl = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_endpoint')->value('value');
        $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
        
        if ($apiUrl && $apiKey) {
            $apiUrl = rtrim($apiUrl, '/');
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(3)->withHeaders([
                    'X-Api-Key' => $apiKey
                ])->get($apiUrl . '/api/v1/products/' . $productId);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $qty = $data['qty'] ?? $data['quantity'] ?? 0;
                    return (int)$qty > 0;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Wholesale checkStock error: ' . $e->getMessage());
            }
        }
        return false;
    }
}
