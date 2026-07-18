<?php

namespace App\Modules\GifMeme\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Wrapper mỏng quanh Storage::disk('r2') (Cloudflare R2, tương thích S3 qua Flysystem).
// Bucket là riêng tư, chưa có custom domain public, nên đọc file luôn đi qua presigned URL
// (getSignedDownloadUrl) thay vì URL cố định.
class R2StorageService
{
    protected function disk()
    {
        return Storage::disk('r2');
    }

    public function uploadObject(string $localPath, string $key, ?string $mimeType = null): bool
    {
        $stream = fopen($localPath, 'r');
        if (!$stream) {
            throw new \RuntimeException("Không mở được file tạm để upload: {$localPath}");
        }

        try {
            $options = $mimeType ? ['ContentType' => $mimeType] : [];
            return $this->disk()->put($key, $stream, $options);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function deleteObject(string $key): bool
    {
        try {
            return $this->disk()->delete($key);
        } catch (\Throwable $e) {
            Log::error("R2 xoá object thất bại [{$key}]: " . $e->getMessage());
            throw $e;
        }
    }

    public function objectExists(string $key): bool
    {
        return $this->disk()->exists($key);
    }

    // Presigned PUT — dự phòng cho upload trực tiếp từ trình duyệt lên R2 sau này
    // (hiện tại admin vẫn upload qua backend để còn đọc metadata bằng getID3).
    public function getSignedUploadUrl(string $key, ?string $mimeType = null, int $expiresMinutes = 15): string
    {
        $result = $this->disk()->temporaryUploadUrl(
            $key,
            now()->addMinutes($expiresMinutes),
            $mimeType ? ['ContentType' => $mimeType] : []
        );

        return $result['url'];
    }

    public function getSignedDownloadUrl(string $key, int $expiresMinutes = 30, ?string $downloadFilename = null): string
    {
        $options = [];
        if ($downloadFilename) {
            $options['ResponseContentDisposition'] = 'attachment; filename="' . str_replace('"', '', $downloadFilename) . '"';
        }

        return $this->disk()->temporaryUrl($key, now()->addMinutes($expiresMinutes), $options);
    }

    // Trả URL public cố định nếu sau này bật custom domain (R2_PUBLIC_URL); null nếu chưa có
    // -> caller phải fallback sang getSignedDownloadUrl().
    public function getPublicObjectUrl(string $key): ?string
    {
        $base = config('gif.public_url');
        if (!$base) {
            return null;
        }

        return rtrim($base, '/') . '/' . ltrim($key, '/');
    }
}


