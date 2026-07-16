<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảnh báo Đăng nhập lạ</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background-color: #ef4444; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .info-box { background-color: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-box ul { list-style: none; padding: 0; margin: 0; }
        .info-box li { margin-bottom: 10px; font-size: 15px; }
        .text { line-height: 1.6; color: #475569; font-size: 16px; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cảnh báo Đăng nhập lạ</h1>
        </div>
        <div class="content">
            <p class="text">Xin chào,</p>
            <p class="text">Hệ thống ghi nhận tài khoản của bạn vừa được đăng nhập từ một địa chỉ IP lạ. Dưới đây là thông tin chi tiết:</p>
            
            <div class="info-box">
                <ul>
                    <li><strong>IP:</strong> {{ $ip }}</li>
                    <li><strong>Trình duyệt/Thiết bị:</strong> {{ $userAgent }}</li>
                    <li><strong>Thời gian:</strong> {{ $time }}</li>
                </ul>
            </div>
            
            <p class="text">Nếu đây là bạn, vui lòng bỏ qua email này.</p>
            <p class="text" style="color: #ef4444; font-weight: bold;">Nếu KHÔNG phải bạn, tài khoản của bạn có thể đang gặp nguy hiểm. Vui lòng đổi mật khẩu ngay lập tức!</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/forgot-password') }}" class="btn">Đổi Mật Khẩu Ngay</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SoftwarePays. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
