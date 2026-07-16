<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\GameKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending_manual');

        $query = GameKey::with('product', 'assignedAdmin')->whereNotNull('sold_to_user_id');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('sold_at')->paginate(30)->withQueryString();

        $userIds = $orders->pluck('sold_to_user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        $pendingCount = GameKey::where('status', 'pending_manual')->count();
        $fulfillmentMode = Setting::getValue('order_fulfillment_mode', 'manual');

        $currentAdmin = Auth::guard('admin')->user();
        $canManageAll = $currentAdmin->hasPermission('orders.manage_all');

        return view('shop::admin.orders', compact('orders', 'users', 'status', 'pendingCount', 'fulfillmentMode', 'currentAdmin', 'canManageAll'));
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

    // Nhân viên bấm "Nhận đơn" — khoá đơn này lại để chỉ mình họ xử lý (trừ khi người khác có quyền orders.manage_all).
    public function claim($id)
    {
        $gameKey = GameKey::findOrFail($id);
        $admin = Auth::guard('admin')->user();

        if ($gameKey->assigned_admin_id && $gameKey->assigned_admin_id != $admin->id) {
            return back()->with('error', 'Đơn này đã có người khác nhận xử lý.');
        }

        $gameKey->update(['assigned_admin_id' => $admin->id, 'claimed_at' => now()]);

        return back()->with('success', "Bạn đã nhận xử lý đơn #{$id}.");
    }

    // Gỡ khoá 1 đơn đang bị nhận (chỉ dành cho admin có quyền orders.manage_all — dùng khi nhân viên nghỉ/đơn bị kẹt).
    public function release($id)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin->hasPermission('orders.manage_all')) {
            abort(403);
        }

        $gameKey = GameKey::findOrFail($id);
        $gameKey->update(['assigned_admin_id' => null, 'claimed_at' => null]);

        return back()->with('success', "Đã bỏ nhận đơn #{$id}.");
    }

    // Kiểm tra khoá "nhận đơn": chặn nếu đơn đã bị người khác nhận và admin hiện tại không có quyền orders.manage_all.
    private function assertCanProcess(GameKey $gameKey, $admin): void
    {
        if ($gameKey->assigned_admin_id && $gameKey->assigned_admin_id != $admin->id && !$admin->hasPermission('orders.manage_all')) {
            abort(403, 'Đơn này đã được ' . ($gameKey->assignedAdmin->name ?? 'người khác') . ' nhận xử lý.');
        }
    }

    // Admin nhập tay key/nội dung đã tự mua ở nơi khác, đánh dấu đơn hoàn tất.
    public function fulfillManual(Request $request, $id)
    {
        $request->validate([
            'key_code' => 'required|string|max:5000',
            'note' => 'nullable|string|max:5000',
        ]);

        $gameKey = GameKey::findOrFail($id);
        $admin = Auth::guard('admin')->user();
        $this->assertCanProcess($gameKey, $admin);

        $gameKey->update([
            'key_code' => $request->key_code,
            'status' => 'sold',
            'error_message' => null,
            'note' => $request->note,
            'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
            'claimed_at' => $gameKey->claimed_at ?? now(),
        ]);

        return back()->with('success', "Đã giao key thủ công cho đơn #{$id}.");
    }

    // Admin bấm để hệ thống gọi API nhà cung cấp lấy key thật ngay cho riêng đơn này
    // (dùng khi muốn tự động hoá 1 đơn cụ thể thay vì nhập tay).
    public function fulfillViaApi($id)
    {
        $gameKey = GameKey::findOrFail($id);
        $admin = Auth::guard('admin')->user();
        $this->assertCanProcess($gameKey, $admin);

        $newKeyString = app(\App\Services\WholesaleProviderService::class)->purchaseKey($gameKey->product_id);
        if (!$newKeyString) {
            return back()->with('error', "Không lấy được key qua API cho đơn #{$id}. Vui lòng thử lại hoặc nhập tay.");
        }

        $gameKey->update([
            'key_code' => $newKeyString,
            'status' => 'sold',
            'error_message' => null,
            'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
            'claimed_at' => $gameKey->claimed_at ?? now(),
        ]);

        return back()->with('success', "Đã lấy key qua API thành công cho đơn #{$id}.");
    }
}
