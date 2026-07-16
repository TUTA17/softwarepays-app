<?php

namespace App\Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;

class GameKey extends Model
{
    protected $fillable = [
        'product_id', 
        'key_code', 
        'status', 
        'sold_to_user_id', 
        'sold_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
