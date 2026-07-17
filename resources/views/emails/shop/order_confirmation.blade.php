<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background-color: #10b981; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .text { line-height: 1.6; color: #475569; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { text-align: left; font-size: 13px; color: #94a3b8; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 8px 0; }
        table td { padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 15px; }
        .total-row td { font-weight: bold; font-size: 17px; color: #0f172a; border-bottom: none; padding-top: 16px; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Đơn hàng đã được xác nhận</h1>
        </div>
        <div class="content">
            <p class="text">Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p class="text">Cảm ơn bạn đã mua hàng tại SoftwarePays. Đơn hàng của bạn đã được xử lý thành công:</p>

            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="text-align: right;">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td style="text-align: right;">{{ number_format($item['price']) }}đ</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td>Tổng cộng</td>
                        <td style="text-align: right;">{{ number_format($total) }}đ</td>
                    </tr>
                </tbody>
            </table>

            <p class="text">Bạn có thể xem chi tiết và Key sản phẩm trong mục "Kho Game Của Tôi" trên tài khoản của bạn.</p>

            <div style="text-align: center;">
                <a href="{{ route('dashboard') }}" class="btn">Xem đơn hàng</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SoftwarePays. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
