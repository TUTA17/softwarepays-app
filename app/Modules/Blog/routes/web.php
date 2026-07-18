<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Blog\Controllers\Theme\BlogController;

Route::middleware(['web'])->group(function () {
    Route::get('/tin-tuc', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/huong-dan', [BlogController::class, 'guides'])->name('blog.guides');
    Route::get('/tin-tuc/{slug}', [BlogController::class, 'show'])->name('blog.show');
    // Alias cho các bài Hướng dẫn muốn URL /guides/{slug} thay vì /tin-tuc/{slug} mặc định
    // (cùng controller/view, chỉ khác đường dẫn URL).
    Route::get('/guides/{slug}', [BlogController::class, 'show'])->name('blog.show.guide');
    Route::get('/api/cron/fetch-news', [BlogController::class, 'fetchCron'])->name('blog.fetchCron');
});
