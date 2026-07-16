<?php

namespace App\Modules\Shop\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Modules\Theme\Models\Product;
use App\Modules\Theme\Models\Transaction;
use App\Modules\Theme\Models\User;

class PurchaseController extends Controller
{
    public function buy(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->is_active) {
            return back()->with('error', 'Sản phẩm này hiện ngừng bán!');
        }

        // Tự động thêm vào giỏ hàng 1 sản phẩm
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->header_image
            ];
        }

        session()->put('cart', $cart);

        // Thay vì trừ tiền luôn, chuyển sang trang xác nhận thanh toán
        return redirect()->route('cart.checkout');
    }
}
