<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('tags')->nullable();

            // Vị trí lưu trên R2 — bắt buộc lưu cả object_key (không chỉ URL) để còn xoá được
            // file thật trên R2 sau này; public_url chỉ dùng khi có bucket public/custom domain,
            // hiện tại luôn null vì đang phát/tải qua presigned URL sinh động.
            $table->string('object_key');
            $table->string('thumbnail_key')->nullable();
            $table->string('public_url')->nullable();

            $table->string('original_filename');
            $table->string('mime_type')->default('image/gif');
            $table->string('extension', 10)->default('gif');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('file_size');

            $table->string('status')->default('draft'); // draft|approved|published|hidden
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('play_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('gif_categories')->nullOnDelete();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gifs');
    }
};
