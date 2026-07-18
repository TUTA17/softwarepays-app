<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\Theme\AuthController;
use App\Modules\Auth\Controllers\Theme\SocialAuthController;
use App\Modules\Auth\Controllers\Theme\WalletController;
use App\Modules\Auth\Controllers\Theme\ForgotPasswordController;
use App\Modules\Auth\Controllers\Theme\SocialController;
use App\Modules\Auth\Controllers\Theme\TwoFactorController;
use App\Modules\Auth\Controllers\Theme\UserController;
use App\Modules\Auth\Controllers\Theme\PaymentGatewayController;

Route::middleware(['web'])->group(function () {
    Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/sign-in', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/verify', [AuthController::class, 'registerVerifyForm'])->name('register.verify.form');
    Route::post('/register/verify', [AuthController::class, 'registerVerifyProcess'])->name('register.verify.verify');
    Route::post('/register/verify/resend', [AuthController::class, 'registerVerifyResend'])->name('register.verify.resend');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Social Login
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect.old');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback.old');

    // Social Login routes
    Route::get('/login/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('/login/{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');

    // 2FA routes
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('twofactor.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('twofactor.verify.post');

    // Forgot Password routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showEmailForm'])->name('password.request');
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

    Route::middleware('auth')->group(function () {
        // User routes
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile/transactions', [UserController::class, 'transactions'])->name('profile.transactions');
        Route::get('/profile/settings', [UserController::class, 'settings'])->name('profile.settings');
        Route::post('/profile/settings', [UserController::class, 'updateSettings'])->name('profile.settings.update');
        Route::get('/profile/referrals', [UserController::class, 'referrals'])->name('referrals.index');

        // Email verification
        Route::get('/verify-email', [AuthController::class, 'verifyEmailForm'])->name('verify.email.form');
        Route::post('/verify-email', [AuthController::class, 'verifyEmailProcess'])->name('verify.email.verify');
        Route::post('/verify-email/resend', [AuthController::class, 'verifyEmailResend'])->name('verify.email.resend');
        
        // Wallet
        Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
        Route::post('/wallet/deposit/create', [WalletController::class, 'createInvoice'])->name('wallet.deposit.create');
        Route::post('/wallet/transaction/{id}/cancel', [WalletController::class, 'cancelTransaction'])->name('wallet.transaction.cancel');
        Route::get('/wallet/transaction/{id}/status', [WalletController::class, 'checkStatus'])->name('wallet.transaction.status');

        // NOWPayments (crypto)
        Route::post('/payments/nowpayments/pay', [PaymentGatewayController::class, 'nowpaymentsPay'])->name('payments.nowpayments.pay');
        Route::get('/payments/nowpayments/status/{transaction}', [PaymentGatewayController::class, 'nowpaymentsStatus'])->name('payments.nowpayments.status');
        Route::get('/payments/nowpayments/min-amount/{method}', [PaymentGatewayController::class, 'nowpaymentsMinAmount'])->name('payments.nowpayments.min_amount');

        // Paylio (thẻ/PayPal/Stripe/Klarna... qua on-ramp, tiền về ví USDC Polygon)
        Route::post('/payments/paylio/pay', [PaymentGatewayController::class, 'paylioPay'])->name('payments.paylio.pay');
    });

    // Bank/MoMo Top-up Webhook Route
    Route::post('/api/webhook/bank-transfer', [WalletController::class, 'webhook']);

    // NOWPayments IPN Webhook (không có session/auth, xác thực bằng chữ ký HMAC)
    Route::post('/payments/nowpayments/ipn', [PaymentGatewayController::class, 'nowpaymentsIpn'])->name('payments.nowpayments.ipn');

    // Paylio redirect khách về sau khi thanh toán xong ở trang hosted của họ (không có session/auth cố định)
    Route::get('/payments/paylio/callback/{transaction}', [PaymentGatewayController::class, 'paylioCallback'])->name('payments.paylio.callback');
});
