<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
