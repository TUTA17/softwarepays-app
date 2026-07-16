<?php

namespace App\Modules\Shop\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\GameKey;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending_manual');
        $admin = $request->user();

        $query = GameKey::with('product', 'assignedAdmin')->whereNotNull('sold_to_user_id');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('sold_at')->paginate(30)->withQueryString();

        $userIds = $orders->pluck('sold_to_user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        $pendingCount = GameKey::where('status', 'pending_manual')->count();

        return response()->json([
            'orders' => $orders->through(function ($order) use ($users) {
                return $this->transform($order, $users[$order->sold_to_user_id] ?? null);
            }),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'pending_count' => $pendingCount,
            'can_manage_all' => $admin->hasPermission('orders.manage_all'),
        ]);
    }

    public function show($id)
    {
        $order = GameKey::with('product', 'assignedAdmin')->findOrFail($id);
        $buyer = $order->sold_to_user_id ? \App\Models\User::find($order->sold_to_user_id) : null;

        return response()->json($this->transform($order, $buyer));
    }

    private function transform(GameKey $order, $buyer): array
    {
        return [
            'id' => $order->id,
            'product_name' => $order->product->name ?? null,
            'buyer_name' => $buyer->name ?? null,
            'buyer_email' => $buyer->email ?? null,
            'status' => $order->status,
            'error_message' => $order->error_message,
            'key_code' => $order->key_code,
            'note' => $order->note,
            'sold_at' => $order->sold_at,
            'assigned_admin_id' => $order->assigned_admin_id,
            'assigned_admin_name' => $order->assignedAdmin->name ?? null,
        ];
    }

    public function claim(Request $request, $id)
    {
        $gameKey = GameKey::findOrFail($id);
        $admin = $request->user();

        if ($gameKey->assigned_admin_id && $gameKey->assigned_admin_id != $admin->id) {
            return response()->json(['success' => false, 'message' => 'Đơn này đã có người khác nhận xử lý.'], 422);
        }

        $gameKey->update(['assigned_admin_id' => $admin->id, 'claimed_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function release(Request $request, $id)
    {
        $admin = $request->user();
        if (!$admin->hasPermission('orders.manage_all')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền bỏ nhận đơn.'], 403);
        }

        $gameKey = GameKey::findOrFail($id);
        $gameKey->update(['assigned_admin_id' => null, 'claimed_at' => null]);

        return response()->json(['success' => true]);
    }

    private function assertCanProcess(GameKey $gameKey, $admin)
    {
        if ($gameKey->assigned_admin_id && $gameKey->assigned_admin_id != $admin->id && !$admin->hasPermission('orders.manage_all')) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn này đã được ' . ($gameKey->assignedAdmin->name ?? 'người khác') . ' nhận xử lý.',
            ], 422);
        }
        return null;
    }

    public function fulfillManual(Request $request, $id)
    {
        $request->validate([
            'key_code' => 'required|string|max:5000',
            'note' => 'nullable|string|max:5000',
        ]);

        $gameKey = GameKey::findOrFail($id);
        $admin = $request->user();

        if ($blocked = $this->assertCanProcess($gameKey, $admin)) {
            return $blocked;
        }

        $gameKey->update([
            'key_code' => $request->key_code,
            'status' => 'sold',
            'error_message' => null,
            'note' => $request->note,
            'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
            'claimed_at' => $gameKey->claimed_at ?? now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function fulfillViaApi(Request $request, $id)
    {
        $gameKey = GameKey::findOrFail($id);
        $admin = $request->user();

        if ($blocked = $this->assertCanProcess($gameKey, $admin)) {
            return $blocked;
        }

        $newKeyString = app(\App\Services\WholesaleProviderService::class)->purchaseKey($gameKey->product_id);
        if (!$newKeyString) {
            return response()->json(['success' => false, 'message' => 'Không lấy được key qua API. Vui lòng thử lại hoặc nhập tay.'], 422);
        }

        $gameKey->update([
            'key_code' => $newKeyString,
            'status' => 'sold',
            'error_message' => null,
            'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
            'claimed_at' => $gameKey->claimed_at ?? now(),
        ]);

        return response()->json(['success' => true]);
    }
}
