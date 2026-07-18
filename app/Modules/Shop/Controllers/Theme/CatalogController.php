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

        $query = Product::where('product_type', $type)->where('is_active', true);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Thẻ quà tặng (qua-tang) có tới hàng nghìn sản phẩm trải khắp hàng trăm thương hiệu
        // (Google Play, Amazon, Netflix...) -> gộp phẳng vào 1 danh sách là không thể chọn nổi.
        // Thêm bộ lọc theo thương hiệu (đoán từ tên sản phẩm) để khách thu hẹp trước khi chọn.
        $brandCounts = [];
        if ($slug === 'qua-tang') {
            $brandCounts = \Illuminate\Support\Facades\Cache::remember('giftcard_brand_counts', 3600, function () use ($type) {
                $counts = [];
                Product::where('product_type', $type)->where('is_active', true)
                    ->pluck('name')->each(function ($name) use (&$counts) {
                        $brand = $this->extractGiftCardBrand($name);
                        $counts[$brand] = ($counts[$brand] ?? 0) + 1;
                    });
                arsort($counts);
                return $counts;
            });

            if ($request->filled('brand') && isset($brandCounts[$request->brand])) {
                $query->where('name', 'like', str_replace(['%', '_'], ['\\%', '\\_'], $request->brand) . '%');
            }
        }

        $products = $query->orderBy('name')->paginate(24)->withQueryString();

        return view('shop::theme.catalog-simple', compact('products', 'title', 'slug', 'brandCounts'));
    }

    // Đoán tên thương hiệu từ tên sản phẩm thẻ quà tặng (VD: "Google Play SAR 500 Gift Card SA"
    // -> "Google Play", "Thẻ Google Play 100.000đ" -> "Thẻ Google Play") — cắt tại vị trí xuất
    // hiện đầu tiên của số, ký hiệu tiền tệ, hoặc cụm "Gift Card", phần còn lại phía trước là brand.
    protected function extractGiftCardBrand(string $name): string
    {
        $brand = $name;
        if (preg_match('/^(.*?)(?:\d|[\$€£₺₴₱₩฿₹]|Gift Card)/u', $name, $m) && trim($m[1]) !== '') {
            $brand = trim($m[1]);
        }

        // Bỏ mã tiền tệ 3 ký tự còn sót lại ở cuối (VD: "Google Play EUR" -> "Google Play") để
        // gộp đúng 1 thương hiệu thay vì tách vụn theo từng loại tiền tệ khác nhau.
        static $currencyCodes = ['IDR', 'TRY', 'ARS', 'HKD', 'INR', 'USD', 'EUR', 'GBP', 'VND', 'THB', 'PHP', 'MYR', 'PLN', 'MXN', 'SAR', 'QAR', 'ZAR', 'UAH', 'KRW', 'BRL', 'CNY', 'AUD', 'AED', 'SGD', 'KWD', 'PEN', 'CAD', 'COP', 'TWD', 'CLP', 'NGN', 'OMR', 'CZK', 'DKK', 'NOK', 'SEK', 'CHF', 'RON', 'HUF', 'BGN', 'ILS', 'JPY', 'NZD', 'RUB'];
        $brand = trim(preg_replace('/\s+(?:' . implode('|', $currencyCodes) . ')$/i', '', $brand));

        return $brand !== '' ? $brand : trim(preg_replace('/\s+/', ' ', $name));
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
        // Thẻ cào nhà mạng (Viettel/Mobifone/Vinaphone/Vietnamobile) chỉ nạp được ở VN -> ẩn
        // hẳn khỏi khách xem site bằng ngôn ngữ khác tiếng Việt, không chỉ làm mờ.
        $products = Product::where('product_type', Product::TYPE_CARD)->where('is_active', true)
            ->when(app()->getLocale() !== 'vi', fn ($q) => $q->whereNotIn('name', [
                'Thẻ cào Viettel', 'Thẻ cào Mobifone', 'Thẻ cào Vinaphone', 'Thẻ cào Vietnamobile',
            ]))
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
