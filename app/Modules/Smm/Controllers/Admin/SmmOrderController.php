<?php

namespace App\Modules\Smm\Controllers\Admin;

use App\Modules\Smm\Models\SmmOrder;
use App\Services\SmmApi;
use Illuminate\Routing\Controller;

class SmmOrderController extends Controller
{
    // Admin bấm khi chế độ xử lý đơn là "Thủ công" — đơn MXH tạo ra ở trạng thái "Chờ duyệt"
    // (chưa gọi API nhà cung cấp), bấm nút này mới thực sự đặt đơn qua API sau khi admin xem qua.
    public function sendToApi($id)
    {
        $order = SmmOrder::findOrFail($id);

        if ($order->api_order_id) {
            return back()->with('error', "Đơn #{$id} đã được gửi qua API rồi.");
        }

        $result = app(SmmApi::class)->order([
            'service' => $order->service_id,
            'link' => $order->link,
            'quantity' => $order->quantity,
        ]);

        if (!$result || isset($result->error)) {
            return back()->with('error', "Gửi đơn qua API thất bại: " . ($result->error ?? 'lỗi không xác định') . '. Vui lòng thử lại.');
        }

        $order->update([
            'api_order_id' => $result->order ?? null,
            'status' => 'Pending',
        ]);

        return back()->with('success', "Đã gửi đơn #{$id} qua API nhà cung cấp thành công.");
    }

    // Admin bấm để lấy lại trạng thái mới nhất từ nhà cung cấp SMM cho 1 đơn cụ thể
    // (nhà cung cấp tự xử lý đơn ở phía họ, không có webhook nên cần chủ động hỏi lại).
    // Danh sách đơn MXH hiển thị gộp chung trong trang Đơn Hàng, xem OrderController::index().
    public function refreshStatus($id)
    {
        $order = SmmOrder::findOrFail($id);

        if (!$order->api_order_id) {
            return back()->with('error', "Đơn #{$id} không có mã đơn từ nhà cung cấp để tra cứu.");
        }

        $result = app(SmmApi::class)->status($order->api_order_id);

        // API trả về {"status":"error","message":"..."} khi lỗi (vd bị giới hạn tốc độ gọi) —
        // KHÔNG có field "error" riêng như code cũ kiểm tra, nên lỗi bị nuốt mất và code cũ lỡ
        // ghi đè thẳng trạng thái đơn thành chữ "error" thay vì báo lỗi cho admin biết.
        if (!$result || ($result->status ?? null) === 'error') {
            return back()->with('error', "Không lấy được trạng thái đơn #{$id}: " . ($result->message ?? 'lỗi không xác định'));
        }

        $order->update(['status' => $result->status ?? $order->status]);

        return back()->with('success', "Đã cập nhật trạng thái đơn #{$id}: " . ($result->status ?? $order->status));
    }
}
