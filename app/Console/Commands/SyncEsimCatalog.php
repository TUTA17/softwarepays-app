<?php

namespace App\Console\Commands;

use App\Modules\Theme\Models\EsimPackage;
use App\Modules\Theme\Models\Product;
use App\Services\EsimAccessService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncEsimCatalog extends Command
{
    protected $signature = 'sync:esim-catalog';
    protected $description = 'Đồng bộ danh sách gói eSIM du lịch từ eSIM Access API (1 sản phẩm = 1 điểm đến/nhóm điểm đến)';

    // eSIM Access trả giá theo USD * 10000 (đơn vị nội bộ của họ), quy đổi tạm 1 USD ~ 25000 VNĐ
    protected const USD_SCALE = 10000;
    protected const USD_TO_VND = 25000;

    public function handle(EsimAccessService $esim)
    {
        $locationsResult = $esim->listLocations();
        $packagesResult = $esim->listPackages();

        if (empty($packagesResult['packageList'])) {
            $this->error('Không lấy được danh sách gói eSIM.');
            return self::FAILURE;
        }

        $locationNames = collect($locationsResult['locationList'] ?? [])->pluck('name', 'code');

        $grouped = collect($packagesResult['packageList'])->groupBy('location');

        // Tỉ lệ lợi nhuận cấu hình trong Admin (giống hệt cơ chế wholesale_profit_margin cho game key),
        // mặc định 20% nếu chưa cấu hình.
        $marginSetting = \App\Modules\Core\Models\Setting::where('name', 'esim_profit_margin')->value('value');
        $margin = is_numeric($marginSetting) && $marginSetting > 0 ? (float) $marginSetting : 20;

        $productCount = 0;
        $packageCount = 0;

        foreach ($grouped as $locationKey => $packages) {
            $codes = explode(',', $locationKey);
            // Ảnh cờ quốc gia thật cho điểm đến đơn lẻ, lấy đúng mã ISO trả về từ API (vd "JP", "TH") —
            // trước đây mã này chỉ dùng tạm để tra tên hiển thị rồi bị bỏ đi, không lưu lại nên card
            // luôn hiện icon chung. Nhóm nhiều nước (gói đa quốc gia) thì không có 1 lá cờ đại diện được.
            if (count($codes) === 1) {
                $name = $locationNames->get($codes[0], $codes[0]) . ' - eSIM Du Lịch';
                $autoHeaderImage = 'https://flagcdn.com/w160/' . strtolower($codes[0]) . '.png';
            } else {
                $preview = implode(', ', array_slice($codes, 0, 4));
                $name = 'eSIM Đa Quốc Gia (' . count($codes) . ' nước: ' . $preview . '...)';
                $autoHeaderImage = null;
            }

            $wholesaleProductId = 'esim_' . md5($locationKey);
            $existing = Product::where('product_type', Product::TYPE_ESIM)
                ->where('wholesale_product_id', $wholesaleProductId)->first();

            $attrs = [
                'name' => $name,
                'is_active' => true,
                'price' => 0, // giá thật lấy theo từng gói trong esim_packages
            ];
            // Chỉ tự set cờ khi sản phẩm chưa có ảnh nào — nếu Admin đã tự upload ảnh riêng qua trang
            // quản trị thì giữ nguyên, không để lần đồng bộ sau ghi đè mất.
            if (!$existing || !$existing->header_image) {
                $attrs['header_image'] = $autoHeaderImage;
            }

            $product = Product::updateOrCreate(
                ['product_type' => Product::TYPE_ESIM, 'wholesale_product_id' => $wholesaleProductId],
                $attrs
            );
            $productCount++;

            foreach ($packages as $pkg) {
                EsimPackage::updateOrCreate(
                    ['package_code' => $pkg['packageCode']],
                    [
                        'product_id' => $product->id,
                        'name' => $pkg['name'],
                        'data_volume_bytes' => $pkg['volume'],
                        'duration' => $pkg['duration'],
                        'duration_unit' => $pkg['durationUnit'] ?? 'DAY',
                        'location' => Str::limit($locationKey, 490, ''),
                        'price' => round(($pkg['price'] / self::USD_SCALE) * self::USD_TO_VND * (1 + $margin / 100), -3),
                        'is_active' => true,
                    ]
                );
                $packageCount++;
            }
        }

        $this->info("Đã đồng bộ {$productCount} điểm đến eSIM, {$packageCount} gói data.");
        return self::SUCCESS;
    }
}
