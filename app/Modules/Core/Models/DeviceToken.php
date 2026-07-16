<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = [
        'admin_id',
        'fcm_token',
        'token_hash',
        'platform',
        'user_agent',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
