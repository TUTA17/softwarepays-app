<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận giao dịch nạp tiền</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background-color: #10b981; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .text { line-height: 1.6; color: #475569; font-size: 16px; }
        .info-box { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-box ul { list-style: none; padding: 0; margin: 0; }
        .info-box li { margin-bottom: 10px; font-size: 15px; display: flex; justify-content: space-between; }
        .amount { font-size: 28px; font-weight: bold; color: #10b981; text-align: center; margin: 10px 0; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nạp tiền thành công</h1>
        </div>
        <div class="content">
            <p class="text">Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p class="text">Giao dịch nạp tiền vào ví của bạn đã được xác nhận thành công.</p>

            @if(($transaction->currency ?? 'VND') === 'USD')
                <div class="amount">+${{ number_format($transaction->amount, 2) }}</div>
            @else
                <div class="amount">+{{ number_format($transaction->amount) }}đ</div>
            @endif

            <div class="info-box">
                <ul>
                    <li><span>Mã giao dịch</span><strong>{{ $transaction->reference_id }}</strong></li>
                    <li><span>Thời gian</span><strong>{{ $transaction->updated_at->format('H:i d/m/Y') }}</strong></li>
                    @if(($transaction->currency ?? 'VND') === 'USD')
                        <li><span>Số dư ví USD hiện tại</span><strong>${{ number_format($user->balance_usd, 2) }}</strong></li>
                    @else
                        <li><span>Số dư ví VNĐ hiện tại</span><strong>{{ number_format($user->balance) }}đ</strong></li>
                    @endif
                </ul>
            </div>

            <p class="text">Nếu bạn không thực hiện giao dịch này, vui lòng liên hệ đội ngũ hỗ trợ ngay lập tức.</p>

            <div style="text-align: center;">
                <a href="{{ route('wallet.show') }}" class="btn">Xem ví của tôi</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SoftwarePays. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
