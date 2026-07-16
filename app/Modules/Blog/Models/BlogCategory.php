<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'order',
    ];

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
