<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Theme\Controllers\HomeController;
use App\Modules\Theme\Controllers\ProductController;
use App\Modules\Theme\Controllers\ShopController;
use App\Modules\Theme\Controllers\AuthController;
use App\Modules\Theme\Controllers\PageController;



Route::get('/currency/{currency}', function ($currency) {
    if (in_array(strtoupper($currency), ['VND', 'USD', 'EUR', 'CNY', 'JPY', 'KRW', 'THB', 'RUB'])) {
        session(['currency' => strtoupper($currency)]);
    }
    return redirect()->back();
})->name('currency.switch');

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, App\Http\Middleware\SetLocaleAndCurrency::SUPPORTED_LOCALES)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/promotions', [PageController::class, 'promotions'])->name('pages.promotions');
Route::get('/support', [PageController::class, 'support'])->name('pages.support');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('pages.privacy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('pages.terms');
Route::get('/warranty-policy', [PageController::class, 'warrantyPolicy'])->name('pages.warranty');


Route::get('/dev/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return "Đã xóa toàn bộ Cache, Route, View, Config thành công!";
});

Route::get('/dev/reset-news', function () {
    \Illuminate\Support\Facades\DB::table('blog_posts')->truncate();
    return "Đã xóa toàn bộ các bài viết tạm không có nội dung. Vui lòng gọi lại API /api/cron/fetch-news để cào lại 20 bài viết mới nhất VỚI ĐẦY ĐỦ NỘI DUNG CHI TIẾT!";
});



// Bank/MoMo Top-up Webhook Route
Route::post('/api/webhook/bank-transfer', [\App\Modules\Auth\Controllers\Theme\WalletController::class, 'webhook']);

// Load Admin Routes


