<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận thanh toán SoftwarePays</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-w: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1e293b; margin: 0;">SoftwarePays</h1>
        </div>
        
        <h2 style="color: #334155; margin-bottom: 20px;">Mã xác nhận thanh toán</h2>
        
        <p style="color: #475569; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
            Chào bạn,<br><br>
            Bạn đang yêu cầu thanh toán đơn hàng trị giá <strong>{{ number_format($amount) }}đ</strong> trên hệ thống SoftwarePays.<br>
            Để hoàn tất việc thanh toán, vui lòng sử dụng mã xác nhận gồm 6 chữ số dưới đây:
        </p>

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 30px;">
            <span style="font-family: monospace; font-size: 32px; font-weight: bold; color: #2563eb; letter-spacing: 5px;">{{ $otp }}</span>
        </div>

        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 30px;">
            Mã này sẽ hết hạn sau <strong>5 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.
        </p>

        <p style="color: #ef4444; font-size: 14px; line-height: 1.5;">
            Nếu bạn không thực hiện giao dịch này, vui lòng đổi mật khẩu tài khoản ngay lập tức vì có thể tài khoản của bạn đang bị sử dụng trái phép.
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <div style="text-align: center; color: #94a3b8; font-size: 12px;">
            <p>&copy; {{ date('Y') }} SoftwarePays. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
