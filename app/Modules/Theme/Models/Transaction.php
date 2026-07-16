<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'status',
        'reference_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
