<?php

namespace App\Modules\Shop\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // Khớp slug URL <-> product_type + tiêu đề trang, dùng chung cho các loại "key đơn giản"
    // (không cần chọn gói/mệnh giá — mua thẳng như game).
    protected const SIMPLE_TYPES = [
        'goi-dang-ky' => [Product::TYPE_SUBSCRIPTION, 'Gói đăng ký'],
        'phan-mem' => [Product::TYPE_SOFTWARE, 'Phần mềm'],
        'qua-tang' => [Product::TYPE_GIFTCARD, 'Thẻ quà tặng'],
    ];

    public function browseSimple(Request $request, string $slug)
    {
        abort_unless(isset(self::SIMPLE_TYPES[$slug]), 404);
        [$type, $title] = self::SIMPLE_TYPES[$slug];

        $products = Product::where('product_type', $type)->where('is_active', true)
            ->orderBy('name')->paginate(24);

        return view('shop::theme.catalog-simple', compact('products', 'title', 'slug'));
    }

    public function browseVpn()
    {
        $products = Product::where('product_type', Product::TYPE_VPN)->where('is_active', true)
            ->with(['vpnPackages' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->get();

        return view('shop::theme.catalog-vpn', compact('products'));
    }

    public function showVpn(int $id)
    {
        $product = Product::where('product_type', Product::TYPE_VPN)->findOrFail($id);
        $packages = $product->vpnPackages()->where('is_active', true)->orderBy('price')->get();

        return view('shop::theme.product-vpn', compact('product', 'packages'));
    }

    public function browseEsim(Request $request)
    {
        $query = Product::where('product_type', Product::TYPE_ESIM)->where('is_active', true);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->orderBy('name')->paginate(30)->withQueryString();

        return view('shop::theme.catalog-esim', compact('products'));
    }

    public function showEsim(int $id)
    {
        $product = Product::where('product_type', Product::TYPE_ESIM)->findOrFail($id);
        $packages = $product->esimPackages()->where('is_active', true)->orderBy('price')->get();

        return view('shop::theme.product-esim', compact('product', 'packages'));
    }

    public function browseCard()
    {
        $products = Product::where('product_type', Product::TYPE_CARD)->where('is_active', true)
            ->with(['cardPackages' => fn ($q) => $q->where('is_active', true)->orderBy('face_value')])
            ->get();

        return view('shop::theme.catalog-card', compact('products'));
    }

    public function showCard(int $id)
    {
        $product = Product::where('product_type', Product::TYPE_CARD)->findOrFail($id);
        $packages = $product->cardPackages()->where('is_active', true)->orderBy('face_value')->get();

        return view('shop::theme.product-card', compact('product', 'packages'));
    }
}
