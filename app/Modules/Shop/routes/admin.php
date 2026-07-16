<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Shop\Controllers\Admin\ProductController;
use App\Modules\Shop\Controllers\Admin\KeyController;
use App\Modules\Shop\Controllers\Admin\TransactionController;
use App\Modules\Shop\Controllers\Admin\CouponController;

Route::prefix(config('app.admin_prefix', 'admin'))->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'products'])->name('products');
    Route::post('/products', [ProductController::class, 'storeProduct'])->name('products.store');
    Route::get('/giftcards', [ProductController::class, 'giftcards'])->name('giftcards');
    Route::post('/giftcards/sync', [ProductController::class, 'syncKinguin'])->name('giftcards.sync');
    Route::post('/products/manual-store', [ProductController::class, 'storeManualProduct'])->name('products.manual_store');
    Route::post('/products/sync', [ProductController::class, 'syncSteam'])->name('products.sync');
    Route::post('/products/kinguin-eur-rate', [ProductController::class, 'saveKinguinEurRate'])->name('products.kinguin_eur_rate');
    Route::post('/products/kinguin-reprice', [ProductController::class, 'recalculateKinguinPrices'])->name('products.kinguin_reprice');
    Route::post('/products/{id}/aliases', [ProductController::class, 'updateAliases'])->name('products.aliases');
    
    Route::get('/keys', [KeyController::class, 'keys'])->name('keys');
    Route::post('/keys', [KeyController::class, 'storeKeys'])->name('keys.store');
    
    Route::get('/transactions', [TransactionController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/export', [TransactionController::class, 'exportTransactions'])->name('transactions.export');
    
    // Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // Categories
    Route::get('/categories', [\App\Modules\Shop\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [\App\Modules\Shop\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Modules\Shop\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [\App\Modules\Shop\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/sync', [\App\Modules\Shop\Controllers\Admin\CategoryController::class, 'sync'])->name('categories.sync');

    // Orders
    Route::get('/orders', [\App\Modules\Shop\Controllers\Admin\OrderController::class, 'index'])->name('orders');
    Route::post('/orders/{id}/fulfill-manual', [\App\Modules\Shop\Controllers\Admin\OrderController::class, 'fulfillManual'])->name('orders.fulfill_manual');
    Route::post('/orders/{id}/fulfill-api', [\App\Modules\Shop\Controllers\Admin\OrderController::class, 'fulfillViaApi'])->name('orders.fulfill_api');
    Route::post('/orders/toggle-mode', [\App\Modules\Shop\Controllers\Admin\OrderController::class, 'toggleMode'])->name('orders.toggle_mode');

    // VPN & eSIM — quản lý riêng: tỉ lệ lợi nhuận + danh sách sản phẩm + upload ảnh
    Route::get('/vpn', [\App\Modules\Shop\Controllers\Admin\VpnController::class, 'index'])->name('vpn');
    Route::post('/vpn/margin', [\App\Modules\Shop\Controllers\Admin\VpnController::class, 'updateMargin'])->name('vpn.update_margin');
    Route::post('/vpn/exchange-rate', [\App\Modules\Shop\Controllers\Admin\VpnController::class, 'updateExchangeRate'])->name('vpn.update_rate');
    Route::post('/vpn/{id}/upload-image', [\App\Modules\Shop\Controllers\Admin\VpnController::class, 'uploadImage'])->name('vpn.upload_image');
    Route::get('/esim', [\App\Modules\Shop\Controllers\Admin\EsimController::class, 'index'])->name('esim');
    Route::post('/esim/margin', [\App\Modules\Shop\Controllers\Admin\EsimController::class, 'updateMargin'])->name('esim.update_margin');
    Route::post('/esim/{id}/upload-image', [\App\Modules\Shop\Controllers\Admin\EsimController::class, 'uploadImage'])->name('esim.upload_image');

    // Thẻ Nạp & Thẻ Game — quản lý riêng: tỉ lệ lợi nhuận + danh sách sản phẩm + upload ảnh
    Route::get('/card', [\App\Modules\Shop\Controllers\Admin\CardController::class, 'index'])->name('card');
    Route::post('/card/margin', [\App\Modules\Shop\Controllers\Admin\CardController::class, 'updateMargin'])->name('card.update_margin');
    Route::post('/card/{id}/upload-image', [\App\Modules\Shop\Controllers\Admin\CardController::class, 'uploadImage'])->name('card.upload_image');
    Route::get('/card/{id}/packages', [\App\Modules\Shop\Controllers\Admin\CardController::class, 'packages'])->name('card.packages');
    Route::post('/card/{id}/packages', [\App\Modules\Shop\Controllers\Admin\CardController::class, 'updatePackages'])->name('card.packages.update');

    // Gói Đăng Ký & Phần Mềm — sản phẩm thủ công, không có tỉ lệ lợi nhuận (không qua wholesale API),
    // chỉ cần danh sách sản phẩm + upload logo thương hiệu.
    Route::get('/subscription', [\App\Modules\Shop\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscription');
    Route::post('/subscription/{id}/upload-image', [\App\Modules\Shop\Controllers\Admin\SubscriptionController::class, 'uploadImage'])->name('subscription.upload_image');
    Route::post('/subscription/{id}/video', [\App\Modules\Shop\Controllers\Admin\SubscriptionController::class, 'updateVideo'])->name('subscription.update_video');
    Route::get('/software', [\App\Modules\Shop\Controllers\Admin\SoftwareController::class, 'index'])->name('software');
    Route::post('/software/{id}/upload-image', [\App\Modules\Shop\Controllers\Admin\SoftwareController::class, 'uploadImage'])->name('software.upload_image');
    Route::post('/software/{id}/video', [\App\Modules\Shop\Controllers\Admin\SoftwareController::class, 'updateVideo'])->name('software.update_video');

    // Banner trang chủ — slide ảnh + link, quản lý qua Admin
    Route::get('/banners', [\App\Modules\Shop\Controllers\Admin\BannerController::class, 'index'])->name('banners');
    Route::post('/banners', [\App\Modules\Shop\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{id}', [\App\Modules\Shop\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
    Route::post('/banners/{id}/toggle-active', [\App\Modules\Shop\Controllers\Admin\BannerController::class, 'toggleActive'])->name('banners.toggle_active');
    Route::delete('/banners/{id}', [\App\Modules\Shop\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');
});