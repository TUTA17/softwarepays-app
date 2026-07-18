<?php

namespace App\Modules\Theme\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Theme\Models\Banner;
use App\Modules\Theme\Models\Product;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\SoundMeme\Services\R2StorageService as SoundR2StorageService;
use App\Modules\GifMeme\Models\Gif;
use App\Modules\GifMeme\Services\R2StorageService as GifR2StorageService;
use App\Services\SteamApiService;

class HomeController extends Controller
{
    public function index(SteamApiService $steamApi, SoundR2StorageService $soundR2, GifR2StorageService $gifR2)
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        // Lấy danh sách game (Giới hạn 8 game hiển thị trang chủ). Trước lọc theo
        // whereNull('wholesale_product_id') để phân biệt Steam vs Kinguin — nay TẤT CẢ game
        // đều có wholesale_product_id thật của Kinguin nên phải lọc theo product_type.
        $products = Product::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('product_type')->orWhere('product_type', Product::TYPE_GAME);
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // whereNotNull('wholesale_product_id') từng chỉ khớp ~8 thẻ Steam Wallet thật, nay sẽ
        // khớp CẢ danh mục game (100k+ dòng) nếu không lọc đúng product_type — vừa sai dữ liệu
        // vừa có nguy cơ tải toàn bộ bảng products vào bộ nhớ cho trang chủ.
        $giftcards = Product::where('is_active', true)
            ->where('product_type', Product::TYPE_GIFTCARD)
            ->orderBy('price', 'asc')
            ->orderBy('id')
            ->get();

        // Dữ liệu cho các section "Danh mục" còn lại trên trang chủ (Gói đăng ký, Phần mềm,
        // Thẻ nạp & Thẻ game, VPN, eSIM Du Lịch) — số lượng sản phẩm mỗi loại này nhỏ (dưới 10)
        // nên lấy TOÀN BỘ, không giới hạn take() để không bỏ sót sản phẩm thật đang bán.
        $subscriptionProducts = Product::where('is_active', true)
            ->where('product_type', Product::TYPE_SUBSCRIPTION)
            ->orderBy('price')->orderBy('id')->get();

        $softwareProducts = Product::where('is_active', true)
            ->where('product_type', Product::TYPE_SOFTWARE)
            ->orderBy('price')->orderBy('id')->get();

        $cardProducts = Product::where('is_active', true)
            ->where('product_type', Product::TYPE_CARD)
            ->with('cardPackages')
            ->orderBy('name')->orderBy('id')->get();

        // eSIM: chọn các điểm đến du lịch phổ biến toàn cầu thay vì lấy ngẫu nhiên trong 200+ nước
        // hiện có, để section trên trang chủ thực sự hữu ích (đủ 12 nước lấp 2 hàng ở màn lớn).
        $esimHighlights = collect([
            'Japan', 'Thailand', 'South Korea', 'Singapore',
            'United States', 'United Kingdom', 'France', 'Italy',
            'Spain', 'Australia', 'Vietnam', 'United Arab Emirates',
        ])
            ->map(fn ($country) => Product::where('is_active', true)
                ->where('product_type', Product::TYPE_ESIM)
                ->where('name', 'like', $country . ' - %')
                ->with('esimPackages')
                ->first())
            ->filter()
            ->values();

        // Gắn thêm dữ liệu từ Steam API cho mỗi game
        foreach ($products as $product) {
            if ($product->steam_app_id) {
                $steamData = $steamApi->getGameDetails($product->steam_app_id);
                $product->steam_data = $steamData;
            }
        }

        // Sound Meme & GIF Meme: lấy top thịnh hành để giới thiệu ở trang chủ, giống các section
        // danh mục sản phẩm khác — link ra trang riêng của từng module để xem/nghe/tải đầy đủ.
        $homeSounds = Sound::where('status', Sound::STATUS_PUBLISHED)
            ->orderBy('play_count', 'desc')
            ->orderBy('id', 'desc')
            ->take(12)
            ->get()
            ->map(function ($s) use ($soundR2) {
                $s->play_url = $soundR2->getSignedDownloadUrl($s->object_key, 30);
                return $s;
            });

        $homeGifs = Gif::where('status', Gif::STATUS_PUBLISHED)
            ->orderBy('play_count', 'desc')
            ->orderBy('id', 'desc')
            ->take(12)
            ->get()
            ->map(function ($g) use ($gifR2) {
                $g->play_url = $gifR2->getSignedDownloadUrl($g->object_key, 30);
                return $g;
            });

        return view('theme::home', compact(
            'banners', 'products', 'giftcards', 'subscriptionProducts', 'softwareProducts',
            'cardProducts', 'esimHighlights', 'homeSounds', 'homeGifs'
        ));
    }
}
