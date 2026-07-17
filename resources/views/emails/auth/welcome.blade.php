<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng bạn đến với SoftwarePays</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2563eb, #10b981); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .text { line-height: 1.6; color: #475569; font-size: 16px; }
        .box { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .box ul { margin: 0; padding-left: 20px; color: #334155; }
        .box li { margin-bottom: 8px; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chào mừng đến với SoftwarePays!</h1>
        </div>
        <div class="content">
            <p class="text">Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p class="text">Cảm ơn bạn đã tạo tài khoản tại SoftwarePays. Tài khoản của bạn đã sẵn sàng để sử dụng.</p>

            <div class="box">
                <ul>
                    <li>Nạp tiền vào ví qua MoMo, chuyển khoản, PayPal hoặc Crypto</li>
                    <li>Mua game bản quyền, phần mềm, thẻ quà tặng và nhiều dịch vụ khác</li>
                    <li>Nhận điểm thưởng cho mỗi đơn hàng</li>
                </ul>
            </div>

            <p class="text">Nếu bạn có bất kỳ câu hỏi nào, đội ngũ hỗ trợ của chúng tôi luôn sẵn sàng giúp đỡ.</p>

            <div style="text-align: center;">
                <a href="{{ url('/') }}" class="btn">Bắt đầu mua sắm</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SoftwarePays. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
