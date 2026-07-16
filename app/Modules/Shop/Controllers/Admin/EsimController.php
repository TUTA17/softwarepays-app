<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EsimController extends Controller
{
    public function index()
    {
        $products = Product::where('product_type', Product::TYPE_ESIM)->orderBy('name')->paginate(30);
        $profitMargin = Setting::getValue('esim_profit_margin', '');

        return view('shop::admin.product-manage', [
            'products' => $products,
            'pageTitle' => 'Quản lý eSIM',
            'uploadRouteName' => 'admin.esim.upload_image',
            'marginRouteName' => 'admin.esim.update_margin',
            'marginSettingName' => 'esim_profit_margin',
            'breadcrumbLabel' => 'eSIM',
            'profitMargin' => $profitMargin,
        ]);
    }

    public function updateMargin(Request $request)
    {
        $request->validate(['margin' => 'nullable|numeric|min:0|max:1000']);

        Setting::setValue('esim_profit_margin', $request->margin);

        return back()->with('success', 'Đã lưu tỉ lệ lợi nhuận eSIM. Áp dụng ở lần chạy đồng bộ tiếp theo (sync:esim-catalog).');
    }

    // Ảnh cờ quốc gia đã được tự động gán khi đồng bộ (sync:esim-catalog); nơi này chỉ dùng khi Admin
    // muốn thay bằng ảnh riêng — sau khi upload, lệnh đồng bộ sẽ không ghi đè ảnh này nữa.
    public function uploadImage(Request $request, $id)
    {
        $request->validate(['header_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240']);

        $product = Product::where('product_type', Product::TYPE_ESIM)->findOrFail($id);

        $filename = 'esim_' . $product->id . '_' . now()->timestamp . '.' . $request->file('header_image')->getClientOriginalExtension();
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
