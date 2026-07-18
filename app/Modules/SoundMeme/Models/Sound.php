<?php

namespace App\Modules\SoundMeme\Models;

use App\Modules\Core\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sound extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';
    const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'tags',
        'object_key', 'thumbnail_key', 'waveform_key', 'public_url',
        'original_filename', 'mime_type', 'extension',
        'duration', 'bitrate', 'codec', 'file_size',
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

    // Số lượt nghe/thích/tải khởi điểm luôn random trên 1 triệu (không tròn) để tránh cảm giác
    // "mới toanh, 0 lượt" khi khách vào xem — lượt thật (increment() ở SoundController) cộng dồn
    // tiếp lên trên nền random này như bình thường.
    protected static function booted(): void
    {
        static::creating(function (Sound $sound) {
            if (!$sound->play_count) {
                $sound->play_count = random_int(1_000_000, 3_000_000);
            }
            if (!$sound->like_count) {
                $sound->like_count = random_int(1_000_000, 3_000_000);
            }
            if (!$sound->download_count) {
                $sound->download_count = random_int(1_000_000, 3_000_000);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(SoundCategory::class, 'category_id');
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
    // ở đây để tránh lỗi ràng buộc unique khi 2 sound trùng tên.
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'sound';
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
