<?php

namespace App\Modules\Core\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    // Xác minh token reCAPTCHA v2 với Google. Fail-open (coi như hợp lệ) khi chưa cấu hình
    // secret key hoặc khi gọi API Google bị lỗi/timeout, để tránh khoá cứng toàn bộ site
    // (đăng nhập/đăng ký/nạp tiền) chỉ vì sự cố tạm thời phía Google.
    public function verify(?string $token, ?string $ip = null): bool
    {
        $secret = config('services.recaptcha.secret_key');
        if (!$secret) {
            return true;
        }

        if (!$token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verify lỗi: ' . $e->getMessage());
            return true;
        }
    }
}
