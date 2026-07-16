<?php

use Illuminate\Support\Facades\Route;
use Modules\UserWallet\Http\Controllers\UserWalletController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('userwallets', UserWalletController::class)->names('userwallet');
});
