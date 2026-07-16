<?php

namespace App\Jobs;

use App\Modules\Theme\Models\GameKey;
use App\Services\EsimAccessService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PollEsimStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Khớp với chu kỳ polling bên softwarepays: 5s,10s,20s,35s,55s kể từ lúc đặt hàng.
    protected const RETRY_DELAYS = [5, 10, 20, 35, 55];

    public function __construct(public int $gameKeyId, public int $attempt = 0)
    {
    }

    public function handle(EsimAccessService $esim): void
    {
        $item = GameKey::find($this->gameKeyId);
        if (!$item || $item->status !== 'processing') {
            return;
        }

        $orderNo = $item->delivery_data['order_no'] ?? null;
        if (!$orderNo) {
            $item->update(['status' => 'failed', 'error_message' => 'Thiếu order_no']);
            return;
        }

        $ready = $esim->checkReady($orderNo);

        if ($ready) {
            $item->update([
                'status' => 'sold',
                'key_code' => $ready['activation_code'],
                'delivery_data' => array_merge($item->delivery_data ?? [], $ready),
            ]);
            return;
        }

        if ($this->attempt >= count(self::RETRY_DELAYS)) {
            // Hết lượt thử tự động — vẫn giữ status=processing, khách/admin có thể refresh thủ công sau.
            return;
        }

        self::dispatch($this->gameKeyId, $this->attempt + 1)->delay(now()->addSeconds(self::RETRY_DELAYS[$this->attempt]));
    }
}
