<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác thực đổi mật khẩu</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #2563eb;
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .otp-box {
            background-color: #f1f5f9;
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            padding: 20px;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 10px;
            color: #0f172a;
            margin: 30px 0;
        }
        .text {
            line-height: 1.6;
            color: #475569;
            font-size: 16px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            border-top: 1px solid #e2e8f0;
        }
        .warning {
            color: #ef4444;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Yêu cầu khôi phục mật khẩu</h1>
        </div>
        
        <div class="content">
            <p class="text">Xin chào,</p>
            <p class="text">Chúng tôi nhận được yêu cầu đổi mật khẩu cho tài khoản liên kết với email này. Dưới đây là mã xác thực OTP của bạn:</p>
            
            <div class="otp-box">
                {{ $otp }}
            </div>
            
            <p class="text">Mã này có hiệu lực trong vòng <strong>10 phút</strong>.</p>
            
            <p class="warning">Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này hoặc liên hệ với bộ phận hỗ trợ ngay lập tức.</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} SoftwarePays. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
