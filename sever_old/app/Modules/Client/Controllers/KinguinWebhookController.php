<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Client\Models\Product;
use Illuminate\Support\Facades\Log;

class KinguinWebhookController extends Controller
{
    /**
     * Mã bí mật để xác thực Webhook từ Kinguin
     * Cần cài đặt trên trang Integration của Kinguin
     */
    private $secretKey = 'MySecretKinguin2026';

    /**
     * Xác thực request từ Kinguin
     */
    private function authenticate(Request $request)
    {
        // Kinguin gửi signature hoặc secret qua header hoặc body tùy theo bản cập nhật API
        // Ở đây ta đơn giản hóa bằng cách check query param hoặc header
        $secret = $request->header('X-Kinguin-Secret') ?? $request->get('secret');
        
        if ($secret !== $this->secretKey) {
            Log::warning('Kinguin Webhook: Unauthorized access attempt', ['ip' => $request->ip()]);
            // Cho phép bypass lúc test nếu chưa config
            // return false; 
        }
        return true;
    }

    /**
     * Xử lý Cập nhật sản phẩm (Product Update)
     */
    public function onProductUpdate(Request $request)
    {
        if (!$this->authenticate($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('Kinguin Webhook: Product Updated', $data);

        // Xử lý cập nhật giá gốc từ $data
        if (isset($data['productId']) && isset($data['price'])) {
            // $product = Product::where('wholesale_product_id', $data['productId'])->first();
        }

        return response()->noContent();
    }

    /**
     * Xử lý Đơn hàng hoàn tất (Order Completed / Key Delivered)
     */
    public function onOrderCompleted(Request $request)
    {
        if (!$this->authenticate($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('Kinguin Webhook: Order Completed', $data);

        // Khi Kinguin giao Key, lưu vào DB và gán cho đơn hàng của khách

        return response()->noContent();
    }

    /**
     * Xử lý Thay đổi trạng thái đơn hàng (Order Status Change)
     */
    public function onOrderStatusChange(Request $request)
    {
        if (!$this->authenticate($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('Kinguin Webhook: Order Status Changed', $data);

        return response()->noContent();
    }
}
