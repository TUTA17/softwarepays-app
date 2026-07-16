<?php

use App\Modules\Core\Controllers\Api\AuthController;
use App\Modules\Core\Controllers\Api\PushController;
use App\Modules\Shop\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/fulfill-manual', [OrderController::class, 'fulfillManual']);
        Route::post('/orders/{id}/fulfill-api', [OrderController::class, 'fulfillViaApi']);

        Route::post('/push/fcm-token', [PushController::class, 'fcmToken']);
    });
});
