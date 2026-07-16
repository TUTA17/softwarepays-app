<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Modules\Theme\Models\Coupon;
use App\Modules\Theme\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CouponNotification;

class SendCouponEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $coupon;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function handle()
    {
        // Chunk users to prevent memory exhaustion
        User::chunk(500, function ($users) {
            foreach ($users as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new CouponNotification($this->coupon));
                }
            }
        });
    }
}
