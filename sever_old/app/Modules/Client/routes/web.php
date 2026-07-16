<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Client\Controllers\HomeController;
use App\Modules\Client\Controllers\ProductController;
use App\Modules\Client\Controllers\ShopController;
use App\Modules\Client\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/game/{id}-{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('login');
Route::post('/sign-in', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Modules\Client\Controllers\WalletController;
use App\Modules\Client\Controllers\PurchaseController;

use App\Modules\Client\Controllers\UserController;

Route::middleware('auth')->group(function () {
    // User routes
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/transactions', [UserController::class, 'transactions'])->name('profile.transactions');
    Route::get('/profile/settings', [UserController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/settings', [UserController::class, 'updateSettings'])->name('profile.settings.update');
    
    // Wallet
    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    
    // Cart
    Route::get('/cart', [\App\Modules\Client\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [\App\Modules\Client\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{id}', [\App\Modules\Client\Controllers\CartController::class, 'remove'])->name('cart.remove');
    // Checkout
    Route::get('/checkout', [\App\Modules\Client\Controllers\CartController::class, 'checkoutView'])->name('cart.checkout');
    Route::post('/checkout/process', [\App\Modules\Client\Controllers\CartController::class, 'checkoutProcess'])->name('cart.checkout.process');

    // Purchase (Buy Now - Adds to cart and redirects to checkout)
    Route::post('/game/{id}/buy', [PurchaseController::class, 'buy'])->name('product.buy');
});

Route::get('/dev/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return "Đã xóa toàn bộ Cache thành công!";
});


// Kinguin Webhook Routes
use App\Modules\Client\Controllers\KinguinWebhookController;
Route::post('/api/kinguin/webhook/product-update', [KinguinWebhookController::class, 'onProductUpdate']);
Route::post('/api/kinguin/webhook/order-completed', [KinguinWebhookController::class, 'onOrderCompleted']);
Route::post('/api/kinguin/webhook/status-change', [KinguinWebhookController::class, 'onOrderStatusChange']);

// Bank/MoMo Top-up Webhook Route
Route::post('/api/webhook/bank-transfer', [\App\Modules\Client\Controllers\WalletController::class, 'webhook']);

// Load Admin Routes


