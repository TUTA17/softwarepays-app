<?php

namespace App\Console\Commands;

use App\Modules\Theme\Models\Transaction;
use Illuminate\Console\Command;

class ExpirePendingTransactions extends Command
{
    /**
     * Ngưỡng thời gian chờ trước khi tự động đánh dấu "thất bại" nếu vẫn còn "Chờ xử lý",
     * theo cổng thanh toán (crypto cần chờ lâu hơn vì có thể vướng xác nhận mạng blockchain).
     */
    protected const EXPIRY_HOURS_DEFAULT = 3;
    protected const EXPIRY_HOURS_CRYPTO = 24;

    protected $signature = 'transactions:expire-pending';

    protected $description = 'Đánh dấu các giao dịch nạp tiền đang "Chờ xử lý" quá lâu (khách bỏ ngang PayPal/Crypto/chuyển khoản) thành "Thất bại"';

    public function handle(): int
    {
        $cryptoExpired = Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(self::EXPIRY_HOURS_CRYPTO))
            ->whereJsonContains('metadata->gateway', 'nowpayments')
            ->get();

        $otherExpired = Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(self::EXPIRY_HOURS_DEFAULT))
            ->where(function ($q) {
                $q->whereNull('metadata')
                    ->orWhereJsonDoesntContain('metadata->gateway', 'nowpayments');
            })
            ->get();

        $expired = $cryptoExpired->merge($otherExpired);

        foreach ($expired as $transaction) {
            $transaction->update(['status' => 'failed']);
        }

        $this->info('Đã đánh dấu thất bại: ' . $expired->count() . ' giao dịch quá hạn chờ xử lý.');

        return self::SUCCESS;
    }
}
