<?php

namespace App\Modules\GifMeme\Services;

use Illuminate\Support\Facades\Log;

class ImageMetadataService
{
    public function analyze(string $localPath): array
    {
        $result = ['width' => null, 'height' => null];

        try {
            $info = getimagesize($localPath);
            if ($info !== false) {
                $result['width'] = $info[0];
                $result['height'] = $info[1];
            }
        } catch (\Throwable $e) {
            Log::warning('Lỗi đọc metadata ảnh: ' . $e->getMessage());
        }

        return $result;
    }
}

