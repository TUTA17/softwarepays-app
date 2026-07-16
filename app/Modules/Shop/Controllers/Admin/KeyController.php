<?php
namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Theme\Models\GameKey;
use App\Modules\Theme\Models\Product;

class KeyController extends Controller
{
public function keys()
    {
        $keys = GameKey::with(['product', 'user'])->orderBy('created_at', 'desc')->paginate(50);
        // Limit to 500 to prevent memory exhaustion with 20k games
        $products = Product::select('id', 'name')->orderBy('id', 'desc')->limit(500)->get();
        return view('shop::admin.keys', compact('keys', 'products'));
    }

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
}
