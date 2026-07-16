<?php

use Illuminate\Support\Facades\Route;
use Modules\UserWallet\Http\Controllers\UserWalletController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('userwallets', UserWalletController::class)->names('userwallet');
});
