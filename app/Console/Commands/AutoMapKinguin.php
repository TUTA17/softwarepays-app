<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Theme\Models\Product;
use Illuminate\Support\Facades\Http;
use App\Modules\Core\Models\Setting;

class AutoMapKinguin extends Command
{
    protected $signature = 'kinguin:auto-map';
    protected $description = 'Automatically map all unmapped Steam games to Kinguin IDs';

    public function handle()
    {
        $apiUrl = Setting::where('name', 'wholesale_api_endpoint')->value('value');
        $apiKey = Setting::where('name', 'wholesale_api_key')->value('value');
        
        if (!$apiUrl || !$apiKey) {
            $this->error('Missing API Endpoint or API Key.');
            return 1;
        }
        
        $apiUrl = rtrim($apiUrl, '/');
        
        $products = Product::whereNull('wholesale_product_id')
            ->whereNotNull('steam_app_id')
            ->orderBy('updated_at', 'asc') // Ưu tiên quét các game lâu chưa quét
            ->get();

        $total = $products->count();
        if ($total == 0) {
            $this->info("All products are already mapped!");
            return 0;
        }

        $this->info("Found $total products to map. Starting...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $mapped = 0;
        $failed = 0;

        foreach ($products as $p) {
            $searchName = trim($p->name);
            
            try {
                $response = Http::timeout(5)->withHeaders([
                    'X-Api-Key' => $apiKey
                ])->get($apiUrl . '/api/v1/products', [
                    'name' => $searchName,
                    'limit' => 5
                ]);
                
                if ($response->successful()) {
                    $results = $response->json('results') ?? $response->json('items');
                    if (!$results && is_array($response->json()) && isset($response->json()[0]['productId'])) {
                        $results = $response->json();
                    }

                    if (!empty($results) && isset($results[0]['productId'])) {
                        $p->wholesale_product_id = $results[0]['productId'];
                        $p->save();
                        $mapped++;
                    } else {
                        $p->touch(); // Đẩy xuống cuối hàng đợi
                        $failed++;
                    }
                } else {
                    $p->touch();
                    $failed++;
                }
            } catch (\Exception $e) {
                $p->touch();
                $failed++;
            }
            
            $bar->advance();
            
            // Ngủ 1 giây để tránh Rate Limit của Kinguin API
            sleep(1);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! Mapped: $mapped, Failed/Not Found: $failed.");
        return 0;
    }
}
