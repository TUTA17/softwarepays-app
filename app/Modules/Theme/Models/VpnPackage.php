<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class VpnPackage extends Model
{
    protected $fillable = [
        'product_id', 'package_key', 'name', 'months', 'gig', 'price', 'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
