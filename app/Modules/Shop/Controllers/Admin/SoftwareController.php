<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SoftwareController extends Controller
{
    public function index()
    {
        $products = Product::where('product_type', Product::TYPE_SOFTWARE)->orderBy('name')->paginate(30);

        return view('shop::admin.product-manage', [
            'products' => $products,
            'pageTitle' => 'Quản lý Phần Mềm',
            'uploadRouteName' => 'admin.software.upload_image',
            'videoRouteName' => 'admin.software.update_video',
            'breadcrumbLabel' => 'Phần Mềm',
        ]);
    }

    // Xem giải thích ở SubscriptionController::updateVideo — không tự lấy video, để Admin tự dán
    // link đã xác minh.
    public function updateVideo(Request $request, $id)
    {
        $request->validate(['video_url' => 'nullable|url']);

        $product = Product::where('product_type', Product::TYPE_SOFTWARE)->findOrFail($id);
        $videoId = \App\Modules\Shop\Controllers\Admin\SubscriptionController::extractYoutubeId($request->input('video_url'));

        $steamData = $product->steam_data ?? [];
        $steamData['videos'] = $videoId ? [['embed_url' => 'https://www.youtube-nocookie.com/embed/' . $videoId]] : [];
        $product->update(['steam_data' => $steamData]);

        return back()->with('success', $videoId ? "Đã lưu video cho {$product->name}." : "Đã xóa video của {$product->name}.");
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate(['header_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240']);

        $product = Product::where('product_type', Product::TYPE_SOFTWARE)->findOrFail($id);

        $filename = 'software_' . $product->id . '_' . now()->timestamp . '.' . $request->file('header_image')->getClientOriginalExtension();
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
