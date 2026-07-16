<?php

namespace App\Console\Commands;

use App\Modules\Theme\Models\CardPackage;
use App\Modules\Theme\Models\Product;
use Illuminate\Console\Command;

class SeedCardCatalog extends Command
{
    protected $signature = 'seed:card-catalog';
    protected $description = 'Tạo danh mục Thẻ nạp điện thoại & Thẻ game (Santhecao) — danh sách tĩnh, không có API discovery';

    // Danh sách nhà cung cấp thẻ cào santhecao.com — không có API liệt kê catalog,
    // đây là danh sách được biên soạn thủ công từ tài liệu API/trang giá công khai,
    // khớp 1:1 với config/cardProviders.js bên softwarepays (mã telco + % chiết khấu
    // santhecao cho theo từng mệnh giá).
    protected const PROVIDERS = [
        ['name' => 'Thẻ cào Viettel', 'telco' => 'VTT', 'denoms' => [
            10000 => 0, 20000 => 0, 30000 => 0, 50000 => 1, 100000 => 1, 200000 => 1, 300000 => 1, 500000 => 1,
        ]],
        ['name' => 'Thẻ cào Mobifone', 'telco' => 'VMS', 'denoms' => [
            10000 => 0, 20000 => 0, 30000 => 0, 50000 => 1, 100000 => 1, 200000 => 1, 300000 => 1, 500000 => 1,
        ]],
        ['name' => 'Thẻ cào Vinaphone', 'telco' => 'VNP', 'denoms' => [
            10000 => 0, 20000 => 0, 30000 => 0, 50000 => 1, 100000 => 1, 200000 => 1, 300000 => 1, 500000 => 1,
        ]],
        ['name' => 'Thẻ cào Vietnamobile', 'telco' => 'VNM', 'denoms' => [
            10000 => 0, 20000 => 0, 30000 => 0, 50000 => 1.3, 100000 => 1.3, 200000 => 1.3, 300000 => 1.3, 500000 => 1.3,
        ]],
        ['name' => 'Thẻ Garena', 'telco' => 'GAR', 'denoms' => [
            10000 => 0, 20000 => 0, 50000 => 0.2, 100000 => 0.2, 200000 => 0.2, 500000 => 0.2,
        ]],
        ['name' => 'Thẻ Zing', 'telco' => 'ZING', 'denoms' => [
            14000 => 3.5, 20000 => 3.5, 28000 => 3.5, 42000 => 3.5, 56000 => 3.5, 84000 => 3.5,
        ]],
        ['name' => 'Thẻ Gate', 'telco' => 'GATE', 'denoms' => [
            10000 => 0, 20000 => 0, 50000 => 1.8, 100000 => 1.8, 200000 => 1.8, 500000 => 1.8,
        ]],
        ['name' => 'Thẻ Vcoin (VTC)', 'telco' => 'VTC', 'denoms' => [
            10000 => 0, 20000 => 0, 50000 => 0.9, 100000 => 0.9, 200000 => 0.9, 500000 => 0.9,
        ]],
    ];

    // Không có API/CDN logo chính thức cho các nhà mạng/game này, dùng favicon thật của website
    // chính thức từng hãng qua Google favicon service (dịch vụ công khai, luôn trả ảnh thật của
    // đúng domain, không phải ảnh dựng sẵn) — đã verify từng domain trả HTTP 200 + image/png.
    protected const LOGO_DOMAINS = [
        'VTT' => 'viettel.com.vn',
        'VMS' => 'mobifone.vn',
        'VNP' => 'vinaphone.com.vn',
        'VNM' => 'vietnamobile.com.vn',
        'GAR' => 'garena.vn',
        'ZING' => 'zingmp3.vn',
        'GATE' => 'gate.vn',
        'VTC' => 'vtcgame.vn',
    ];

    public function handle()
    {
        $count = 0;
        $packageCount = 0;
        foreach (self::PROVIDERS as $p) {
            $faceValues = array_keys($p['denoms']);

            // Giá hiển thị (min/max) tính theo giá bán thực (đã trừ % chiết khấu santhecao cho),
            // không phải theo mệnh giá gốc — khớp cách softwarepays tính costPriceVnd.
            $sellPrices = array_map(fn ($fv) => $this->sellPrice($fv, $p['denoms'][$fv]), $faceValues);

            $existing = Product::where('product_type', Product::TYPE_CARD)
                ->where('wholesale_product_id', 'card_' . $p['telco'])->first();

            $attrs = [
                'name' => $p['name'],
                'description' => $p['name'] . ' — nạp mã tự động, giao ngay sau khi thanh toán.',
                'is_active' => true,
                'price' => min($sellPrices),
                'original_price' => max($faceValues),
            ];

            // Bảo vệ ảnh admin đã tự upload qua /admin/card — chỉ gán favicon tự động khi
            // sản phẩm mới tạo hoặc chưa từng có header_image.
            if ((!$existing || !$existing->header_image) && isset(self::LOGO_DOMAINS[$p['telco']])) {
                $attrs['header_image'] = 'https://www.google.com/s2/favicons?sz=128&domain=' . self::LOGO_DOMAINS[$p['telco']];
            }

            $product = Product::updateOrCreate(
                ['product_type' => Product::TYPE_CARD, 'wholesale_product_id' => 'card_' . $p['telco']],
                $attrs
            );
            $count++;

            foreach ($p['denoms'] as $faceValue => $discountPercent) {
                $basePrice = $this->sellPrice($faceValue, $discountPercent);

                // % khuyến mãi admin đặt riêng cho từng mệnh giá (qua /admin/card/{id}/packages) tính
                // thẳng trên mệnh giá thật (face_value) — không cộng dồn với chiết khấu vendor santhecao
                // (discount_percent) đã có sẵn trong $basePrice, tránh sai lệch % Admin nhập so với %
                // thật hiển thị cho khách. Không ghi đè promo_discount_percent vì đó là giá trị admin
                // tự chỉnh, chỉ đọc để tính lại "price" mỗi lần chạy sync.
                $existingPkg = CardPackage::where('product_id', $product->id)->where('face_value', $faceValue)->first();
                $promo = $existingPkg->promo_discount_percent ?? null;
                $finalPrice = ($promo && $promo > 0)
                    ? (int) (ceil($faceValue * (1 - $promo / 100) / 1000) * 1000)
                    : $basePrice;

                CardPackage::updateOrCreate(
                    ['product_id' => $product->id, 'face_value' => $faceValue],
                    [
                        'discount_percent' => $discountPercent,
                        'original_price' => $basePrice,
                        'price' => $finalPrice,
                        'is_active' => true,
                    ]
                );
                $packageCount++;
            }
        }

        $this->info("Đã tạo {$count} nhà cung cấp thẻ nạp/thẻ game, {$packageCount} mệnh giá.");
        return self::SUCCESS;
    }

    // Khớp công thức bên softwarepays (cardCatalog.service.js): giá bán = giá vốn santhecao
    // chiết khấu cho theo mệnh giá, cộng thêm % lợi nhuận admin đặt ở /admin/card (mặc định 0%),
    // làm tròn lên bội số 1.000đ.
    protected function sellPrice(int $faceValue, float $discountPercent): int
    {
        $marginSetting = \App\Modules\Core\Models\Setting::getValue('card_profit_margin', '');
        $margin = is_numeric($marginSetting) && $marginSetting > 0 ? (float) $marginSetting : 0;

        $costPrice = $faceValue * (1 - $discountPercent / 100);
        $sellPrice = $costPrice * (1 + $margin / 100);
        return (int) (ceil($sellPrice / 1000) * 1000);
    }
}
