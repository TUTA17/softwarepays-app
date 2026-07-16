<?php

namespace App\Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'status',
        'reference_id'
    ];
}
