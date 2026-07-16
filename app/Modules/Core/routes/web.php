<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\Controllers\AuthController;
use App\Modules\Core\Controllers\DashboardController;
use App\Modules\Core\Controllers\AdminController;
use App\Modules\Core\Controllers\RoleController;
use App\Modules\Core\Controllers\SettingController;

// Login routes (public)
Route::get('/' . config('app.admin_prefix', 'admin') . '/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/' . config('app.admin_prefix', 'admin') . '/login', [AuthController::class, 'login'])->name('admin.login.post');

// PWA manifest cho trang quản trị (public, trình duyệt tự fetch khi cài app)
Route::get('/' . config('app.admin_prefix', 'admin') . '/manifest.webmanifest', [\App\Modules\Core\Controllers\Admin\PushController::class, 'manifest'])->name('admin.manifest');

// Admin protected routes
Route::prefix(config('app.admin_prefix', 'admin'))->middleware(['admin.auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Profile (self-edit)
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile/update', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');
    Route::post('/profile/change-password', [AdminController::class, 'changePassword'])->name('admin.profile.changePassword');

    // Admin management
    Route::prefix('admins')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.admins.index');
        Route::get('/create', [AdminController::class, 'create'])->name('admin.admins.create');
        Route::post('/', [AdminController::class, 'store'])->name('admin.admins.store');
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('admin.admins.edit');
        Route::put('/{id}', [AdminController::class, 'update'])->name('admin.admins.update');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('admin.admins.destroy');
    });

    // Role management
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });

    // Settings management
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('admin.settings.store');
    
    // SMM Panel Settings
    Route::get('/settings/smm', [\App\Modules\Core\Controllers\SmmSettingController::class, 'index'])->name('admin.settings.smm');
    Route::post('/settings/smm', [\App\Modules\Core\Controllers\SmmSettingController::class, 'store'])->name('admin.settings.smm.store');

    // System management (Cache, Error Logs, Backup, Import)
    Route::prefix('system')->name('admin.system.')->group(function () {
        // Cache
        Route::get('cache', [\App\Modules\Core\Controllers\System\CacheController::class, 'index'])->name('cache.index');
        Route::get('cache/clear/{type}', [\App\Modules\Core\Controllers\System\CacheController::class, 'clear'])->name('cache.clear');



        // Sao lưu dữ liệu
        Route::get('backup', [\App\Modules\Core\Controllers\System\BackupController::class, 'index'])->name('backup.index');
        Route::post('backup', [\App\Modules\Core\Controllers\System\BackupController::class, 'store'])->name('backup.store');
        Route::get('backup/run', [\App\Modules\Core\Controllers\System\BackupController::class, 'runBackup'])->name('backup.run');
        Route::get('backup/download/{name}', [\App\Modules\Core\Controllers\System\BackupController::class, 'downloadDB'])->name('backup.download');
        Route::get('backup/delete/{name}', [\App\Modules\Core\Controllers\System\BackupController::class, 'deleteDB'])->name('backup.delete');

        // Import Excel
        Route::get('import', [\App\Modules\Core\Controllers\System\ImportController::class, 'index'])->name('import.index');
        Route::post('import/upload', [\App\Modules\Core\Controllers\System\ImportController::class, 'upload'])->name('import.upload');
        Route::post('import/process', [\App\Modules\Core\Controllers\System\ImportController::class, 'processSave'])->name('import.process');
    });
});
