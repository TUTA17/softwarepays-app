<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Smm\Controllers\Admin\SmmOrderController;

Route::prefix(config('app.admin_prefix', 'admin'))->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::post('/smm-orders/{id}/refresh', [SmmOrderController::class, 'refreshStatus'])->name('smm_orders.refresh');
    Route::post('/smm-orders/{id}/send-to-api', [SmmOrderController::class, 'sendToApi'])->name('smm_orders.send_to_api');
});
