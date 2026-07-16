<?php

namespace App\Modules\Shop\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Theme\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);
        
        // Kiểm tra xem game này còn Key không (Hoặc có cấu hình lấy từ API không)
        $availableKeysCount = $product->available_keys;
        
        return view('shop::theme.product', compact('product', 'availableKeysCount'));
    }
}
