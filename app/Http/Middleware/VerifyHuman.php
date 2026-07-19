<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Cổng xác minh "không phải robot" (reCAPTCHA v2) — CHỈ áp dụng cho các bước liên quan tới
// tiền (thanh toán đơn hàng, nạp ví), không còn chặn toàn site như trước. Sau khi xác minh 1
// lần, session lưu cờ human_verified nên các bước thanh toán/nạp tiền sau đó trong cùng phiên
// tự động được thông qua — không cần gắn captcha riêng lẻ từng form.
class VerifyHuman
{
    // CHỈ những path này mới cần qua cổng xác minh — thanh toán đơn hàng và nạp ví (đều là nơi
    // tiền thực sự di chuyển). Mọi trang/API khác (duyệt shop, đăng nhập, đăng ký, blog, SMM,
    // sound/gif meme...) không còn bị chặn bởi cổng này nữa.
    protected array $protectedPrefixes = [
        'cart/checkout',
        'wallet',
        'payments/nowpayments/pay',
        'payments/nowpayments/status',
        'payments/nowpayments/min-amount',
        'payments/paylio/pay',
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

        $isProtected = false;
        foreach ($this->protectedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $isProtected = true;
                break;
            }
        }

        if (!$isProtected) {
            return $next($request);
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
                'message' => __('human_verify.required_error'),
            ], 403);
        }

        if ($request->isMethod('get')) {
            $request->session()->put('human_verify_redirect', $request->fullUrl());
        }

        return redirect()->route('human.verify.form');
    }
}
