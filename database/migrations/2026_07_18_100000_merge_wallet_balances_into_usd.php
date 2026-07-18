<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Gộp ví VNĐ (balance) + ví USD (balance_usd) thành 1 số dư USD duy nhất (đỡ rắc rối cho khách —
// không còn phải chọn "ví nào" khi thanh toán). Đồng thời chuẩn hoá luôn các giao dịch "purchase"
// lịch sử (trước đây ghi VNĐ) về USD để báo cáo doanh thu (SUM) không bị lẫn 2 đơn vị tiền tệ.
// Giao dịch loại "deposit" giữ nguyên currency gốc (VD: chuyển khoản ngân hàng vẫn ghi đúng số
// VNĐ đã chuyển — đó là bằng chứng giao dịch thật, không phải số dư ví).
return new class extends Migration
{
    public function up(): void
    {
        $rate = \App\Helpers\CurrencyHelper::rate('USD');

        DB::table('users')->orderBy('id')->chunkById(200, function ($users) use ($rate) {
            foreach ($users as $user) {
                $convertedFromVnd = round($user->balance * $rate, 2);
                $newBalance = round($user->balance_usd + $convertedFromVnd, 2);
                DB::table('users')->where('id', $user->id)->update(['balance' => $newBalance]);
            }
        });

        DB::table('transactions')->where('type', 'purchase')->where('currency', '!=', 'USD')
            ->orderBy('id')->chunkById(500, function ($rows) use ($rate) {
                foreach ($rows as $row) {
                    DB::table('transactions')->where('id', $row->id)->update([
                        'amount' => round($row->amount * $rate, 2),
                        'currency' => 'USD',
                    ]);
                }
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance_usd');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance_usd', 12, 2)->default(0)->after('balance');
        });

        // Không thể khôi phục chính xác số dư VNĐ/USD gốc đã gộp (lossy) — coi toàn bộ số dư
        // hiện có là ví VNĐ cũ, ví USD về 0, admin điều chỉnh tay nếu cần rollback thật.
    }
};
