<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Modules\Theme\Models\Coupon;

class CouponNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $coupon;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function build()
    {
        return $this->subject('🎉 Nhận Ngay Mã Giảm Giá Mới: ' . $this->coupon->code)
                    ->view('emails.coupon_notification');
    }
}
