<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop old conflicting tables
        Schema::dropIfExists('admins');
        Schema::dropIfExists('settings');

        // 1. Bảng Admin
        if (!Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->nullable();
                $table->string('email', 255)->unique();
                $table->string('password', 60);
                $table->string('image', 100)->nullable()->default('admin_default.png');
                $table->boolean('status')->default(1);
                $table->boolean('super_admin')->default(0);
                
                $table->string('tel', 20)->nullable();
                $table->string('address', 255)->nullable();
                $table->text('intro')->nullable();
                $table->string('cccd', 100)->nullable();
                $table->string('gioitinh', 10)->nullable();
                $table->date('birthday')->nullable();
                $table->string('facebook', 255)->nullable();
                $table->string('zalo', 255)->nullable();
                $table->string('skype', 255)->nullable();
                $table->string('ID_card_photo_on_the_front', 100)->nullable();
                $table->string('ID_card_photo_on_the_back', 100)->nullable();

                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Bảng Roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Bảng Permissions
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('display_name', 100)->nullable();
                $table->string('group', 100)->nullable();
                $table->timestamps();
            });
        }

        // 4. Bảng trung gian Role_Admin
        if (!Schema::hasTable('role_admin')) {
            Schema::create('role_admin', function (Blueprint $table) {
                $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->primary(['admin_id', 'role_id']);
            });
        }

        // 5. Bảng trung gian Role_Permission
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->primary(['role_id', 'permission_id']);
            });
        }

        // 6. Bảng Settings
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->text('value')->nullable();
                $table->string('type', 100)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_admin');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('admin');
    }
};
