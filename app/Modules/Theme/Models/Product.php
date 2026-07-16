<?php

namespace App\Modules\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Sản phẩm dạng key đơn (Kinguin) mặc định product_type=null tương đương 'game'.
    // giftcard/subscription/software dùng chung cơ chế key đơn này, chỉ khác nhãn hiển thị.
    public const TYPE_GAME = 'game';
    public const TYPE_GIFTCARD = 'giftcard';
    public const TYPE_SUBSCRIPTION = 'subscription';
    public const TYPE_SOFTWARE = 'software';
    public const TYPE_VPN = 'vpn';
    public const TYPE_ESIM = 'esim';
    public const TYPE_CARD = 'card';
    public const TYPE_SMM = 'smm';

    protected $fillable = ['name', 'steam_app_id', 'wholesale_product_id', 'kinguin_reference_price_eur', 'kinguin_original_price_eur', 'kinguin_platform', 'kinguin_brand', 'product_type', 'vpn_server_id', 'price', 'seo_title', 'seo_description', 'is_active', 'description', 'genres', 'original_price', 'header_image', 'aliases', 'popularity', 'steam_data'];

    protected $casts = [
        'steam_data' => 'array',
    ];

    public function keys()
    {
        return $this->hasMany(GameKey::class);
    }

    public function vpnPackages()
    {
        return $this->hasMany(VpnPackage::class);
    }

    public function esimPackages()
    {
        return $this->hasMany(EsimPackage::class);
    }

    public function cardPackages()
    {
        return $this->hasMany(CardPackage::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Icon nền tảng thật theo kinguin_platform (đã verify sống từ DB: 30 giá trị thật đang có,
    // vd Xbox, PlayStation, Ubisoft, Battle.net...) — trước đây UI hardcode icon Steam cho MỌI
    // sản phẩm, sai với phần lớn game không phải Steam. Chỉ dùng icon đã xác nhận có trong FA 6.0.0
    // (bản site đang pin), còn lại rơi về icon chìa khóa chung.
    protected const PLATFORM_ICON_MAP = [
        'Steam' => 'fa-brands fa-steam',
        'Xbox' => 'fa-brands fa-xbox', 'Xbox One' => 'fa-brands fa-xbox',
        'Xbox 360' => 'fa-brands fa-xbox', 'Xbox Series X|S' => 'fa-brands fa-xbox',
        'PlayStation' => 'fa-brands fa-playstation', 'PlayStation 3' => 'fa-brands fa-playstation',
        'PlayStation 4' => 'fa-brands fa-playstation', 'PlayStation 5' => 'fa-brands fa-playstation',
        'Android' => 'fa-brands fa-android',
        'iOS' => 'fa-brands fa-apple',
        'MS Store (PC)' => 'fa-brands fa-windows', 'PC' => 'fa-brands fa-windows',
        'Battle.net' => 'fa-brands fa-battle-net',
        'Meta Quest' => 'fa-solid fa-vr-cardboard', 'Meta Quest 2' => 'fa-solid fa-vr-cardboard',
        'Official Website' => 'fa-solid fa-globe',
    ];

    public static function platformIcon(?string $platform): string
    {
        if (!$platform) return 'fa-solid fa-gamepad';
        return self::PLATFORM_ICON_MAP[$platform] ?? 'fa-solid fa-key';
    }

    // Nhãn nền tảng hiển thị thật — giftcard Steam Wallet không có kinguin_platform riêng
    // (nguồn khác Kinguin games) nhưng thực sự là Steam nên suy ra từ tên; các loại sản
    // phẩm khác (subscription/software/vpn/esim/card) không có khái niệm "nền tảng" game.
    public function platformDisplayLabel(): ?string
    {
        if ($this->kinguin_platform) return $this->kinguin_platform;
        if ($this->product_type === self::TYPE_GIFTCARD && str_contains($this->name, 'Steam Wallet')) return 'Steam';
        return null;
    }

    public function getAvailableKeysAttribute()
    {
        $count = $this->keys()->whereNull('sold_to_user_id')->count();
        if ($count > 0) {
            return $count;
        }

        // Check auto-provisioning setting (Mock mode or Real API Key configured)
        $mockMode = \App\Modules\Core\Models\Setting::where('name', 'wholesale_mock_mode')->value('value') ?? '1';
        $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
        
        if ($mockMode === '1') {
            return 999;
        }
        
        if (!empty($apiKey) && !empty($this->wholesale_product_id) && strpos($this->wholesale_product_id, 'kinguin_mock_') === false) {
            return 999;
        }

        return 0;
    }
}
