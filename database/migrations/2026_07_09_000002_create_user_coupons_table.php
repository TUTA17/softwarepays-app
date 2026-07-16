<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coupon_id');
            $table->enum('status', ['saved', 'used'])->default('saved');
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            
            // Một user chỉ được lưu một mã 1 lần
            $table->unique(['user_id', 'coupon_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_coupons');
    }
};
