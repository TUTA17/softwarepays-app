<?php

namespace App\Modules\GifMeme\Models;

use App\Modules\Core\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gif extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';
    const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'tags',
        'object_key', 'thumbnail_key', 'public_url',
        'original_filename', 'mime_type', 'extension',
        'width', 'height', 'file_size',
        'status', 'is_featured', 'play_count', 'download_count',
        'like_count', 'share_count',
        'created_by'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'duration' => 'integer',
        'bitrate' => 'integer',
        'file_size' => 'integer',
        'play_count' => 'integer',
        'download_count' => 'integer',
    ];

    // Số lượt nghe/thích/tải khởi điểm luôn random 10k-300k (không tròn) để tránh cảm giác
    // "mới toanh, 0 lượt" khi khách vào xem — lượt thật (increment() ở GifController) cộng dồn
    // tiếp lên trên nền random này như bình thường.
    protected static function booted(): void
    {
        static::creating(function (Gif $Gif) {
            if (!$Gif->play_count) {
                $Gif->play_count = random_int(10_000, 300_000);
            }
            if (!$Gif->like_count) {
                $Gif->like_count = random_int(10_000, 300_000);
            }
            if (!$Gif->download_count) {
                $Gif->download_count = random_int(10_000, 300_000);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(GifCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getTagsArrayAttribute(): array
    {
        if (!$this->tags) return [];
        return array_values(array_filter(array_map('trim', explode(',', $this->tags))));
    }

    // Sinh slug duy nhất từ tiêu đề — Blog module không chống trùng nên tự thêm hậu tố -2, -3...
    // ở đây để tránh lỗi ràng buộc unique khi 2 Gif trùng tên.
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'Gif';
        $slug = $base;
        $i = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}


