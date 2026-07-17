<?php

namespace App\Modules\Shop\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Smm\Models\SmmOrder;
use App\Modules\Theme\Models\GameKey;
use App\Services\SmmApi;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Quy đổi trạng thái riêng của nhà cung cấp SMM về đúng 4 nhóm trạng thái chung — khớp
    // App\Modules\Shop\Controllers\Admin\OrderController::normalizeSmmStatus() bên web, để 1 tab
    // lọc áp dụng cho MỌI loại đơn (Game/Giftcard/Thẻ nạp/VPN/eSIM/MXH), không tách tab riêng.
    private function normalizeSmmStatus(string $smmStatus): string
    {
        return \App\Modules\Shop\Controllers\Admin\OrderController::normalizeSmmStatus($smmStatus);
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending_manual');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;
        $admin = $request->user();
        $fulfillmentMode = Setting::getValue('order_fulfillment_mode', 'manual');

        $gameQuery = GameKey::with('product', 'assignedAdmin')->whereNotNull('sold_to_user_id');
        if ($status !== 'all') {
            $gameQuery->where('status', $status);
        }
        $gameOrders = $gameQuery->orderByDesc('sold_at')->get();

        $userIds = $gameOrders->pluck('sold_to_user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        $smmAll = SmmOrder::with('user')->orderByDesc('id')->get();
        $smmOrders = $status === 'all'
            ? $smmAll
            : $smmAll->filter(fn ($o) => $this->normalizeSmmStatus($o->status) === $status)->values();

        $rows = [];
        foreach ($gameOrders as $o) {
            $rows[] = ['time' => $o->sold_at, 'data' => $this->transform($o, $users[$o->sold_to_user_id] ?? null)];
        }
        foreach ($smmOrders as $o) {
            $rows[] = ['time' => $o->created_at, 'data' => $this->transformSmm($o)];
        }
        usort($rows, fn ($a, $b) => ($b['time'] ?? now()) <=> ($a['time'] ?? now()));

        $total = count($rows);
        $slice = array_map(fn ($r) => $r['data'], array_slice($rows, ($page - 1) * $perPage, $perPage));
        $lastPage = max(1, (int) ceil($total / $perPage));

        return response()->json([
            'orders' => ['data' => $slice],
            'current_page' => $page,
            'last_page' => $lastPage,
            'pending_count' => GameKey::where('status', 'pending_manual')->count(),
            'can_manage_all' => $admin->hasPermission('orders.manage_all'),
            'fulfillment_mode' => $fulfillmentMode,
        ]);
    }

    // Bật/tắt chế độ xử lý đơn (thủ công / tự động) — tương đương admin.orders.toggle_mode bên web,
    // cùng 1 setting nên đổi ở app hay web đều áp dụng chung ngay lập tức.
    public function toggleMode(Request $request)
    {
        $request->validate(['mode' => 'required|in:manual,auto']);

        Setting::setValue('order_fulfillment_mode', $request->mode);

        return response()->json(['success' => true, 'mode' => $request->mode]);
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
            'type' => 'game',
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

    private function transformSmm(SmmOrder $order): array
    {
        return [
            'type' => 'smm',
            'id' => $order->id,
            'product_name' => $order->service_name,
            'buyer_name' => $order->user->name ?? null,
            'buyer_email' => $order->user->email ?? null,
            'status' => $order->status,
            'quantity' => $order->quantity,
            'charge' => $order->charge,
            'link' => $order->link,
            'api_order_id' => $order->api_order_id,
            'sold_at' => $order->created_at,
        ];
    }

    // Đơn MXH tạo ở chế độ "Thủ công" chưa gọi API nhà cung cấp (api_order_id rỗng) — admin bấm
    // để thực sự đặt đơn qua API, tương đương admin.smm_orders.send_to_api bên web.
    public function sendSmmToApi($id)
    {
        $order = SmmOrder::findOrFail($id);

        if ($order->api_order_id) {
            return response()->json(['success' => false, 'message' => "Đơn #{$id} đã được gửi qua API rồi."], 422);
        }

        $result = app(SmmApi::class)->order([
            'service' => $order->service_id,
            'link' => $order->link,
            'quantity' => $order->quantity,
        ]);

        if (!$result || isset($result->error)) {
            return response()->json(['success' => false, 'message' => "Gửi đơn qua API thất bại: " . ($result->error ?? 'lỗi không xác định')], 422);
        }

        $order->update([
            'api_order_id' => $result->order ?? null,
            'status' => 'Pending',
        ]);

        return response()->json(['success' => true]);
    }

    // Admin bấm để lấy lại trạng thái mới nhất từ nhà cung cấp SMM cho 1 đơn (không có webhook
    // tự động nên phải chủ động hỏi lại) — tương đương admin.smm_orders.refresh bên web.
    public function refreshSmmStatus($id)
    {
        $order = SmmOrder::findOrFail($id);

        if (!$order->api_order_id) {
            return response()->json(['success' => false, 'message' => "Đơn #{$id} không có mã đơn từ nhà cung cấp để tra cứu."], 422);
        }

        $result = app(SmmApi::class)->status($order->api_order_id);
        // API trả {"status":"error","message":"..."} khi lỗi — không có field "error" riêng.
        if (!$result || ($result->status ?? null) === 'error') {
            return response()->json(['success' => false, 'message' => "Không lấy được trạng thái: " . ($result->message ?? 'lỗi không xác định')], 422);
        }

        $order->update(['status' => $result->status ?? $order->status]);

        return response()->json(['success' => true, 'status' => $order->status]);
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

    // Mỗi loại sản phẩm gọi đúng API đối tác của nó (Kinguin / Santhecao / VPN Panel / eSIM Access),
    // dùng ngữ cảnh (telco, mệnh giá, mã gói...) đã lưu sẵn trong delivery_data — khớp đúng logic
    // bên Admin\OrderController::fulfillViaApi() của web, trước đây bản API này chỉ hỗ trợ Wholesale
    // (Kinguin) nên bấm "Lấy key qua API" trên app cho đơn Card/VPN/eSIM sẽ luôn thất bại.
    public function fulfillViaApi(Request $request, $id)
    {
        $gameKey = GameKey::with('product')->findOrFail($id);
        $admin = $request->user();

        if ($blocked = $this->assertCanProcess($gameKey, $admin)) {
            return $blocked;
        }

        $productType = $gameKey->product->product_type ?? null;
        $context = $gameKey->delivery_data ?? [];

        switch ($productType) {
            case \App\Modules\Theme\Models\Product::TYPE_CARD:
                $telco = $context['retry_telco'] ?? null;
                $amount = $context['retry_amount'] ?? null;
                if (!$telco || !$amount) {
                    return response()->json(['success' => false, 'message' => "Đơn #{$id} thiếu thông tin nhà mạng/mệnh giá để gọi lại API. Vui lòng nhập tay."], 422);
                }

                $santhecao = app(\App\Services\SanthecaoService::class);
                $partnerRid = 'kg' . $gameKey->sold_to_user_id . '_' . time() . rand(10, 99);
                $result = $santhecao->buyCardCode($telco, (int) $amount, 1, $partnerRid);
                if ($santhecao->isPending($result)) {
                    $result = $santhecao->checkBuyCard($partnerRid, $result['trans_id'] ?? null);
                }

                if (!$result || (int) ($result['status'] ?? 0) !== 1) {
                    return response()->json(['success' => false, 'message' => "Gọi API Santhecao thất bại: " . ($result['desc'] ?? 'không xác định') . '. Vui lòng thử lại hoặc nhập tay.'], 422);
                }

                $cards = $result['cards'] ?? [];
                $pin = collect($cards)->pluck('code')->join("\n---\n") ?: null;
                $serial = collect($cards)->pluck('serial')->join("\n---\n") ?: null;

                $gameKey->update([
                    'key_code' => $pin ?: ('Mã giao dịch: ' . ($result['trans_id'] ?? '')),
                    'status' => 'sold',
                    'error_message' => null,
                    'delivery_data' => array_merge($context, ['pin_code' => $pin, 'serial' => $serial, 'trans_id' => $result['trans_id'] ?? null]),
                    'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
                    'claimed_at' => $gameKey->claimed_at ?? now(),
                ]);

                return response()->json(['success' => true]);

            case \App\Modules\Theme\Models\Product::TYPE_VPN:
                $vpnServerId = $context['retry_vpn_server_id'] ?? null;
                $packageKey = $context['retry_package_key'] ?? null;
                if (!$vpnServerId || !$packageKey) {
                    return response()->json(['success' => false, 'message' => "Đơn #{$id} thiếu thông tin gói VPN để gọi lại API. Vui lòng nhập tay."], 422);
                }

                $result = app(\App\Services\VpnPanelsService::class)->purchaseVpn($vpnServerId, $packageKey);
                if (!$result) {
                    return response()->json(['success' => false, 'message' => "Gọi API VPN thất bại. Vui lòng thử lại hoặc nhập tay."], 422);
                }

                $gameKey->update([
                    'key_code' => $result['subscription_link'] ?? $result['username'] ?? 'N/A',
                    'status' => 'sold',
                    'error_message' => null,
                    'delivery_data' => array_merge($context, $result),
                    'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
                    'claimed_at' => $gameKey->claimed_at ?? now(),
                ]);

                return response()->json(['success' => true]);

            case \App\Modules\Theme\Models\Product::TYPE_ESIM:
                $packageCode = $context['retry_package_code'] ?? null;
                if (!$packageCode) {
                    return response()->json(['success' => false, 'message' => "Đơn #{$id} thiếu mã gói eSIM để gọi lại API. Vui lòng nhập tay."], 422);
                }

                $orderNo = app(\App\Services\EsimAccessService::class)->purchaseEsim($gameKey->id, $packageCode, 1);
                if (!$orderNo) {
                    return response()->json(['success' => false, 'message' => "Gọi API eSIM thất bại. Vui lòng thử lại hoặc nhập tay."], 422);
                }

                $gameKey->update([
                    'status' => 'processing',
                    'error_message' => null,
                    'delivery_data' => array_merge($context, ['order_no' => $orderNo]),
                    'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
                    'claimed_at' => $gameKey->claimed_at ?? now(),
                ]);
                \App\Jobs\PollEsimStatus::dispatch($gameKey->id)->delay(now()->addSeconds(8));

                return response()->json(['success' => true]);

            default:
                $newKeyString = app(\App\Services\WholesaleProviderService::class)->purchaseKey($gameKey->product_id);
                if (!$newKeyString) {
                    return response()->json(['success' => false, 'message' => "Không lấy được key qua API. Vui lòng thử lại hoặc nhập tay."], 422);
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
}
