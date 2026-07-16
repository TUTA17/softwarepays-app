<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Core\Controllers\Admin\SettingController;
use App\Modules\Core\Controllers\Admin\PushController;

Route::prefix(config('app.admin_prefix', 'admin'))->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/settings/payment', [SettingController::class, 'paymentSettings'])->name('settings.payment');
    Route::post('/settings/payment', [SettingController::class, 'savePaymentSettings']);
    Route::post('/settings/payment/fee', [SettingController::class, 'savePaymentFeeSettings'])->name('settings.payment.fee');
    Route::post('/settings/payment/intl-fee', [SettingController::class, 'saveIntlPaymentFeeSettings'])->name('settings.payment.intl-fee');

    Route::get('/settings/affiliate', [SettingController::class, 'affiliateSettings'])->name('settings.affiliate');
    Route::post('/settings/affiliate', [SettingController::class, 'saveAffiliateSettings']);

    // Push notification (nạp qua điện thoại/desktop khi cài PWA)
    Route::get('/push/public-key', [PushController::class, 'publicKey'])->name('push.public-key');
    Route::post('/push/subscribe', [PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::post('/push/fcm-token', [PushController::class, 'fcmToken'])->name('push.fcm-token');
});