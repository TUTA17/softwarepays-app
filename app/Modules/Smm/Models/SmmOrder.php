<?php

namespace App\Modules\Smm\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SmmOrder extends Model
{
    protected $table = 'smm_orders';

    protected $fillable = [
        'user_id',
        'service_id',
        'service_name',
        'link',
        'quantity',
        'charge',
        'api_order_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
