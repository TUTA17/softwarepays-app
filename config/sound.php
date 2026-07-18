<?php

return [

    'max_upload_mb' => (int) env('SOUND_MAX_UPLOAD_MB', 25),

    // Chỉ dùng nếu sau này bật custom domain public cho bucket (chưa cấu hình DNS hiện tại).
    // Để trống -> luôn dùng presigned URL tạm thời qua R2StorageService.
    'public_url' => env('R2_PUBLIC_URL'),

    'allowed_mime_types' => [
        'audio/mpeg',
        'audio/ogg',
        'audio/wav',
        'audio/x-wav',
        'audio/webm',
        'audio/mp4',
    ],

    'allowed_extensions' => ['mp3', 'ogg', 'wav', 'webm', 'm4a', 'mp4'],

];
