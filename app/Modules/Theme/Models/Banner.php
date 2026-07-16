<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['image', 'image_intl', 'show_vi_image', 'link_url', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'show_vi_image' => 'boolean',
    ];

    // 'image' = ảnh riêng cho Tiếng Việt, 'image_intl' = ảnh dùng chung cho các ngôn ngữ còn lại.
    // show_vi_image mặc định tắt (false) — khi tắt, khách Tiếng Việt cũng thấy ảnh "ngôn ngữ khác"
    // luôn (chỉ 1 ảnh cần quản lý cho tới khi Admin có ảnh riêng cho Tiếng Việt và bật lên).
    public function displayImage(): ?string
    {
        if (app()->getLocale() === 'vi' && $this->show_vi_image && $this->image) {
            return $this->image;
        }

        return $this->image_intl ?: $this->image;
    }
}
