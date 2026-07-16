<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->text('fcm_token');
            $table->string('token_hash', 64)->unique();
            $table->string('platform', 20)->default('android');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admin')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
