<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('users', function (Blueprint $table) {
    if (!Schema::hasColumn('users', 'two_factor_enabled')) {
        $table->boolean('two_factor_enabled')->default(false);
    }
    if (!Schema::hasColumn('users', 'checkout_otp_enabled')) {
        $table->boolean('checkout_otp_enabled')->default(false);
    }
    if (!Schema::hasColumn('users', 'two_factor_secret')) {
        $table->text('two_factor_secret')->nullable();
    }
    if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
        $table->text('two_factor_recovery_codes')->nullable();
    }
    if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
        $table->timestamp('two_factor_confirmed_at')->nullable();
    }
    
    // Add social login fields if missing
    if (!Schema::hasColumn('users', 'google_id')) {
        $table->string('google_id')->nullable();
    }
    if (!Schema::hasColumn('users', 'github_id')) {
        $table->string('github_id')->nullable();
    }
});

echo "Missing columns added to users table successfully.\n";
