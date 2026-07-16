<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: #0284c7; color: #ffffff; text-align: center; padding: 30px 20px; }
        .content { padding: 30px; text-align: center; color: #334155; }
        .coupon-box { display: inline-block; background: #f0f9ff; border: 2px dashed #38bdf8; color: #0284c7; font-size: 24px; font-weight: bold; padding: 15px 30px; border-radius: 8px; margin: 20px 0; letter-spacing: 2px; }
        .footer { background: #f8fafc; text-align: center; padding: 20px; font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; background: #0284c7; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">🎁 Mã Giảm Giá Mới Dành Cho Bạn!</h1>
        </div>
        <div class="content">
            <p style="font-size: 16px; line-height: 1.5;">Chào bạn,</p>
            <p style="font-size: 16px; line-height: 1.5;">Chúng tôi vừa tung ra một mã giảm giá cực hot. Nhanh tay sử dụng trước khi hết hạn nhé!</p>
            
            <div class="coupon-box">{{ $coupon->code }}</div>
            
            <p style="font-size: 18px; font-weight: bold; color: #0f172a; margin-top: 10px;">
                @if($coupon->discount_type == 'fixed')
                    Giảm ngay {{ number_format($coupon->discount_value) }} VNĐ
                @else
                    Giảm ngay {{ $coupon->discount_value }}%
                    @if($coupon->max_discount_amount)
                        (Tối đa {{ number_format($coupon->max_discount_amount) }} VNĐ)
                    @endif
                @endif
            </p>
            
            <p style="font-size: 14px; margin-bottom: 5px;"><strong>Mô tả:</strong> {{ $coupon->description }}</p>
            @if($coupon->min_order_amount > 0)
                <p style="font-size: 14px; margin-bottom: 5px; color: #64748b;">Áp dụng cho đơn hàng từ {{ number_format($coupon->min_order_amount) }} VNĐ</p>
            @endif
            @if($coupon->valid_until)
                <p style="font-size: 14px; margin-bottom: 5px; color: #ef4444;">Hạn sử dụng: {{ $coupon->valid_until->format('d/m/Y H:i') }}</p>
            @endif
            
            <a href="{{ url('/') }}" class="btn">MUA SẮM NGAY</a>
        </div>
        <div class="footer">
            Email này được gửi tự động từ hệ thống. Vui lòng không trả lời email này.<br>
            Cảm ơn bạn đã tin tưởng và đồng hành cùng chúng tôi!
        </div>
    </div>
</body>
</html>
