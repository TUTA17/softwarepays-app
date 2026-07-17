<?php

namespace App\Modules\Shop\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Theme\Models\Product;
use App\Services\SteamApiService;

class ShopController extends Controller
{
    public function index(Request $request, SteamApiService $steamApi)
    {
        // Game (nguồn Kinguin) = product_type NULL (legacy) hoặc 'game'. Trước lọc theo
        // whereNull('wholesale_product_id') để phân biệt Steam vs Kinguin — nay TẤT CẢ game
        // đều có wholesale_product_id (productId thật của Kinguin) nên phải lọc theo product_type.
        $query = Product::where('is_active', true)->where(function ($q) {
            $q->whereNull('product_type')->orWhere('product_type', Product::TYPE_GAME);
        });

        // Filter by keyword (Smart Search: Exact + Fuzzy Acronyms)
        if ($request->has('q') && $request->q != '') {
            $keyword = trim($request->q);
            
            $query->where(function($q) use ($keyword) {
                // 1. Tìm kiếm chính xác tên hoặc aliases
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('aliases', 'like', '%' . $keyword . '%');

                // 2. Tìm kiếm theo kiểu viết tắt (Fuzzy Search - Acronyms)
                // Ví dụ: "gta" -> "%g%t%a%", "ets2" -> "%e%t%s%2%"
                // Chỉ áp dụng cho các từ khóa ngắn (dưới 10 ký tự) và không có dấu cách
                if (strlen($keyword) <= 10 && !str_contains($keyword, ' ')) {
                    $fuzzyPattern = '%' . implode('%', str_split($keyword)) . '%';
                    $q->orWhere('name', 'like', $fuzzyPattern);
                }
            });
        }

        // Filter by custom price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by multiple genres (checkboxes). Game giờ lấy từ Kinguin, thể loại nằm ở cột
        // JSON `genres` (vd ["Action","RPG"]) chứ không còn qua bảng categories/pivot như hàng
        // Steam cũ (99%+ sản phẩm hiện không có dòng category_product nào) — JSON_OVERLAPS khớp
        // đúng kiểu "có ít nhất 1 thể loại trùng", giống Op.overlap bên softwarepays.
        if ($request->has('genres') && is_array($request->genres) && count($request->genres) > 0) {
            $query->whereRaw('JSON_OVERLAPS(genres, ?)', [json_encode(array_values($request->genres))]);
        }

        // Filter by platform (nền tảng kích hoạt key: Steam, Ubisoft, EA App, Battle.net...)
        if ($request->has('platforms') && is_array($request->platforms) && count($request->platforms) > 0) {
            $query->whereIn('kinguin_platform', $request->platforms);
        }

        // Filter by brand (thẻ nạp thương hiệu: Steam Wallet, PSN, Xbox Live...)
        if ($request->has('brands') && is_array($request->brands) && count($request->brands) > 0) {
            $query->whereIn('kinguin_brand', $request->brands);
        }

        // Filter by discount
        if ($request->has('is_discounted') && $request->is_discounted == '1') {
            $query->whereColumn('original_price', '>', 'price');
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        if ($sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort == 'discount_desc') {
            $query->orderByRaw('(original_price - price) / original_price DESC');
        } else {
            $query->orderBy('popularity', 'desc');
        }

        // Pagination with categories
        $products = $query->with('categories')->paginate(12)->withQueryString();

        // Danh sách thể loại cho sidebar: trước lấy tên category Steam cũ (tiếng Việt, không còn
        // khớp dữ liệu Kinguin tiếng Anh) — nay tính trực tiếp từ cột genres JSON thật, cache 6
        // tiếng vì quét ~100k+ dòng qua JSON_TABLE mỗi request là không cần thiết.
        $genres = \Illuminate\Support\Facades\Cache::remember('shop_genre_facet', now()->addHours(6), function () {
            return \Illuminate\Support\Facades\DB::select("
                SELECT jt.genre AS value, COUNT(*) AS cnt
                FROM products p, JSON_TABLE(p.genres, '$[*]' COLUMNS(genre VARCHAR(50) PATH '$')) AS jt
                WHERE (p.product_type IS NULL OR p.product_type = 'game') AND p.genres IS NOT NULL
                GROUP BY jt.genre
                ORDER BY cnt DESC
                LIMIT 25
            ");
        });
        $genres = array_column($genres, 'value');

        // Facet nền tảng — cột đơn giản (không phải JSON) nên chỉ cần GROUP BY thường.
        $platforms = \Illuminate\Support\Facades\Cache::remember('shop_platform_facet', now()->addHours(6), function () {
            return Product::where(function ($q) {
                $q->whereNull('product_type')->orWhere('product_type', Product::TYPE_GAME);
            })->whereNotNull('kinguin_platform')
                ->selectRaw('kinguin_platform as value, COUNT(*) as cnt')
                ->groupBy('kinguin_platform')
                ->orderByDesc('cnt')
                ->limit(20)
                ->pluck('value')
                ->toArray();
        });

        // Facet thương hiệu thẻ nạp (Steam Wallet, PSN, Xbox Live...) — chỉ hiện brand nào
        // thực sự có sản phẩm active để tránh checkbox chọn xong ra 0 kết quả.
        $brands = \Illuminate\Support\Facades\Cache::remember('shop_brand_facet', now()->addHours(6), function () {
            return Product::where(function ($q) {
                $q->whereNull('product_type')->orWhere('product_type', Product::TYPE_GAME);
            })->whereNotNull('kinguin_brand')
                ->selectRaw('kinguin_brand as value, COUNT(*) as cnt')
                ->groupBy('kinguin_brand')
                ->orderByDesc('cnt')
                ->pluck('value')
                ->toArray();
        });

        return view('shop::theme.index', compact('products', 'genres', 'platforms', 'brands'));
    }

    public function steamWallet()
    {
        // Trước lọc theo whereNotNull('wholesale_product_id') + tên chứa Steam/Wallet — nay MỌI
        // game (kể cả game thường có "Steam" trong tên) đều có wholesale_product_id thật của
        // Kinguin nên phải lọc đúng theo product_type=giftcard (danh mục thẻ Steam Wallet riêng).
        $products = Product::where('product_type', Product::TYPE_GIFTCARD)
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'like', '%Steam%')
                  ->orWhere('name', 'like', '%Wallet%');
            })
            ->orderBy('price', 'asc')
            ->get();

        // Danh mục nhiều trăm mệnh giá trải khắp hàng chục loại tiền tệ (IDR, TRY, ARS, HKD, INR...)
        // khiến bộ lọc Vietnam/Global cũ gần như vô dụng (đa số rơi vào "Other"). Tự nhận diện mã
        // tiền tệ từ tên sản phẩm để có bộ lọc dropdown hữu ích hơn, kèm ô tìm kiếm nhanh.
        $currencyCounts = [];
        foreach ($products as $product) {
            $code = $this->extractGiftCardCurrency($product->name);
            $product->currency_code = $code;
            $currencyCounts[$code] = ($currencyCounts[$code] ?? 0) + 1;
        }
        ksort($currencyCounts);

        return view('shop::theme.steam-wallet', compact('products', 'currencyCounts'));
    }

    // Nhận diện mã tiền tệ (IDR, TRY, USD...) từ tên sản phẩm thẻ Steam Wallet để phục vụ bộ lọc.
    protected function extractGiftCardCurrency(string $name): string
    {
        static $knownCodes = ['IDR', 'TRY', 'ARS', 'HKD', 'INR', 'USD', 'EUR', 'GBP', 'VND', 'THB', 'PHP', 'MYR', 'PLN', 'MXN', 'SAR', 'QAR', 'ZAR', 'UAH', 'KRW', 'BRL', 'CNY', 'AUD', 'AED', 'SGD', 'KWD', 'PEN', 'CAD', 'COP', 'TWD', 'CLP', 'NGN', 'OMR'];
        foreach ($knownCodes as $code) {
            if (preg_match('/\b' . $code . '\b/i', $name)) {
                return $code;
            }
        }

        static $symbolMap = ['€' => 'EUR', '£' => 'GBP', '₹' => 'INR', '₺' => 'TRY', '₴' => 'UAH', '₱' => 'PHP', '₩' => 'KRW', '฿' => 'THB'];
        foreach ($symbolMap as $symbol => $code) {
            if (str_contains($name, $symbol)) {
                return $code;
            }
        }

        if (str_contains($name, '$')) {
            return 'USD';
        }

        return 'Khác';
    }

    public function searchApi(Request $request)
    {
        if (!$request->has('q') || empty(trim($request->q))) {
            return response()->json([]);
        }

        $keyword = trim($request->q);
        
        $query = Product::where('is_active', true)->where(function ($q) {
            $q->whereNull('product_type')->orWhere('product_type', Product::TYPE_GAME);
        });

        $query->where(function($q) use ($keyword) {
            $q->where('name', 'like', '%' . $keyword . '%')
              ->orWhere('aliases', 'like', '%' . $keyword . '%');

            if (strlen($keyword) <= 10 && !str_contains($keyword, ' ')) {
                $fuzzyPattern = '%' . implode('%', str_split($keyword)) . '%';
                $q->orWhere('name', 'like', $fuzzyPattern);
            }
        });

        $products = $query->take(5)->get(['id', 'name', 'price', 'original_price', 'header_image']);

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => \App\Helpers\CurrencyHelper::formatPrice($product->price),
                'original_price' => $product->original_price > $product->price ? \App\Helpers\CurrencyHelper::formatPrice($product->original_price) : null,
                'image' => $product->header_image ?? 'https://placehold.co/100x100/1e293b/ffffff?text=Game',
                'url' => route('product.show', ['id' => $product->id, 'slug' => \Illuminate\Support\Str::slug($product->name) ?: 'game'])
            ];
        });

        return response()->json($results);
    }
}
