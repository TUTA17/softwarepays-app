<?php

namespace App\Modules\SoundMeme\Models;

use Illuminate\Database\Eloquent\Model;

class SoundCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'status', 'order'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function sounds()
    {
        return $this->hasMany(Sound::class, 'category_id');
    }
}
