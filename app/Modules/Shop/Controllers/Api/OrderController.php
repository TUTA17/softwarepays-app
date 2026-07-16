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

        $query = GameKey::with('product')->whereNotNull('sold_to_user_id');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('sold_at')->paginate(30)->withQueryString();

        $userIds = $orders->pluck('sold_to_user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        $pendingCount = GameKey::where('status', 'pending_manual')->count();

        return response()->json([
            'orders' => $orders->through(function ($order) use ($users) {
                $buyer = $users[$order->sold_to_user_id] ?? null;

                return [
                    'id' => $order->id,
                    'product_name' => $order->product->name ?? null,
                    'buyer_name' => $buyer->name ?? null,
                    'buyer_email' => $buyer->email ?? null,
                    'status' => $order->status,
                    'error_message' => $order->error_message,
                    'key_code' => $order->key_code,
                    'sold_at' => $order->sold_at,
                ];
            }),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'pending_count' => $pendingCount,
        ]);
    }

    public function show($id)
    {
        $order = GameKey::with('product')->findOrFail($id);
        $buyer = $order->sold_to_user_id ? \App\Models\User::find($order->sold_to_user_id) : null;

        return response()->json([
            'id' => $order->id,
            'product_name' => $order->product->name ?? null,
            'buyer_name' => $buyer->name ?? null,
            'buyer_email' => $buyer->email ?? null,
            'status' => $order->status,
            'error_message' => $order->error_message,
            'key_code' => $order->key_code,
            'sold_at' => $order->sold_at,
        ]);
    }

    public function fulfillManual(Request $request, $id)
    {
        $request->validate(['key_code' => 'required|string|max:5000']);

        $gameKey = GameKey::findOrFail($id);
        $gameKey->update([
            'key_code' => $request->key_code,
            'status' => 'sold',
            'error_message' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function fulfillViaApi($id)
    {
        $gameKey = GameKey::findOrFail($id);

        $newKeyString = app(\App\Services\WholesaleProviderService::class)->purchaseKey($gameKey->product_id);
        if (!$newKeyString) {
            return response()->json(['success' => false, 'message' => 'Không lấy được key qua API. Vui lòng thử lại hoặc nhập tay.'], 422);
        }

        $gameKey->update([
            'key_code' => $newKeyString,
            'status' => 'sold',
            'error_message' => null,
        ]);

        return response()->json(['success' => true]);
    }
}
