<?php

namespace App\Console\Commands;

use App\Modules\Theme\Models\Product;
use App\Modules\Theme\Models\VpnPackage;
use App\Services\VpnPanelsService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncVpnCatalog extends Command
{
    protected $signature = 'sync:vpn-catalog';
    protected $description = 'Đồng bộ danh sách server + gói cước VPN từ VPN Panels API';

    public function handle(VpnPanelsService $vpn)
    {
        $result = $vpn->listServers();
        $servers = $result['servers'] ?? $result;

        if (empty($servers)) {
            $this->error('Không lấy được danh sách server từ VPN Panels API.');
            return self::FAILURE;
        }

        // Tỉ lệ lợi nhuận cấu hình trong Admin (giống hệt cơ chế wholesale_profit_margin cho game key
        // — xem WholesaleProviderService::getWholesalePrice()), mặc định 20% nếu chưa cấu hình.
        $marginSetting = \App\Modules\Core\Models\Setting::where('name', 'vpn_profit_margin')->value('value');
        $margin = is_numeric($marginSetting) && $marginSetting > 0 ? (float) $marginSetting : 20;

        // Tỷ giá USD -> VNĐ dùng để quy đổi giá gói (VPN Panels API trả bằng USD) — Admin tự chỉnh
        // ở trang Quản lý VPN, mặc định 25.000đ/$ nếu chưa cấu hình.
        $usdRateSetting = \App\Modules\Core\Models\Setting::where('name', 'vpn_usd_rate')->value('value');
        $usdRate = is_numeric($usdRateSetting) && $usdRateSetting > 0 ? (float) $usdRateSetting : 25000;

        $productCount = 0;
        $packageCount = 0;

        foreach ($servers as $server) {
            // Không set 'header_image' ở đây — VPN không có nguồn ảnh tự động, cột này chỉ do Admin
            // tự upload qua trang quản trị; bỏ qua khỏi mảng update để updateOrCreate không đụng tới,
            // giữ nguyên ảnh Admin đã upload qua các lần đồng bộ sau.
            $product = Product::updateOrCreate(
                ['vpn_server_id' => (string) $server['id']],
                [
                    'name' => strip_tags($server['name']),
                    'product_type' => Product::TYPE_VPN,
                    'description' => $server['description'] ?? null,
                    'is_active' => true,
                    'price' => 0, // giá thật lấy theo từng gói trong vpn_packages
                ]
            );
            $productCount++;

            $addData = is_string($server['add_data'] ?? null) ? json_decode($server['add_data'], true) : ($server['add_data'] ?? []);
            $packages = $addData['ontime_packages'] ?? [];

            foreach ($packages as $key => $pkg) {
                if (($pkg['hide'] ?? '0') === '1') continue;

                $month = (int) ($pkg['month'] ?? 0);
                $gig = (float) ($pkg['gig'] ?? 0);
                $label = str_replace(
                    ['{{CREDITS}}', '{{MONTH}}', '{{PRICE}}'],
                    ['', $month, $pkg['formattedPrice'] ?? ''],
                    $pkg['name'] ?? $key
                );
                $label = trim(preg_replace('/\s+/', ' ', strip_tags($label)));

                // "selectedPackage" gửi lên API add-order phải là pkg.ids (mã gói thật của VPN Panels),
                // không phải key object JS — key chỉ là index tạm, khớp vpnCatalogSync.service.js bên softwarepays.
                $packageKey = (string) ($pkg['ids'] ?? $key);

                VpnPackage::updateOrCreate(
                    ['product_id' => $product->id, 'package_key' => $packageKey],
                    [
                        'name' => $label ?: $key,
                        'months' => $month ?: null,
                        'gig' => $gig ?: null,
                        'price' => round(((float) $pkg['price']) * $usdRate * (1 + $margin / 100), -3),
                        'is_active' => true,
                    ]
                );
                $packageCount++;
            }
        }

        $this->info("Đã đồng bộ {$productCount} server VPN, {$packageCount} gói cước.");
        return self::SUCCESS;
    }
}
