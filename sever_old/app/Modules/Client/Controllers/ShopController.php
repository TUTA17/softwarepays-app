<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Client\Models\Product;
use App\Services\SteamApiService;

class ShopController extends Controller
{
    public function index(Request $request, SteamApiService $steamApi)
    {
        $query = Product::where('is_active', true);

        // Filter by keyword (searches name OR aliases)
        if ($request->has('q') && $request->q != '') {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('aliases', 'like', '%' . $keyword . '%');
            });
        }

        // Filter by custom price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by multiple genres (checkboxes)
        if ($request->has('genres') && is_array($request->genres) && count($request->genres) > 0) {
            $query->where(function($q) use ($request) {
                foreach ($request->genres as $g) {
                    $q->orWhere('genres', 'like', '%' . $g . '%');
                }
            });
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
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $products = $query->paginate(12)->withQueryString();

        // Get genres for sidebar (Cached to prevent memory exhaustion and slowness)
        $genres = \Illuminate\Support\Facades\Cache::remember('shop_genres_list', 86400, function () {
            $rows = \Illuminate\Support\Facades\DB::table('products')->where('is_active', true)->whereNotNull('genres')->pluck('genres');
            $genresList = [];
            foreach ($rows as $row) {
                $arr = json_decode($row, true);
                if (!$arr && is_string($row)) {
                    $arr = array_map('trim', explode(',', $row));
                }
                if ($arr && is_array($arr)) {
                    foreach ($arr as $g) {
                        if (!in_array($g, $genresList)) {
                            $genresList[] = $g;
                        }
                    }
                }
            }
            sort($genresList);
            return $genresList;
        });

        return view('shop.index', compact('products', 'genres'));
    }
}
