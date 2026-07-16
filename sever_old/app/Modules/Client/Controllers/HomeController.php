<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Client\Models\Product;
use App\Services\SteamApiService;

class HomeController extends Controller
{
    public function index(SteamApiService $steamApi)
    {
        // Lấy danh sách game (Giới hạn 8 game hiển thị trang chủ)
        $products = Product::where('is_active', true)->orderBy('created_at', 'desc')->take(8)->get();
        
        // Gắn thêm dữ liệu từ Steam API cho mỗi game
        foreach ($products as $product) {
            if ($product->steam_app_id) {
                $steamData = $steamApi->getGameDetails($product->steam_app_id);
                $product->steam_data = $steamData;
            }
        }
        
        return view('home', compact('products'));
    }
}
