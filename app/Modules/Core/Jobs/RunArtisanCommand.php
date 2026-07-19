<?php

namespace App\Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

// Chạy 1 lệnh artisan ở nền qua queue thay vì Artisan::call() thẳng trong request HTTP.
// Container prod chạy `php artisan serve` (single-threaded, không phải php-fpm/nginx thật),
// nên gọi đồng bộ trong request sẽ treo TOÀN BỘ site cho tới khi lệnh chạy xong — đã gây lỗi
// 504 thật khi admin bấm nút Crawl (crawl chạy vài phút chặn hết mọi request khác).
class RunArtisanCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Các lệnh crawl --all có thể chạy nhiều chục phút qua nhiều danh mục.
    public int $timeout = 1800;

    public function __construct(protected string $command, protected array $parameters = [])
    {
    }

    public function handle(): void
    {
        try {
            Artisan::call($this->command, $this->parameters);
            Log::info("RunArtisanCommand: '{$this->command}' completed", ['output' => Artisan::output()]);
        } catch (\Throwable $e) {
            Log::error("RunArtisanCommand: '{$this->command}' failed: " . $e->getMessage());
        }
    }
}
