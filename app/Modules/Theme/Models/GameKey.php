<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class GameKey extends Model
{
    // Trạng thái bổ sung cho các loại giao hàng bất đồng bộ (VPN/eSIM/Thẻ):
    // 'processing' (đang chờ nhà cung cấp xử lý), 'failed' (lỗi giao hàng).
    // 'available'/'sold' vẫn giữ nguyên ý nghĩa cũ cho key đơn (game/giftcard/subscription/software).
    protected $fillable = [
        'product_id',
        'key_code',
        'status',
        'delivery_data',
        'error_message',
        'sold_to_user_id',
        'sold_at',
        'assigned_admin_id',
        'claimed_at',
        'note',
    ];

    protected $casts = [
        'delivery_data' => 'array',
        'sold_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(\App\Modules\Core\Models\Admin::class, 'assigned_admin_id');
    }
}
