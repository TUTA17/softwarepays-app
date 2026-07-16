<?php
namespace App\Modules\Auth\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Theme\Models\User;

class UserController extends Controller
{
public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(50);
        return view('auth::admin.users', compact('users'));
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

    // API thêm sản phẩm thủ công (Wallet, Giftcard...)
    public function storeManualProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'wholesale_product_id' => 'required|string|unique:products,wholesale_product_id',
            'price' => 'required|numeric|min:0',
            'header_image' => 'nullable|url',
            'description' => 'nullable|string'
        ]);

        Product::create([
            'name' => $request->name,
            'steam_app_id' => null, // Không phải game Steam
            'wholesale_product_id' => $request->wholesale_product_id,
            'header_image' => $request->header_image,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => true
        ]);

        return back()->with('success', 'Thêm sản phẩm thủ công thành công!');
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
}
