<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Cổng xác minh "không phải robot" (reCAPTCHA v2) cho khách lần đầu vào site trong phiên.
// Sau khi xác minh 1 lần, session lưu cờ human_verified nên các trang/API sau đó (đăng nhập,
// đăng ký, nạp tiền, thanh toán...) tự động được thông qua vì cùng nằm sau cổng này — không
// cần gắn captcha riêng lẻ từng form.
class VerifyHuman
{
    // Các path không cần qua cổng: trang quản trị, API nội bộ (app di động dùng Sanctum token,
    // không có session trình duyệt để redirect), webhook thanh toán/cron server-to-server,
    // và chính route xử lý captcha (tránh vòng lặp redirect vô hạn).
    protected array $exceptPrefixes = [
        'api/',
        'human-verify',
        'up',
        'payments/nowpayments/ipn',
        'system/sync-steam-games',
        'system/sync-kinguin',
    ];

    // Cho qua bot tìm kiếm / preview link hợp lệ để không ảnh hưởng SEO và tính năng xem trước
    // khi chia sẻ link (Facebook, Zalo, Telegram...). Không dùng để chặn bot xấu (việc đó do
    // chính cổng captcha đảm nhiệm), chỉ để allowlist các bot tốt đã biết.
    protected string $goodBotPattern = '/googlebot|bingbot|facebookexternalhit|twitterbot|zalobot|slackbot|telegrambot|whatsapp|linkedinbot|applebot/i';

    public function handle(Request $request, Closure $next)
    {
        if (!config('services.recaptcha.secret_key')) {
            return $next($request);
        }

        $adminPrefix = trim(config('app.admin_prefix', 'admin'), '/');
        $path = ltrim($request->path(), '/');

        if ($path === $adminPrefix || str_starts_with($path, $adminPrefix . '/')) {
            return $next($request);
        }

        foreach ($this->exceptPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }

        if ($request->userAgent() && preg_match($this->goodBotPattern, $request->userAgent())) {
            return $next($request);
        }

        if ($request->session()->get('human_verified')) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'human_verify_required' => true,
                'message' => 'Vui lòng xác minh bạn không phải robot rồi thử lại.',
            ], 403);
        }

        if ($request->isMethod('get')) {
            $request->session()->put('human_verify_redirect', $request->fullUrl());
        }

        return redirect()->route('human.verify.form');
    }
}
