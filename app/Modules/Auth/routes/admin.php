<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\Admin\UserController;

Route::prefix(config('app.admin_prefix', 'admin'))->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/users', [UserController::class, 'users'])->name('users');
});