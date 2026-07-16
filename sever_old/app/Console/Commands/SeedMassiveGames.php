<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Client\Models\Product;

class SeedMassiveGames extends Command
{
    protected $signature = 'steam:seed-massive {count=20000}';
    protected $description = 'Seed a massive amount of real Steam games quickly using GetAppList';

    public function handle()
    {
        $targetCount = (int) $this->argument('count');
        $this->info("Bắt đầu lấy dữ liệu từ SteamSpy (Mục tiêu: $targetCount games)...");

        $genresList = [
            'Hành Động', 'Nhập Vai', 'Chiến Thuật', 'Phiêu Lưu', 'Thể Thao', 
            'Đua Xe', 'Sinh Tồn', 'Kinh Dị', 'Thế Giới Mở', 'Bắn Súng', 
            'Giải Đố', 'Mô Phỏng', 'Nhiều Người Chơi'
        ];

        $insertedCount = 0;
        $batch = [];
        $now = now();
        
        // Get existing app IDs to avoid duplicates
        $existingAppIds = Product::pluck('steam_app_id')->toArray();
        $existingSet = array_flip($existingAppIds);

        $this->info("Đang xử lý và chèn dữ liệu...");
        $bar = $this->output->createProgressBar($targetCount);

        for ($page = 0; $page <= 25; $page++) {
            if ($insertedCount >= $targetCount) {
                break;
            }

            $response = Http::withoutVerifying()->timeout(30)->get("https://steamspy.com/api.php?request=all&page={$page}");
            
            if (!$response->successful()) {
                $this->warn("Lỗi khi lấy trang $page. Đang bỏ qua...");
                continue;
            }

            $apps = $response->json();
            
            if (empty($apps)) {
                break;
            }

            foreach ($apps as $appId => $app) {
                if ($insertedCount >= $targetCount) {
                    break;
                }

                $name = trim($app['name']);

                // Skip empty names or already existing
                if (empty($name) || isset($existingSet[$appId])) {
                    continue;
                }
                
                // SteamSpy price is in cents. E.g. 999 = $9.99
                $priceCents = isset($app['price']) ? (int)$app['price'] : 0;
                
                // If price is 0, skip or mock
                if ($priceCents == 0) {
                    $priceVnd = rand(10, 300) * 5000;
                } else {
                    $priceVnd = $priceCents * 250; // $1 = 25000 VND
                }

                $hasDiscount = rand(1, 100) <= 30; // 30% chance to have discount
                $originalPrice = $hasDiscount ? $priceVnd * (1 + (rand(1, 5) * 0.1)) : $priceVnd;

                // Generate 1-4 random genres
                shuffle($genresList);
                $gameGenres = array_slice($genresList, 0, rand(1, 4));

                $batch[] = [
                    'name' => $name,
                    'steam_app_id' => $appId,
                    'price' => $priceVnd,
                    'original_price' => $originalPrice,
                    'description' => "Đây là thông tin mô tả chi tiết của tựa game $name. Một siêu phẩm không thể bỏ qua!",
                    'header_image' => "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/$appId/header.jpg",
                    'is_active' => true,
                    'genres' => json_encode($gameGenres),
                    'seo_title' => $name . ' - Bản quyền giá rẻ',
                    'seo_description' => "Mua ngay $name với giá tốt nhất thị trường.",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $existingSet[$appId] = true;
                $insertedCount++;
                $bar->advance();

                // Insert in chunks of 500
                if (count($batch) >= 500) {
                    DB::table('products')->insert($batch);
                    $batch = [];
                }
            }
        }

        // Insert remaining
        if (count($batch) > 0) {
            DB::table('products')->insert($batch);
        }

        $bar->finish();
        $this->info("\n");
        $this->info("Hoàn tất! Đã thêm thành công $insertedCount games vào cửa hàng.");

        return Command::SUCCESS;
    }
}
