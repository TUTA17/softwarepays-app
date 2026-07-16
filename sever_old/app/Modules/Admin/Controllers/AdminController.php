<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Modules\Client\Models\User;
use App\Modules\Client\Models\Product;
use App\Modules\Client\Models\GameKey;
use App\Modules\Client\Models\Transaction;

class AdminController extends Controller
{


    public function products()
    {
        $products = Product::withCount(['keys as available_keys' => function ($q) {
            $q->where('status', 'available');
        }])->orderBy('id', 'desc')->paginate(50);
        
        return view('admin::products', compact('products'));
    }

    public function keys()
    {
        $keys = GameKey::with(['product', 'user'])->orderBy('created_at', 'desc')->paginate(50);
        // Limit to 500 to prevent memory exhaustion with 20k games
        $products = Product::select('id', 'name')->orderBy('id', 'desc')->limit(500)->get();
        return view('admin::keys', compact('keys', 'products'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(50);
        return view('admin::users', compact('users'));
    }

    // API thêm Game từ Steam
    public function storeProduct(Request $request)
    {
        $request->validate([
            'steam_app_id' => 'required|numeric|unique:products',
            'price' => 'required|numeric|min:0'
        ]);

        $appId = $request->steam_app_id;
        // Gọi API Steam có cc=vn và l=vietnamese
        $response = Http::get("http://store.steampowered.com/api/appdetails?appids={$appId}&cc=vn&l=vietnamese");
        if (!$response->successful() || !isset($response->json()[$appId]['data'])) {
            return back()->with('error', 'Không tìm thấy Game với App ID này!');
        }

        $data = $response->json()[$appId]['data'];
        
        // Extract genres
        $genres = [];
        if (isset($data['genres'])) {
            foreach ($data['genres'] as $genre) {
                $genres[] = $genre['description'];
            }
        }

        // Extract original price
        $originalPrice = null;
        if (isset($data['price_overview']['initial'])) {
            // Steam trả về cents hoặc phần trăm xu, nên cần chia 100
            $originalPrice = $data['price_overview']['initial'] / 100;
        }

        Product::create([
            'name' => $data['name'],
            'steam_app_id' => $appId,
            'header_image' => $data['header_image'] ?? null,
            'original_price' => $originalPrice,
            'description' => $data['detailed_description'] ?? ($data['about_the_game'] ?? null),
            'genres' => json_encode($genres, JSON_UNESCAPED_UNICODE),
            'price' => $request->price,
            'is_active' => true
        ]);

        return back()->with('success', 'Thêm game thành công!');
    }

    // Cập nhật từ khóa phụ (aliases) cho game
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

    public function exportTransactions(Request $request)
    {
        $query = Transaction::with('user')
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');

        if ($request->filled('month')) {
            $month = date('m', strtotime($request->month));
            $year = date('Y', strtotime($request->month));
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $filename = "Lich_su_nap_tien_{$month}_{$year}.csv";
        } else {
            $filename = "Lich_su_nap_tien_Tat_ca.csv";
        }

        $transactions = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            // Thêm BOM để Excel đọc đúng UTF-8
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Khách hàng', 'Số tiền', 'Mô tả', 'Mã tham chiếu', 'Thời gian']);

            foreach ($transactions as $tx) {
                fputcsv($file, [
                    $tx->id,
                    $tx->user ? $tx->user->name : 'N/A',
                    $tx->amount,
                    $tx->description,
                    $tx->reference_id,
                    $tx->created_at->format('d/m/Y H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function paymentSettings()
    {
        $settings = \App\Modules\Core\Models\Setting::getAllGrouped();
        $paymentConfig = $settings['payment_tab'] ?? [];
        return view('admin::payment-settings', compact('paymentConfig'));
    }

    public function savePaymentSettings(Request $request)
    {
        $request->validate([
            'sepay_logo' => 'nullable|string',
            'sepay_name' => 'required|string',
            'sepay_client_id' => 'required|string',
            'sepay_secret_key' => 'required|string'
        ]);

        \App\Modules\Core\Models\Setting::setValue('sepay_logo', $request->sepay_logo);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_logo'], ['value' => $request->sepay_logo, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_name'], ['value' => $request->sepay_name, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_client_id'], ['value' => $request->sepay_client_id, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_secret_key'], ['value' => $request->sepay_secret_key, 'type' => 'payment_tab']);

        return back()->with('success', 'Cập nhật cấu hình thanh toán thành công!');
    }
}
