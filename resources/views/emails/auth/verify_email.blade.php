<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh địa chỉ email</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background-color: #2563eb; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .text { line-height: 1.6; color: #475569; font-size: 16px; }
        .otp-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
        .otp-box span { font-family: monospace; font-size: 32px; font-weight: bold; color: #2563eb; letter-spacing: 5px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác minh địa chỉ email</h1>
        </div>
        <div class="content">
            <p class="text">Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p class="text">Vui lòng sử dụng mã xác minh gồm 6 chữ số dưới đây để xác minh địa chỉ email của bạn:</p>

            <div class="otp-box">
                <span>{{ $otp }}</span>
            </div>

            <p class="text" style="color: #64748b; font-size: 14px;">Mã này sẽ hết hạn sau <strong>15 phút</strong>. Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SoftwarePays. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
