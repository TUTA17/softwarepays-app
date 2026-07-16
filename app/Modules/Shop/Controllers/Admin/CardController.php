<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\CardPackage;
use App\Modules\Theme\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CardController extends Controller
{
    public function index()
    {
        $products = Product::where('product_type', Product::TYPE_CARD)->orderBy('name')->paginate(30);
        $profitMargin = Setting::getValue('card_profit_margin', '');

        return view('shop::admin.product-manage', [
            'products' => $products,
            'pageTitle' => 'Quản lý Thẻ Nạp & Thẻ Game',
            'uploadRouteName' => 'admin.card.upload_image',
            'marginRouteName' => 'admin.card.update_margin',
            'marginSettingName' => 'card_profit_margin',
            'breadcrumbLabel' => 'Thẻ Nạp',
            'profitMargin' => $profitMargin,
            'packagesRouteName' => 'admin.card.packages',
        ]);
    }

    public function updateMargin(Request $request)
    {
        $request->validate(['margin' => 'nullable|numeric|min:0|max:1000']);

        Setting::setValue('card_profit_margin', $request->margin);

        return back()->with('success', 'Đã lưu tỉ lệ lợi nhuận Thẻ Nạp. Áp dụng ở lần chạy đồng bộ tiếp theo (seed:card-catalog).');
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate(['header_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240']);

        $product = Product::where('product_type', Product::TYPE_CARD)->findOrFail($id);

        $filename = 'card_' . $product->id . '_' . now()->timestamp . '.' . $request->file('header_image')->getClientOriginalExtension();
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

    // Chiết khấu % cho khách áp dụng riêng theo từng mệnh giá (không phải % lợi nhuận chung ở
    // trang danh sách) — vd Viettel mệnh giá 500k giảm 5% cho khách nhưng mệnh giá 10k thì không.
    public function packages($id)
    {
        $product = Product::where('product_type', Product::TYPE_CARD)->findOrFail($id);
        $packages = $product->cardPackages()->orderBy('face_value')->get();

        return view('shop::admin.card-packages', compact('product', 'packages'));
    }

    public function updatePackages(Request $request, $id)
    {
        $product = Product::where('product_type', Product::TYPE_CARD)->findOrFail($id);

        $request->validate([
            'discount.*' => 'nullable|numeric|min:0|max:90',
        ]);

        foreach ($request->input('discount', []) as $packageId => $discount) {
            $package = CardPackage::where('product_id', $product->id)->find($packageId);
            if (!$package) {
                continue;
            }

            // % giảm giá tính thẳng trên mệnh giá thật (giá gốc khách nhìn thấy), không cộng dồn
            // với chiết khấu nội bộ từ nhà cung cấp — tránh sai lệch giữa % Admin nhập và % thật
            // hiển thị cho khách (đã từng xảy ra khi tính trên original_price nội bộ).
            $promo = is_numeric($discount) && $discount > 0 ? (float) $discount : null;
            $finalPrice = $promo
                ? (int) (ceil($package->face_value * (1 - $promo / 100) / 1000) * 1000)
                : ($package->original_price ?: $package->price);

            $package->update([
                'promo_discount_percent' => $promo,
                'price' => $finalPrice,
            ]);
        }

        return back()->with('success', "Đã cập nhật chiết khấu cho {$product->name}.");
    }
}
