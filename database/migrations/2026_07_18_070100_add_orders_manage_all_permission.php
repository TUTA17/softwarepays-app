<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('permissions')->where('name', 'orders.manage_all')->exists()) {
            DB::table('permissions')->insert([
                'name' => 'orders.manage_all',
                'display_name' => 'Xử lý tất cả đơn hàng (bỏ qua khoá nhận đơn)',
                'group' => 'orders',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->where('name', 'orders.manage_all')->delete();
    }
};
