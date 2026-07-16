<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'image',
        'source',
        'author',
        'is_auto',
        'is_featured',
        'status',
        'views',
        'pub_date',
    ];

    protected $casts = [
        'pub_date' => 'datetime',
        'is_auto' => 'boolean',
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}
