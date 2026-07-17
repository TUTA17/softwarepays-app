<?php
// Core routes file. Module routes are loaded via ModulesServiceProvider.
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HumanVerifyController;

Route::get('/human-verify', [HumanVerifyController::class, 'form'])->name('human.verify.form');
Route::post('/human-verify', [HumanVerifyController::class, 'verify'])->name('human.verify');

// Webhook / API URL for external Cron Job (danh mục game — nay lấy từ Kinguin catalog thật,
// không còn cào Steam nữa; route giữ nguyên URL cũ để cron job hiện có trên server không bị gãy).
Route::get('/system/sync-steam-games/{token}', function($token) {
    if ($token !== 'K9xP2mQvL5') {
        return response('Unauthorized', 403);
    }

    ignore_user_abort(true);
    set_time_limit(0);

    if (function_exists('fastcgi_finish_request')) {
        echo "Bắt đầu chạy CronJob ngầm đồng bộ game từ Kinguin...";
        fastcgi_finish_request();
    }

    try {
        Artisan::call('kinguin:sync-games', ['--pages' => 20]);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error("CronJob Kinguin Sync Error: " . $e->getMessage());
    }

    if (!function_exists('fastcgi_finish_request')) {
        return "CronJob Kinguin đã chạy xong!";
    }
});

// Webhook / API URL for external Cron Job (Kinguin Wallet)
Route::get('/system/sync-kinguin/{token}', function($token) {
    if ($token !== 'K9xP2mQvL5') {
        return response('Unauthorized', 403);
    }

    ignore_user_abort(true);
    set_time_limit(0);

    if (function_exists('fastcgi_finish_request')) {
        echo "Bắt đầu chạy CronJob ngầm Kinguin...";
        fastcgi_finish_request();
    }

    try {
        Artisan::call('kinguin:fetch-giftcards');
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error("CronJob Kinguin Error: " . $e->getMessage());
    }

    if (!function_exists('fastcgi_finish_request')) {
        return "CronJob Kinguin đã chạy xong!";
    }
});
