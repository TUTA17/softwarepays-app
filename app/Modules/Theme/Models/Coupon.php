<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'is_public',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot('status', 'used_at')
            ->withTimestamps();
    }

    public function isValid()
    {
        if (!$this->is_active) return false;
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        
        $now = now();
        if ($this->valid_from && $this->valid_from > $now) return false;
        if ($this->valid_until && $this->valid_until < $now) return false;
        
        return true;
    }

    public function calculateDiscount($total)
    {
        if ($this->discount_type === 'percent') {
            $discount = $total * ($this->discount_value / 100);
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }
            return $discount;
        }

        // fixed
        return min($this->discount_value, $total);
    }
}
