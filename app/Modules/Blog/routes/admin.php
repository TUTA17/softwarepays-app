<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Blog\Controllers\Admin\CategoryController;
use App\Modules\Blog\Controllers\Admin\PostController;

Route::prefix(config('app.admin_prefix', 'admin') . '/blog')->name('admin.blog.')->middleware(['admin.auth'])->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    
    Route::get('/posts', [PostController::class, 'index'])->name('posts');
    Route::post('/posts/sync', [PostController::class, 'sync'])->name('posts.sync');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
});
