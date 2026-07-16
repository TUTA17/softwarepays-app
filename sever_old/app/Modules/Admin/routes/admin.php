<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Admin\Controllers\AdminController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Protected Admin routes (Game Shop features)
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/products', [AdminController::class, 'products'])->name('products');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::post('/products/{id}/aliases', [AdminController::class, 'updateAliases'])->name('products.aliases');
        Route::get('/keys', [AdminController::class, 'keys'])->name('keys');
        Route::post('/keys', [AdminController::class, 'storeKeys'])->name('keys.store');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/export', [AdminController::class, 'exportTransactions'])->name('transactions.export');
        
        Route::get('/settings/payment', [AdminController::class, 'paymentSettings'])->name('settings.payment');
        Route::post('/settings/payment', [AdminController::class, 'savePaymentSettings']);
    });

});
