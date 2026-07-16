<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Theme\Models\Product;

class SeedMassiveGames extends Command
{
    // Đổi mặc định thành 150 game mỗi lần chạy
    protected $signature = 'steam:seed-massive {count=150}';
    protected $description = 'Seed a massive amount of real Steam games quickly using GetAppList & AppDetails';

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
        
        // Lấy danh sách ID đã có để tránh trùng lặp
        $existingAppIds = Product::whereNotNull('steam_app_id')->pluck('steam_app_id')->toArray();
        $existingSet = array_flip($existingAppIds);

        $this->info("Đang xử lý và lấy thông tin chi tiết từ Steam...");
        $bar = $this->output->createProgressBar($targetCount);

        for ($page = 0; $page <= 100; $page++) {
            if ($insertedCount >= $targetCount) {
                break;
            }

            // Gọi SteamSpy để lấy danh sách Game (mỗi page 1000 game)
            $response = Http::withoutVerifying()->timeout(30)->get("https://steamspy.com/api.php?request=all&page={$page}");
            
            if (!$response->successful()) {
                $this->warn("Lỗi khi lấy trang $page từ SteamSpy. Đang bỏ qua...");
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

                $name = mb_substr(trim($app['name']), 0, 150);

                // Bỏ qua nếu không có tên hoặc game đã tồn tại trong DB
                if (empty($name) || isset($existingSet[$appId])) {
                    continue;
                }
                
                // --- XỬ LÝ LẤY NỘI DUNG CHI TIẾT TỪ STEAM API ---
                $description = "Đây là thông tin mô tả chi tiết của tựa game $name. Một siêu phẩm không thể bỏ qua!";
                $steamData = [];
                $realPriceVnd = null;
                $realOriginalPriceVnd = null;
                
                try {
                    $steamApiUrl = "https://store.steampowered.com/api/appdetails?appids={$appId}&l=vietnamese&cc=vn";
                    $detailResponse = Http::timeout(10)->withHeaders([
                        'User-Agent' => 'Mozilla/5.0'
                    ])->get($steamApiUrl);

                    if ($detailResponse->successful()) {
                        $detailData = $detailResponse->json();
                        if (isset($detailData[$appId]['success']) && $detailData[$appId]['success'] === true) {
                            $appData = $detailData[$appId]['data'];
                            
                            $desc = $appData['about_the_game'] ?? '';
                            if (empty($desc)) {
                                $desc = $appData['short_description'] ?? '';
                            }

                            $pc_req = '';
                            if (isset($appData['pc_requirements']['minimum'])) {
                                $pc_req .= "<h3>Cấu hình tối thiểu:</h3><br>" . $appData['pc_requirements']['minimum'];
                            }
                            if (isset($appData['pc_requirements']['recommended'])) {
                                $pc_req .= "<h3>Cấu hình đề nghị:</h3><br>" . $appData['pc_requirements']['recommended'];
                            }

                            if (!empty($desc)) {
                                $description = $desc;
                                if (!empty($pc_req)) {
                                    $description .= "<br><hr><br>" . $pc_req;
                                }
                            }
                            
                            // Lấy thêm các thông tin mở rộng để hiển thị giao diện cực xịn
                            $screenshots = [];
                            if (isset($appData['screenshots']) && is_array($appData['screenshots'])) {
                                foreach ($appData['screenshots'] as $ss) {
                                    if (isset($ss['path_full'])) {
                                        $screenshots[] = $ss['path_full'];
                                    }
                                }
                            }
                            
                            // Thể loại thật từ Steam
                            $realGenres = [];
                            if (isset($appData['genres']) && is_array($appData['genres'])) {
                                foreach ($appData['genres'] as $g) {
                                    if (isset($g['description'])) {
                                        $realGenres[] = $g['description'];
                                    }
                                }
                            }
                            
                            // Giá thật từ Steam (VND)
                            if (isset($appData['price_overview'])) {
                                $realPriceVnd = $appData['price_overview']['final'] / 100;
                                $realOriginalPriceVnd = $appData['price_overview']['initial'] / 100;
                            } elseif (isset($appData['is_free']) && $appData['is_free'] === true) {
                                $realPriceVnd = 0;
                                $realOriginalPriceVnd = 0;
                            }

                            $steamData = [
                                'detailed_description' => $appData['detailed_description'] ?? '',
                                'screenshots' => array_slice($screenshots, 0, 8), // Lấy tối đa 8 ảnh cho đẹp
                                'release_date' => $appData['release_date']['date'] ?? 'Đang cập nhật',
                                'publishers' => $appData['publishers'] ?? [],
                                'developers' => $appData['developers'] ?? [],
                                'pc_requirements' => $appData['pc_requirements'] ?? [],
                                'genres' => $realGenres
                            ];
                        }
                    }
                    // Ngủ 150ms để không bị Steam khóa IP
                    usleep(150000);
                } catch (\Exception $e) {
                    // Lỗi mạng hoặc bị khóa, giữ nguyên mô tả giả
                }
                // ------------------------------------------------

                // Xử lý Giá
                if ($realPriceVnd !== null) {
                    $priceVnd = $realPriceVnd;
                    $originalPrice = $realOriginalPriceVnd ?? $realPriceVnd;
                } else {
                    $priceCents = isset($app['price']) ? (int)$app['price'] : 0;
                    if ($priceCents == 0) {
                        $priceVnd = rand(10, 300) * 5000;
                    } else {
                        $priceVnd = $priceCents * 250;
                    }
                    $hasDiscount = rand(1, 100) <= 30;
                    $originalPrice = $hasDiscount ? $priceVnd * (1 + (rand(1, 5) * 0.1)) : $priceVnd;
                }

                // Xử lý Thể loại
                shuffle($genresList);
                $gameGenres = array_slice($genresList, 0, rand(1, 4));

                // Độ hot
                $popularity = isset($app['positive']) ? (int)$app['positive'] : 0;

                $batch[] = [
                    'name' => $name,
                    'steam_app_id' => $appId,
                    'price' => $priceVnd,
                    'original_price' => $originalPrice,
                    'description' => $description,
                    'steam_data' => json_encode($steamData),
                    'header_image' => "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/$appId/header.jpg",
                    'is_active' => true,
                    'genres' => json_encode(!empty($realGenres) ? $realGenres : $gameGenres),
                    'popularity' => $popularity,
                    'seo_title' => mb_substr($name . ' - Bản quyền', 0, 190),
                    'seo_description' => mb_substr("Mua ngay $name với giá tốt nhất.", 0, 190),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $existingSet[$appId] = true;
                $insertedCount++;
                $bar->advance();

                if (count($batch) >= 150) {
                    DB::table('products')->insert($batch);
                    $batch = [];
                }
            }
        }

        if (count($batch) > 0) {
            DB::table('products')->insert($batch);
        }

        $bar->finish();
        $this->info("\n");
        $this->info("Hoàn tất! Đã thêm thành công $insertedCount games (kèm mô tả chuẩn) vào cửa hàng.");

        $totalGames = Product::count();
        \Illuminate\Support\Facades\Log::info("CronJob Steam Sync (150 games): Chạy hoàn tất. Đã thêm mới {$insertedCount} games. Tổng kho game hiện tại: {$totalGames} games.");

        return Command::SUCCESS;
    }
}
