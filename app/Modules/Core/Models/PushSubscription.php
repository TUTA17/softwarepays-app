<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = [
        'admin_id',
        'endpoint',
        'endpoint_hash',
        'p256dh',
        'auth',
        'user_agent',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
