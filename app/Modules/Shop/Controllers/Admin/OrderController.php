<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\GameKey;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending_manual');

        $query = GameKey::with('product')->whereNotNull('sold_to_user_id');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('sold_at')->paginate(30)->withQueryString();

        $userIds = $orders->pluck('sold_to_user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        $pendingCount = GameKey::where('status', 'pending_manual')->count();
        $fulfillmentMode = Setting::getValue('order_fulfillment_mode', 'manual');

        return view('shop::admin.orders', compact('orders', 'users', 'status', 'pendingCount', 'fulfillmentMode'));
    }

    // Bật/tắt nhanh chế độ xử lý đơn (thủ công / tự động) ngay tại trang Đơn Hàng,
    // không cần vào Cài đặt riêng — cùng 1 setting với trang Cài đặt > Tự động hoá & API.
    public function toggleMode(Request $request)
    {
        $request->validate(['mode' => 'required|in:manual,auto']);

        Setting::setValue('order_fulfillment_mode', $request->mode);

        $label = $request->mode === 'auto' ? 'Tự động' : 'Thủ công';
        return back()->with('success', "Đã chuyển chế độ xử lý đơn hàng sang: {$label}.");
    }

    // Admin nhập tay key/nội dung đã tự mua ở nơi khác, đánh dấu đơn hoàn tất.
    public function fulfillManual(Request $request, $id)
    {
        $request->validate(['key_code' => 'required|string|max:5000']);

        $gameKey = GameKey::findOrFail($id);
        $gameKey->update([
            'key_code' => $request->key_code,
            'status' => 'sold',
            'error_message' => null,
        ]);

        return back()->with('success', "Đã giao key thủ công cho đơn #{$id}.");
    }

    // Admin bấm để hệ thống gọi API nhà cung cấp lấy key thật ngay cho riêng đơn này
    // (dùng khi muốn tự động hoá 1 đơn cụ thể thay vì nhập tay).
    public function fulfillViaApi($id)
    {
        $gameKey = GameKey::findOrFail($id);

        $newKeyString = app(\App\Services\WholesaleProviderService::class)->purchaseKey($gameKey->product_id);
        if (!$newKeyString) {
            return back()->with('error', "Không lấy được key qua API cho đơn #{$id}. Vui lòng thử lại hoặc nhập tay.");
        }

        $gameKey->update([
            'key_code' => $newKeyString,
            'status' => 'sold',
            'error_message' => null,
        ]);

        return back()->with('success', "Đã lấy key qua API thành công cho đơn #{$id}.");
    }
}
