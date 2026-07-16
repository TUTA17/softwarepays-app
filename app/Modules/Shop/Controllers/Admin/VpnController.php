<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VpnController extends Controller
{
    public function index()
    {
        $products = Product::where('product_type', Product::TYPE_VPN)->orderBy('name')->paginate(30);
        $profitMargin = Setting::getValue('vpn_profit_margin', '');
        $exchangeRate = Setting::getValue('vpn_usd_rate', 25000);

        return view('shop::admin.product-manage', [
            'products' => $products,
            'pageTitle' => 'Quản lý VPN',
            'uploadRouteName' => 'admin.vpn.upload_image',
            'marginRouteName' => 'admin.vpn.update_margin',
            'marginSettingName' => 'vpn_profit_margin',
            'breadcrumbLabel' => 'VPN',
            'profitMargin' => $profitMargin,
            'exchangeRateRouteName' => 'admin.vpn.update_rate',
            'exchangeRate' => $exchangeRate,
        ]);
    }

    public function updateMargin(Request $request)
    {
        $request->validate(['margin' => 'nullable|numeric|min:0|max:1000']);

        Setting::setValue('vpn_profit_margin', $request->margin);

        return back()->with('success', 'Đã lưu tỉ lệ lợi nhuận VPN. Áp dụng ở lần chạy đồng bộ tiếp theo (sync:vpn-catalog).');
    }

    // VPN Panels API trả giá gói bằng USD, cần quy đổi ra VNĐ khi đồng bộ (sync:vpn-catalog) —
    // trước đây hardcode 25.000đ/$ trong code, giờ Admin tự chỉnh tỷ giá này theo thị trường.
    public function updateExchangeRate(Request $request)
    {
        $request->validate(['usd_rate' => 'required|numeric|min:1']);

        Setting::setValue('vpn_usd_rate', $request->usd_rate);

        return back()->with('success', 'Đã lưu tỷ giá USD → VNĐ. Áp dụng ở lần chạy đồng bộ tiếp theo (sync:vpn-catalog).');
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate(['header_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240']);

        $product = Product::where('product_type', Product::TYPE_VPN)->findOrFail($id);

        $filename = 'vpn_' . $product->id . '_' . now()->timestamp . '.' . $request->file('header_image')->getClientOriginalExtension();
        $destinationPath = public_path('uploads/products');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $request->file('header_image')->move($destinationPath, $filename);

        // Xóa ảnh cũ nếu là ảnh tự upload trước đó (nằm trong uploads/products), không đụng tới link ngoài.
        if ($product->header_image && str_contains($product->header_image, 'uploads/products/')) {
            File::delete(public_path('uploads/products/' . basename($product->header_image)));
        }

        $product->update(['header_image' => asset('uploads/products/' . $filename)]);

        return back()->with('success', "Đã cập nhật ảnh cho {$product->name}.");
    }
}
