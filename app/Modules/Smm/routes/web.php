<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Smm\Controllers\SmmController;

Route::middleware(['web'])->group(function () {
    Route::get('/smm', [SmmController::class, 'index'])->name('smm.index');
    
    // Yêu cầu đăng nhập mới được đặt hàng
    Route::middleware(['auth'])->group(function () {
        Route::post('/smm/order', [SmmController::class, 'order'])->name('smm.order');
    });
});
