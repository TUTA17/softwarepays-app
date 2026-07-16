<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_packages', function (Blueprint $table) {
            $table->unsignedInteger('original_price')->nullable()->after('price');
            $table->decimal('promo_discount_percent', 5, 2)->nullable()->after('original_price');
        });
    }

    public function down(): void
    {
        Schema::table('card_packages', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'promo_discount_percent']);
        });
    }
};
