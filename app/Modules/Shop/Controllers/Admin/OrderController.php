<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Setting;
use App\Modules\Smm\Models\SmmOrder;
use App\Modules\Theme\Models\GameKey;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Quy đổi trạng thái riêng của nhà cung cấp SMM (Pending/In progress/Completed/Partial/Canceled...)
    // về đúng 4 nhóm trạng thái chung của toàn bộ Đơn Hàng, để 1 tab lọc áp dụng cho MỌI loại đơn
    // (Game/Giftcard/Thẻ nạp/VPN/eSIM/MXH) thay vì phải có thêm 1 tab "MXH" riêng biệt.
    public static function normalizeSmmStatus(string $smmStatus): string
    {
        $s = strtolower($smmStatus);
        if (str_contains($s, 'cancel') || str_contains($s, 'fail') || str_contains($s, 'refund')) return 'failed';
        if (str_contains($s, 'complet') || str_contains($s, 'partial')) return 'sold';
        if (str_contains($s, 'progress') || str_contains($s, 'process')) return 'processing';
        return 'pending_manual';
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending_manual');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;

        $pendingCount = GameKey::where('status', 'pending_manual')->count();
        $fulfillmentMode = Setting::getValue('order_fulfillment_mode', 'manual');

        $currentAdmin = Auth::guard('admin')->user();
        $canManageAll = $currentAdmin->hasPermission('orders.manage_all');

        // 1) Đơn Game/Giftcard/Thẻ nạp/VPN/eSIM (GameKey)
        $gameQuery = GameKey::with('product', 'assignedAdmin')->whereNotNull('sold_to_user_id');
        if ($status !== 'all') {
            $gameQuery->where('status', $status);
        }
        $gameOrders = $gameQuery->orderByDesc('sold_at')->get();

        $userIds = $gameOrders->pluck('sold_to_user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        // 2) Đơn MXH (SmmOrder) — lọc theo trạng thái đã quy đổi về cùng nhóm với GameKey ở trên,
        // để gộp chung thật sự vào 1 danh sách duy nhất thay vì tách tab riêng.
        $smmAll = SmmOrder::with('user')->orderByDesc('id')->get();
        $smmOrders = $status === 'all'
            ? $smmAll
            : $smmAll->filter(fn ($o) => self::normalizeSmmStatus($o->status) === $status)->values();

        // 3) Gộp cả 2 nguồn thành 1 danh sách chung, sắp xếp theo thời gian, rồi tự phân trang tay
        // (không thể paginate() thẳng qua SQL vì khác bảng/khác schema).
        $rows = [];
        foreach ($gameOrders as $o) {
            $rows[] = ['type' => 'game', 'time' => $o->sold_at, 'model' => $o, 'buyer' => $users[$o->sold_to_user_id] ?? null];
        }
        foreach ($smmOrders as $o) {
            $rows[] = ['type' => 'smm', 'time' => $o->created_at, 'model' => $o, 'buyer' => null];
        }
        usort($rows, fn ($a, $b) => ($b['time'] ?? now()) <=> ($a['time'] ?? now()));

        $total = count($rows);
        $slice = array_slice($rows, ($page - 1) * $perPage, $perPage);

        $orders = new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('shop::admin.orders', compact('orders', 'status', 'pendingCount', 'fulfillmentMode', 'currentAdmin', 'canManageAll'));
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
    // (dùng khi muốn tự động hoá 1 đơn cụ thể thay vì nhập tay). Mỗi loại sản phẩm gọi
    // đúng API đối tác của nó (Kinguin / Santhecao / VPN Panel / eSIM Access), dùng ngữ cảnh
    // (telco, mệnh giá, mã gói...) đã lưu sẵn trong delivery_data lúc tạo đơn "chờ xử lý".
    public function fulfillViaApi($id)
    {
        $gameKey = GameKey::with('product')->findOrFail($id);
        $admin = Auth::guard('admin')->user();
        $this->assertCanProcess($gameKey, $admin);

        $productType = $gameKey->product->product_type ?? null;
        $context = $gameKey->delivery_data ?? [];

        switch ($productType) {
            case \App\Modules\Theme\Models\Product::TYPE_CARD:
                $telco = $context['retry_telco'] ?? null;
                $amount = $context['retry_amount'] ?? null;
                if (!$telco || !$amount) {
                    return back()->with('error', "Đơn #{$id} thiếu thông tin nhà mạng/mệnh giá để gọi lại API. Vui lòng nhập tay.");
                }

                $santhecao = app(\App\Services\SanthecaoService::class);
                $partnerRid = 'kg' . $gameKey->sold_to_user_id . '_' . time() . rand(10, 99);
                $result = $santhecao->buyCardCode($telco, (int) $amount, 1, $partnerRid);
                if ($santhecao->isPending($result)) {
                    $result = $santhecao->checkBuyCard($partnerRid, $result['trans_id'] ?? null);
                }

                if (!$result || (int) ($result['status'] ?? 0) !== 1) {
                    return back()->with('error', "Gọi API Santhecao thất bại cho đơn #{$id}: " . ($result['desc'] ?? 'không xác định') . '. Vui lòng thử lại hoặc nhập tay.');
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

                return back()->with('success', "Đã lấy thẻ qua API Santhecao thành công cho đơn #{$id}.");

            case \App\Modules\Theme\Models\Product::TYPE_VPN:
                $vpnServerId = $context['retry_vpn_server_id'] ?? null;
                $packageKey = $context['retry_package_key'] ?? null;
                if (!$vpnServerId || !$packageKey) {
                    return back()->with('error', "Đơn #{$id} thiếu thông tin gói VPN để gọi lại API. Vui lòng nhập tay.");
                }

                $result = app(\App\Services\VpnPanelsService::class)->purchaseVpn($vpnServerId, $packageKey);
                if (!$result) {
                    return back()->with('error', "Gọi API VPN thất bại cho đơn #{$id}. Vui lòng thử lại hoặc nhập tay.");
                }

                $gameKey->update([
                    'key_code' => $result['subscription_link'] ?? $result['username'] ?? 'N/A',
                    'status' => 'sold',
                    'error_message' => null,
                    'delivery_data' => array_merge($context, $result),
                    'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
                    'claimed_at' => $gameKey->claimed_at ?? now(),
                ]);

                return back()->with('success', "Đã lấy VPN qua API thành công cho đơn #{$id}.");

            case \App\Modules\Theme\Models\Product::TYPE_ESIM:
                $packageCode = $context['retry_package_code'] ?? null;
                if (!$packageCode) {
                    return back()->with('error', "Đơn #{$id} thiếu mã gói eSIM để gọi lại API. Vui lòng nhập tay.");
                }

                $orderNo = app(\App\Services\EsimAccessService::class)->purchaseEsim($gameKey->id, $packageCode, 1);
                if (!$orderNo) {
                    return back()->with('error', "Gọi API eSIM thất bại cho đơn #{$id}. Vui lòng thử lại hoặc nhập tay.");
                }

                $gameKey->update([
                    'status' => 'processing',
                    'error_message' => null,
                    'delivery_data' => array_merge($context, ['order_no' => $orderNo]),
                    'assigned_admin_id' => $gameKey->assigned_admin_id ?? $admin->id,
                    'claimed_at' => $gameKey->claimed_at ?? now(),
                ]);
                \App\Jobs\PollEsimStatus::dispatch($gameKey->id)->delay(now()->addSeconds(8));

                return back()->with('success', "Đã gửi lại yêu cầu eSIM qua API cho đơn #{$id}, đang chờ nhà cung cấp xác nhận.");

            default:
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
}