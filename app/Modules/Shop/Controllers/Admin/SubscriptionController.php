<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SubscriptionController extends Controller
{
    public function index()
    {
        $products = Product::where('product_type', Product::TYPE_SUBSCRIPTION)->orderBy('name')->paginate(30);

        return view('shop::admin.product-manage', [
            'products' => $products,
            'pageTitle' => 'Quản lý Gói Đăng Ký',
            'uploadRouteName' => 'admin.subscription.upload_image',
            'videoRouteName' => 'admin.subscription.update_video',
            'breadcrumbLabel' => 'Gói Đăng Ký',
        ]);
    }

    // Không có nguồn video chính thức đáng tin cậy để tự động lấy cho dịch vụ subscription
    // (khác game/giftcard Kinguin có video_id thật trong API) — để Admin tự dán link YouTube
    // đã xác minh, tránh gắn nhầm video không chính chủ.
    public function updateVideo(Request $request, $id)
    {
        $request->validate(['video_url' => 'nullable|url']);

        $product = Product::where('product_type', Product::TYPE_SUBSCRIPTION)->findOrFail($id);
        $videoId = self::extractYoutubeId($request->input('video_url'));

        $steamData = $product->steam_data ?? [];
        $steamData['videos'] = $videoId ? [['embed_url' => 'https://www.youtube-nocookie.com/embed/' . $videoId]] : [];
        $product->update(['steam_data' => $steamData]);

        return back()->with('success', $videoId ? "Đã lưu video cho {$product->name}." : "Đã xóa video của {$product->name}.");
    }

    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) return null;
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate(['header_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240']);

        $product = Product::where('product_type', Product::TYPE_SUBSCRIPTION)->findOrFail($id);

        $filename = 'subscription_' . $product->id . '_' . now()->timestamp . '.' . $request->file('header_image')->getClientOriginalExtension();
        $destinationPath = public_path('uploads/products');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $request->file('header_image')->move($destinationPath, $filename);

        if ($product->header_image && str_contains($product->header_image, 'uploads/products/')) {
            File::delete(public_path('uploads/products/' . basename($product->header_image)));
        }

        $product->update(['header_image' => asset('uploads/products/' . $filename)]);

        return back()->with('success', "Đã cập nhật ảnh cho {$product->name}.");
    }
}
