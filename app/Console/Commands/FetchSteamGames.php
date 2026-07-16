<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Modules\Theme\Models\Product;
use App\Services\SteamApiService;
use Illuminate\Support\Str;

class FetchSteamGames extends Command
{
    protected $signature = 'steam:fetch-games';
    protected $description = 'Lấy danh sách top game thịnh hành từ SteamSpy và tự động lưu vào database';

    public function handle(SteamApiService $steamApiService)
    {
        $autoFetch = \App\Modules\Core\Models\Setting::where('name', 'auto_fetch_games')->value('value') ?? '1';
        if ($autoFetch === '0') {
            $this->info('Tính năng Tự động lấy Game đang bị tắt trong Cấu hình Admin. Bỏ qua.');
            return Command::SUCCESS;
        }

        $this->info('Đang lấy dữ liệu từ SteamSpy...');

        // Fetch top 100 in 2 weeks
        $response = Http::timeout(20)->get('https://steamspy.com/api.php?request=top100in2weeks');
        
        if (!$response->successful()) {
            $this->error('Không thể lấy dữ liệu từ SteamSpy.');
            return Command::FAILURE;
        }

        $games = $response->json();
        $count = 0;

        foreach ($games as $appId => $gameData) {
            // Skip free games (price == 0)
            if (isset($gameData['price']) && $gameData['price'] == 0) {
                continue;
            }

            // Check if game already exists
            $existingProduct = Product::where('steam_app_id', $appId)->first();

            $this->info("Đang lấy chi tiết cho game: {$gameData['name']} (AppID: $appId)");
            
            // Get rich details from Steam Store API
            $details = $steamApiService->getGameDetails($appId);
            
            if ($details && $details['price_numeric'] > 0) {
                
                // Get wholesale price logic
                $wholesaleService = app(\App\Services\WholesaleProviderService::class);
                $pricing = $wholesaleService->getWholesalePrice($appId);
                
                if ($existingProduct) {
                    $existingProduct->update([
                        'price' => $pricing['selling_price'],
                        'original_price' => $pricing['original_price'],
                        'description' => $details['detailed_description'],
                        'header_image' => $details['header_image'],
                    ]);
                    $this->info("=> Cập nhật giá cho game đã có: {$details['name']}");
                } else {
                    Product::create([
                        'name' => $details['name'],
                        'steam_app_id' => $appId,
                        'price' => $pricing['selling_price'],
                        'original_price' => $pricing['original_price'],
                        'description' => $details['detailed_description'],
                        'header_image' => $details['header_image'],
                        'is_active' => true,
                        'genres' => json_encode($details['genres'] ?? []),
                        'seo_title' => $details['name'] . ' - Bản quyền giá rẻ',
                        'seo_description' => Str::limit(strip_tags($details['description']), 160)
                    ]);
                    $this->info("=> Đã thêm game mới: {$details['name']}");
                }
                
                $count++;
                
                // Sleep to avoid rate limiting from Steam API (200 requests/5 min)
                usleep(500000); // 0.5s
            }
        }

        $this->info("Đã cập nhật thành công $count game mới vào hệ thống!");
        return Command::SUCCESS;
    }
}
