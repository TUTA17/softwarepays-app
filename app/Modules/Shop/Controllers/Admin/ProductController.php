<?php
namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use App\Modules\Theme\Models\Product;
use App\Modules\Shop\Models\GameKey;
use App\Modules\Theme\Models\Transaction;

class ProductController extends Controller
{
    public function products()
    {
        // Game (nguồn Kinguin) = product_type NULL (legacy) hoặc 'game'. Trước đây lọc theo
        // whereNull('wholesale_product_id') để phân biệt Steam vs Kinguin — nay TẤT CẢ game đều
        // có wholesale_product_id (productId thật của Kinguin) nên phải lọc theo product_type.
        $products = Product::where(function ($q) {
            $q->whereNull('product_type')->orWhere('product_type', Product::TYPE_GAME);
        })->withCount(['keys as available_keys' => function ($q) {
            $q->where('status', 'available');
        }])->orderBy('id', 'desc')->paginate(50);

        $eurRate = \App\Modules\Core\Models\Setting::getValue('kinguin_eur_rate', 28000);

        return view('shop::admin.products', compact('products', 'eurRate'));
    }

    public function giftcards()
    {
        $products = Product::where('product_type', Product::TYPE_GIFTCARD)->withCount(['keys as available_keys' => function ($q) {
            $q->where('status', 'available');
        }])->orderBy('id', 'desc')->paginate(50);

        return view('shop::admin.giftcards', compact('products'));
    }

    public function updateAliases(Request $request, $id)
    {
        $request->validate([
            'aliases' => 'nullable|string'
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'aliases' => $request->aliases
        ]);

        return back()->with('success', 'Cập nhật từ khóa tìm kiếm thành công!');
    }

    // API thêm nhiều Key
    public function storeKeys(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'keys_text' => 'required|string'
        ]);

        $keys = array_filter(array_map('trim', explode("\n", $request->keys_text)));
        $count = 0;

        foreach ($keys as $k) {
            if (!empty($k)) {
                GameKey::create([
                    'product_id' => $request->product_id,
                    'key_code' => $k,
                    'status' => 'available'
                ]);
                $count++;
            }
        }

        return back()->with('success', "Đã thêm thành công {$count} keys!");
    }

    // Quản lý Giao dịch (Nạp tiền, Mua hàng)
    public function transactions(Request $request)
    {
        $query = Transaction::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('month')) {
            $month = date('m', strtotime($request->month));
            $year = date('Y', strtotime($request->month));
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $transactions = $query->paginate(50)->withQueryString();
        return view('admin::transactions', compact('transactions'));
    }

    // Danh mục game giờ lấy trực tiếp từ catalog thật của Kinguin (kinguin:sync-games),
    // không còn cào dữ liệu từ Steam nữa — xem app/Console/Commands/SyncKinguinGames.php.
    public function syncSteam()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                Artisan::call('kinguin:sync-games', ['--pages' => 50]);
            } else {
                if (function_exists('exec')) {
                    $path = base_path();
                    exec("cd {$path} && php artisan kinguin:sync-games --pages=50 > /dev/null 2>&1 &");
                } else {
                    try {
                        \Illuminate\Support\Facades\Http::timeout(1)->get(url('/system/sync-steam-games/K9xP2mQvL5'));
                    } catch (\Exception $e) {
                        // Bỏ qua lỗi Timeout vì ta chủ động ép nó ngắt kết nối sau 1 giây
                    }
                }
            }
            return back()->with('success', 'Hệ thống đang đồng bộ danh mục game từ Kinguin chạy ngầm trên Server. Bạn có thể rời khỏi trang này!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi hệ thống: Vui lòng dùng CronJob để đồng bộ tự động.');
        }
    }

    public function syncKinguin()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                Artisan::call('kinguin:fetch-giftcards');
            } else {
                if (function_exists('exec')) {
                    $path = base_path();
                    exec("cd {$path} && php artisan kinguin:fetch-giftcards > /dev/null 2>&1 &");
                } else {
                    try {
                        \Illuminate\Support\Facades\Http::timeout(1)->get(url('/system/sync-kinguin/K9xP2mQvL5'));
                    } catch (\Exception $e) {
                        // Bỏ qua
                    }
                }
            }
            return back()->with('success', 'Đang đồng bộ thẻ Steam Wallet chạy ngầm trên Server. Sẽ mất 1-2 phút!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi hệ thống: Vui lòng dùng CronJob.');
        }
    }

    // Lưu tỷ giá EUR->VNĐ dùng cho cả lần đồng bộ tiếp theo (kinguin:sync-games) lẫn
    // nút "Tính lại giá gốc & giá bán" bên dưới — khớp settings.service.js#setEurExchangeRate.
    public function saveKinguinEurRate(Request $request)
    {
        $request->validate(['eur_rate' => 'required|numeric|min:1']);
        \App\Modules\Core\Models\Setting::setValue('kinguin_eur_rate', $request->input('eur_rate'));

        return back()->with('success', 'Đã lưu tỷ giá EUR → VNĐ.');
    }

    // Tính lại "giá gốc" (compare-at) và "giá bán" cho MỌI sản phẩm nguồn Kinguin từ giá EUR
    // đã lưu sẵn (kinguin_reference_price_eur/kinguin_original_price_eur) x tỷ giá hiện tại —
    // không gọi lại API Kinguin, khớp repriceAllKinguinProducts() bên softwarepays.
    public function recalculateKinguinPrices()
    {
        $eurRate = (float) \App\Modules\Core\Models\Setting::getValue('kinguin_eur_rate', 28000);
        $maxMultiplier = \App\Console\Commands\SyncKinguinGames::MAX_ORIGINAL_PRICE_MULTIPLIER;

        $updated = \Illuminate\Support\Facades\DB::update("
            UPDATE products
            SET price = ROUND((kinguin_reference_price_eur * ?) / 1000) * 1000,
                original_price = CASE
                    WHEN ROUND((kinguin_original_price_eur * ?) / 1000) * 1000 > ROUND((kinguin_reference_price_eur * ?) / 1000) * 1000
                         AND kinguin_original_price_eur <= kinguin_reference_price_eur * ?
                    THEN ROUND((kinguin_original_price_eur * ?) / 1000) * 1000
                    ELSE ROUND((kinguin_reference_price_eur * ?) / 1000) * 1000
                END,
                updated_at = NOW()
            WHERE product_type = ? AND kinguin_reference_price_eur IS NOT NULL
        ", [$eurRate, $eurRate, $eurRate, $maxMultiplier, $eurRate, $eurRate, Product::TYPE_GAME]);

        return back()->with('success', "Đã tính lại giá cho {$updated} sản phẩm theo tỷ giá {$eurRate}đ/EUR.");
    }

    public function storeProduct(Request $request)
    {
        $product = Product::create($request->all());
        return back()->with('success', 'Thêm game thành công!');
    }

    public function storeManualProduct(Request $request)
    {
        $product = Product::create($request->all());
        return back()->with('success', 'Thêm sản phẩm thủ công thành công!');
    }
}
