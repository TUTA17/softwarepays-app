<?php

namespace App\Modules\GifMeme\Models;

use Illuminate\Database\Eloquent\Model;

class GifCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'status', 'order'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function Gifs()
    {
        return $this->hasMany(Gif::class, 'category_id');
    }
}


