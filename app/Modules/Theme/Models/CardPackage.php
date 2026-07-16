<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class CardPackage extends Model
{
    protected $fillable = ['product_id', 'face_value', 'discount_percent', 'price', 'original_price', 'promo_discount_percent', 'is_active'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
