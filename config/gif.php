<?php

return [

    'max_upload_mb' => (int) env('GIF_MAX_UPLOAD_MB', 15),

    // Chỉ dùng nếu sau này bật custom domain public cho bucket (chưa cấu hình DNS hiện tại).
    // Để trống -> luôn dùng presigned URL tạm thời qua R2StorageService.
    'public_url' => env('R2_PUBLIC_URL'),

    'allowed_mime_types' => [
        'image/gif',
        'image/webp',
        'image/png',
        'image/jpeg',
    ],

    'allowed_extensions' => ['gif', 'webp', 'png', 'jpg', 'jpeg'],

];
