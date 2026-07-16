<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCoupon extends Pivot
{
    protected $table = 'user_coupons';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'status',
        'used_at',
    ];
    
    protected $casts = [
        'used_at' => 'datetime',
    ];
}
