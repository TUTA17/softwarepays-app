<?php

use Illuminate\Support\Facades\Route;
use App\Modules\GifMeme\Controllers\Admin\GifController;
use App\Modules\GifMeme\Controllers\Admin\GifCategoryController;
use App\Modules\Core\Controllers\Admin\R2TestController;

Route::prefix(config('app.admin_prefix', 'admin') . '/gifmeme')->name('admin.gifmeme.')->middleware(['admin.auth'])->group(function () {
    Route::get('/r2-test', [R2TestController::class, 'test'])->name('r2_test');
    Route::get('/gifs', [GifController::class, 'index'])->name('gifs');
    Route::get('/gifs/create', [GifController::class, 'create'])->name('gifs.create');
    Route::post('/gifs', [GifController::class, 'store'])->name('gifs.store')->middleware('throttle:Gif-upload');
    Route::post('/gifs/crawl', [GifController::class, 'crawl'])->name('gifs.crawl');
    Route::post('/gifs/crawl-images', [GifController::class, 'crawlImages'])->name('gifs.crawl_images');
    Route::post('/gifs/settings', [GifController::class, 'saveSettings'])->name('gifs.settings');
    Route::put('/gifs/{id}/approve', [GifController::class, 'approve'])->name('gifs.approve');
    Route::get('/gifs/{id}/edit', [GifController::class, 'edit'])->name('gifs.edit');
    Route::put('/gifs/{id}', [GifController::class, 'update'])->name('gifs.update')->middleware('throttle:Gif-upload');
    Route::delete('/gifs/{id}', [GifController::class, 'destroy'])->name('gifs.destroy');
    Route::post('/gifs/bulk-delete', [GifController::class, 'bulkDelete'])->name('gifs.bulk_delete');
    Route::post('/gifs/bulk-approve', [GifController::class, 'bulkApprove'])->name('gifs.bulk_approve');
    Route::post('/gifs/bulk-approve-all', [GifController::class, 'bulkApproveAll'])->name('gifs.bulk_approve_all');
    Route::post('/gifs/bulk-delete-all', [GifController::class, 'bulkDeleteAll'])->name('gifs.bulk_delete_all');

    Route::get('/categories', [GifCategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [GifCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [GifCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [GifCategoryController::class, 'destroy'])->name('categories.destroy');
});


