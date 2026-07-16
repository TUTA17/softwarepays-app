<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_admin_id')->nullable()->after('sold_to_user_id');
            $table->timestamp('claimed_at')->nullable()->after('assigned_admin_id');
            $table->text('note')->nullable()->after('error_message');

            $table->foreign('assigned_admin_id')->references('id')->on('admin')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('game_keys', function (Blueprint $table) {
            $table->dropForeign(['assigned_admin_id']);
            $table->dropColumn(['assigned_admin_id', 'claimed_at', 'note']);
        });
    }
};
