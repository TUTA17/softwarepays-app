<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class EsimPackage extends Model
{
    protected $fillable = [
        'product_id', 'package_code', 'name', 'data_volume_bytes', 'duration',
        'duration_unit', 'location', 'price', 'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
