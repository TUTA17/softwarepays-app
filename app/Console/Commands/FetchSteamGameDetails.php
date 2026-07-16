<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Modules\Theme\Models\Product;

class FetchSteamGameDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:fetch-details {count=150}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lấy nội dung chi tiết (description) cho các game chưa có mô tả (chạy ngầm từng đợt để tránh bị block)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->argument('count');
        
        $this->info("Bắt đầu tìm kiếm $limit game chưa có mô tả chi tiết...");

        // Tìm các game thuộc Steam (có steam_app_id) và đang xài mô tả mẫu hoặc chưa có
        $games = Product::whereNotNull('steam_app_id')
            ->whereNull('wholesale_product_id')
            ->where(function($query) {
                $query->whereNull('description')
                      ->orWhere('description', 'like', '%Đây là thông tin mô tả chi tiết%')
                      ->orWhere('description', '');
            })
            ->orderBy('popularity', 'desc') // Ưu tiên cập nhật các game Hot nhất trước
            ->limit($limit)
            ->get();

        if ($games->isEmpty()) {
            $this->info("Tuyệt vời! Toàn bộ kho game đã được cập nhật mô tả chi tiết.");
            \Illuminate\Support\Facades\Log::info("CronJob Steam Details: Toàn bộ kho game đã có mô tả đầy đủ.");
            return Command::SUCCESS;
        }

        $this->info("Đã tìm thấy " . $games->count() . " game cần lấy dữ liệu. Bắt đầu tải...");
        
        $successCount = 0;
        $bar = $this->output->createProgressBar($games->count());

        foreach ($games as $game) {
            $appId = $game->steam_app_id;
            $url = "https://store.steampowered.com/api/appdetails?appids={$appId}&l=vietnamese";

            try {
                // Thêm timeout và giả lập trình duyệt để chống chặn
                $response = Http::timeout(10)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data[$appId]['success']) && $data[$appId]['success'] === true) {
                        $appData = $data[$appId]['data'];
                        
                        // Lấy mô tả Tiếng Việt (hoặc tiếng Anh nếu ko có)
                        $description = $appData['about_the_game'] ?? '';
                        
                        // Nếu vẫn rỗng thì thử lấy mô tả ngắn
                        if (empty($description)) {
                            $description = $appData['short_description'] ?? 'Chưa có thông tin mô tả chi tiết cho tựa game này.';
                        }

                        // Lấy thêm cấu hình hệ thống nếu có
                        $pc_req = '';
                        if (isset($appData['pc_requirements']['minimum'])) {
                            $pc_req .= "<h3>Cấu hình tối thiểu:</h3><br>" . $appData['pc_requirements']['minimum'];
                        }
                        if (isset($appData['pc_requirements']['recommended'])) {
                            $pc_req .= "<h3>Cấu hình đề nghị:</h3><br>" . $appData['pc_requirements']['recommended'];
                        }

                        // Ghép lại thành nội dung hoàn chỉnh
                        $finalDesc = $description;
                        if (!empty($pc_req)) {
                            $finalDesc .= "<br><hr><br>" . $pc_req;
                        }

                        $game->description = $finalDesc;
                        $game->save();
                        $successCount++;
                    } else {
                        // Game đã bị xóa khỏi cửa hàng Steam, đánh dấu để lần sau không cào lại nữa
                        $game->description = "Tựa game này không còn cung cấp thông tin trên cửa hàng Steam.";
                        $game->save();
                    }
                }

                // Nghỉ 200ms giữa mỗi lượt để tránh bị Steam khóa IP do spam request (429 Too Many Requests)
                usleep(200000); 

            } catch (\Exception $e) {
                // Lỗi mạng, bỏ qua chạy tiếp game sau
            }

            $bar->advance();
        }

        $bar->finish();
        
        $remaining = Product::whereNotNull('steam_app_id')
            ->whereNull('wholesale_product_id')
            ->where(function($query) {
                $query->whereNull('description')
                      ->orWhere('description', 'like', '%Đây là thông tin mô tả chi tiết%')
                      ->orWhere('description', '');
            })
            ->count();

        $this->info("\nHoàn tất! Cập nhật thành công $successCount game.");
        \Illuminate\Support\Facades\Log::info("CronJob Steam Details: Chạy hoàn tất. Cập nhật thành công {$successCount}/{$games->count()} game. Số game còn lại cần cập nhật: {$remaining}");

        return Command::SUCCESS;
    }
}
