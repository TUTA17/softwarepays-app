<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SoundMeme\Controllers\Admin\SoundController;
use App\Modules\SoundMeme\Controllers\Admin\SoundCategoryController;
use App\Modules\Core\Controllers\Admin\R2TestController;

Route::prefix(config('app.admin_prefix', 'admin') . '/soundmeme')->name('admin.soundmeme.')->middleware(['admin.auth'])->group(function () {
    Route::get('/r2-test', [R2TestController::class, 'test'])->name('r2_test');
    Route::get('/sounds', [SoundController::class, 'index'])->name('sounds');
    Route::get('/sounds/create', [SoundController::class, 'create'])->name('sounds.create');
    Route::post('/sounds', [SoundController::class, 'store'])->name('sounds.store')->middleware('throttle:sound-upload');
    Route::post('/sounds/crawl', [SoundController::class, 'crawl'])->name('sounds.crawl');
    Route::post('/sounds/settings', [SoundController::class, 'saveSettings'])->name('sounds.settings');
    Route::put('/sounds/{id}/approve', [SoundController::class, 'approve'])->name('sounds.approve');
    Route::get('/sounds/{id}/edit', [SoundController::class, 'edit'])->name('sounds.edit');
    Route::put('/sounds/{id}', [SoundController::class, 'update'])->name('sounds.update')->middleware('throttle:sound-upload');
    Route::delete('/sounds/{id}', [SoundController::class, 'destroy'])->name('sounds.destroy');
    Route::post('/sounds/bulk-delete', [SoundController::class, 'bulkDelete'])->name('sounds.bulk_delete');
    Route::post('/sounds/bulk-approve', [SoundController::class, 'bulkApprove'])->name('sounds.bulk_approve');
    Route::post('/sounds/bulk-approve-all', [SoundController::class, 'bulkApproveAll'])->name('sounds.bulk_approve_all');
    Route::post('/sounds/bulk-delete-all', [SoundController::class, 'bulkDeleteAll'])->name('sounds.bulk_delete_all');

    Route::get('/categories', [SoundCategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [SoundCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [SoundCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [SoundCategoryController::class, 'destroy'])->name('categories.destroy');
});
