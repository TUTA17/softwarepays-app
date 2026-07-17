<?php

use App\Modules\Auth\Controllers\Api\UserController;
use App\Modules\Core\Controllers\Api\AuthController;
use App\Modules\Core\Controllers\Api\DashboardController;
use App\Modules\Core\Controllers\Api\PushController;
use App\Modules\Shop\Controllers\Api\OrderController;
use App\Modules\Shop\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/claim', [OrderController::class, 'claim']);
        Route::post('/orders/{id}/release', [OrderController::class, 'release']);
        Route::post('/orders/{id}/fulfill-manual', [OrderController::class, 'fulfillManual']);
        Route::post('/orders/{id}/fulfill-api', [OrderController::class, 'fulfillViaApi']);
        Route::post('/orders/{id}/refresh-smm-status', [OrderController::class, 'refreshSmmStatus']);
        Route::post('/orders/{id}/send-smm-to-api', [OrderController::class, 'sendSmmToApi']);
        Route::post('/orders/toggle-mode', [OrderController::class, 'toggleMode']);

        Route::post('/push/fcm-token', [PushController::class, 'fcmToken']);

        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/customers', [UserController::class, 'index']);
        Route::post('/customers/{id}/add-balance', [UserController::class, 'addBalance']);
        Route::get('/transactions', [TransactionController::class, 'index']);
    });
});
