<?php

namespace App\Console\Commands;

use App\Modules\Theme\Models\Product;
use Illuminate\Console\Command;

class SeedExtraCatalogs extends Command
{
    protected $signature = 'seed:extra-catalogs';
    protected $description = 'Tạo sản phẩm mẫu cho Gói đăng ký / Phần mềm / Thẻ quà tặng — dùng chung cơ chế giao Key tự động (Kinguin) đã có sẵn cho Steam Wallet';

    protected const CATALOGS = [
        Product::TYPE_SUBSCRIPTION => [
            ['id' => 'sub_netflix_1m', 'name' => 'Netflix Premium 1 Tháng', 'price' => 89000, 'description' => 'Netflix là dịch vụ xem phim, series, chương trình truyền hình trực tuyến hàng đầu thế giới với kho nội dung khổng lồ, cập nhật liên tục các bộ phim, series độc quyền Netflix Original. Gói Premium cho phép xem chất lượng Ultra HD 4K, phát trên 4 thiết bị cùng lúc, không quảng cáo.'],
            ['id' => 'sub_netflix_3m', 'name' => 'Netflix Premium 3 Tháng', 'price' => 249000, 'description' => 'Netflix là dịch vụ xem phim, series, chương trình truyền hình trực tuyến hàng đầu thế giới với kho nội dung khổng lồ, cập nhật liên tục các bộ phim, series độc quyền Netflix Original. Gói Premium cho phép xem chất lượng Ultra HD 4K, phát trên 4 thiết bị cùng lúc, không quảng cáo.'],
            ['id' => 'sub_spotify_1m', 'name' => 'Spotify Premium 1 Tháng', 'price' => 59000, 'description' => 'Spotify Premium là gói nghe nhạc trực tuyến không quảng cáo với hơn 100 triệu bài hát, podcast từ Spotify. Nghe nhạc chất lượng cao, tải nhạc nghe offline, tua bài không giới hạn và phát nhạc liên tục không bị gián đoạn bởi quảng cáo.'],
            ['id' => 'sub_youtube_1m', 'name' => 'YouTube Premium 1 Tháng', 'price' => 49000, 'description' => 'YouTube Premium mang lại trải nghiệm xem video không quảng cáo trên toàn bộ YouTube, cho phép phát video ở chế độ nền (tắt màn hình vẫn nghe được) và tải video xem offline. Gói này còn bao gồm luôn YouTube Music Premium.'],
            ['id' => 'sub_office365_1y', 'name' => 'Microsoft 365 Personal 1 Năm', 'price' => 590000, 'description' => 'Microsoft 365 Personal bao gồm trọn bộ ứng dụng văn phòng Word, Excel, PowerPoint, Outlook bản quyền đầy đủ, luôn cập nhật phiên bản mới nhất, kèm 1TB lưu trữ đám mây OneDrive, dùng được trên PC, Mac, điện thoại và máy tính bảng.'],
            ['id' => 'sub_canva_1y', 'name' => 'Canva Pro 1 Năm', 'price' => 350000, 'description' => 'Canva Pro mở khóa toàn bộ thư viện hàng triệu mẫu thiết kế, ảnh, font chữ và yếu tố đồ họa cao cấp, công cụ xóa nền ảnh một chạm, thay đổi kích thước thiết kế tức thì và không gian lưu trữ 1TB cho các dự án thiết kế.'],
        ],
        Product::TYPE_SOFTWARE => [
            ['id' => 'sw_windows11_pro', 'name' => 'Windows 11 Pro (Key bản quyền)', 'price' => 490000, 'description' => 'Windows 11 Pro là hệ điều hành mới nhất của Microsoft với giao diện hiện đại, hiệu năng tối ưu và các tính năng dành cho doanh nghiệp như BitLocker mã hóa ổ đĩa, Hyper-V ảo hóa, Remote Desktop và quản lý domain. Key kích hoạt bản quyền chính hãng vĩnh viễn.'],
            ['id' => 'sw_office2021', 'name' => 'Microsoft Office 2021 Pro Plus', 'price' => 690000, 'description' => 'Microsoft Office 2021 Professional Plus bao gồm Word, Excel, PowerPoint, Outlook, Access, Publisher bản quyền vĩnh viễn (mua 1 lần dùng mãi mãi, không cần gia hạn hàng năm như Microsoft 365), cài đặt trên 1 máy tính Windows.'],
            ['id' => 'sw_winrar', 'name' => 'WinRAR License', 'price' => 190000, 'description' => 'WinRAR là phần mềm nén/giải nén file phổ biến nhất, hỗ trợ định dạng RAR, ZIP và nhiều định dạng nén khác, cho phép tạo file nén có mật khẩu bảo vệ, chia nhỏ file dung lượng lớn. Bản quyền vĩnh viễn, không còn hiện thông báo nhắc mua.'],
            ['id' => 'sw_idm', 'name' => 'Internet Download Manager (IDM) License', 'price' => 150000, 'description' => 'Internet Download Manager (IDM) là phần mềm tăng tốc độ tải file lên đến 5 lần bằng công nghệ chia nhỏ file thông minh, tự động tiếp tục tải khi mất kết nối, tải video trực tiếp từ trình duyệt. Bản quyền vĩnh viễn kèm cập nhật phiên bản mới.'],
            ['id' => 'sw_kaspersky_1y', 'name' => 'Kaspersky Total Security 1 Năm', 'price' => 350000, 'description' => 'Kaspersky Total Security bảo vệ toàn diện máy tính khỏi virus, mã độc, ransomware theo thời gian thực, kèm tường lửa, quản lý mật khẩu, kiểm soát của phụ huynh và bảo vệ giao dịch ngân hàng trực tuyến an toàn.'],
        ],
        Product::TYPE_GIFTCARD => [
            ['id' => 'gc_google_play_100', 'name' => 'Thẻ Google Play 100,000đ', 'price' => 105000],
            ['id' => 'gc_google_play_200', 'name' => 'Thẻ Google Play 200,000đ', 'price' => 208000],
            ['id' => 'gc_apple_200', 'name' => 'Thẻ Apple Store & iTunes 200,000đ', 'price' => 210000],
            ['id' => 'gc_amazon_10usd', 'name' => 'Thẻ quà tặng Amazon 10 USD', 'price' => 270000],
            ['id' => 'gc_psn_10usd', 'name' => 'Thẻ PlayStation Store 10 USD', 'price' => 265000],
            ['id' => 'gc_xbox_10usd', 'name' => 'Thẻ Xbox Gift Card 10 USD', 'price' => 265000],
        ],
    ];

    public function handle()
    {
        $count = 0;
        foreach (self::CATALOGS as $type => $items) {
            foreach ($items as $item) {
                Product::updateOrCreate(
                    ['wholesale_product_id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'product_type' => $type,
                        'price' => $item['price'],
                        'original_price' => $item['price'],
                        'description' => $item['description'] ?? null,
                        'is_active' => true,
                        'seo_title' => $item['name'] . ' - Giao ngay tự động',
                        'seo_description' => 'Mua ' . $item['name'] . ' giá tốt, giao mã tự động ngay sau khi thanh toán.',
                    ]
                );
                $count++;
            }
        }

        $this->info("Đã tạo {$count} sản phẩm Gói đăng ký/Phần mềm/Thẻ quà tặng.");
        return self::SUCCESS;
    }
}
