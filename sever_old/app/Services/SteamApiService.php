<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SteamApiService
{
    /**
     * Lấy thông tin game từ Steam API và tự động lưu Cache 24h
     */
    public function getGameDetails($steamAppId)
    {
        if (!$steamAppId) return null;

        // Lưu cache 24 tiếng (86400 giây) để không bị Steam block và tăng tốc load
        return Cache::remember('steam_game_' . $steamAppId, 86400, function () use ($steamAppId) {
            try {
                $response = Http::timeout(10)->get('https://store.steampowered.com/api/appdetails', [
                    'appids' => $steamAppId,
                    'l' => 'vietnamese', // Ưu tiên tiếng Việt nếu có
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data[$steamAppId]['success']) && $data[$steamAppId]['success']) {
                        $gameData = $data[$steamAppId]['data'];
                        
                        $priceNumeric = 0;
                        if (isset($gameData['price_overview']['final'])) {
                            // Steam lưu giá kèm 2 số 0 ở cuối cho VND (giống cent)
                            $priceNumeric = $gameData['price_overview']['final'] / 100;
                        }
                        
                        return [
                            'name' => $gameData['name'] ?? 'Unknown',
                            'description' => $gameData['short_description'] ?? '',
                            'detailed_description' => $gameData['detailed_description'] ?? $gameData['about_the_game'] ?? '',
                            'header_image' => $gameData['header_image'] ?? null,
                            'screenshots' => collect($gameData['screenshots'] ?? [])->pluck('path_full')->toArray(),
                            'price_formatted' => $gameData['price_overview']['final_formatted'] ?? 'Free',
                            'price_numeric' => $priceNumeric,
                            'genres' => collect($gameData['genres'] ?? [])->pluck('description')->toArray(),
                            'categories' => collect($gameData['categories'] ?? [])->pluck('description')->toArray(),
                            'developers' => $gameData['developers'] ?? [],
                            'publishers' => $gameData['publishers'] ?? [],
                            'release_date' => $gameData['release_date']['date'] ?? 'Đang cập nhật',
                            'pc_requirements' => $gameData['pc_requirements'] ?? [],
                            'steam_url' => 'https://store.steampowered.com/app/' . $steamAppId
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors and return null
            }
            return null;
        });
    }
}
