<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Blog\Controllers\Theme\BlogController;

Route::middleware(['web'])->group(function () {
    Route::get('/tin-tuc', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/huong-dan', [BlogController::class, 'guides'])->name('blog.guides');
    Route::get('/tin-tuc/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/api/cron/fetch-news', [BlogController::class, 'fetchCron'])->name('blog.fetchCron');
});
