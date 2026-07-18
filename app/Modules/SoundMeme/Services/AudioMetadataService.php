<?php

namespace App\Modules\SoundMeme\Services;

use Illuminate\Support\Facades\Log;

// Đọc duration/bitrate/codec bằng getID3 (thuần PHP, không cần ffmpeg cài trên server).
// Server hiện không có ffmpeg và container không build image riêng (chạy thẳng từ php:8.3-cli,
// mất mọi gói cài thêm mỗi khi container bị tạo lại) nên cố tình tránh phụ thuộc binary hệ thống.
class AudioMetadataService
{
    public function analyze(string $localPath): array
    {
        $result = ['duration' => null, 'bitrate' => null, 'codec' => null];

        if (!class_exists(\getID3::class)) {
            Log::warning('getID3 chưa được cài (composer require james-heinrich/getid3) — bỏ qua đọc metadata âm thanh.');
            return $result;
        }

        try {
            $getID3 = new \getID3();
            $info = $getID3->analyze($localPath);

            if (isset($info['playtime_seconds'])) {
                $result['duration'] = (int) round($info['playtime_seconds']);
            }
            if (isset($info['audio']['bitrate'])) {
                $result['bitrate'] = (int) round($info['audio']['bitrate'] / 1000);
            }
            if (isset($info['audio']['dataformat'])) {
                $result['codec'] = $info['audio']['dataformat'];
            }
        } catch (\Throwable $e) {
            Log::warning('getID3 đọc metadata âm thanh thất bại: ' . $e->getMessage());
        }

        return $result;
    }
}
