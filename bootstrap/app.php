<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'admin.auth' => \App\Modules\Core\Middleware\AdminAuth::class,
            'permission' => \App\Modules\Core\Middleware\CheckPermission::class,
        ]);

        // Production chạy sau nginx reverse proxy trên cùng host, proxy_pass qua cổng đã publish của
        // Docker -> container luôn thấy đúng 1 nguồn kết nối duy nhất là gateway của mạng Docker
        // (đã xác minh thực tế: REMOTE_ADDR luôn là 172.16.1.1). Chỉ tin tưởng đúng IP này (không phải
        // toàn bộ mạng) để $request->ip() trả về đúng IP khách thật (qua X-Forwarded-For) mà không mở
        // rộng khả năng giả mạo IP cho các request khác.
        $middleware->trustProxies(at: ['172.16.1.1']);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleAndCurrency::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/kinguin/webhook/*',
            'api/webhook/*',
            'payments/nowpayments/ipn',
            '*logout',
            '*logout*'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
