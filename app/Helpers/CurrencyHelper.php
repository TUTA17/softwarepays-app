<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyHelper
{
    protected static $cachedRates = null;

    protected static function loadRates()
    {
        if (self::$cachedRates === null) {
            self::$cachedRates = Cache::remember('exchange_rates_vnd', 60 * 60 * 12, function () {
                try {
                    $response = Http::timeout(2)->get('https://open.er-api.com/v6/latest/VND');
                    if ($response->successful()) {
                        return $response->json()['rates'];
                    }
                } catch (\Exception $e) {
                    // Ignore and use fallback
                }
                return [
                    'VND' => 1,
                    'USD' => 1/25000,
                    'CNY' => 1/3500,
                    'JPY' => 1/170,
                    'KRW' => 1/19,
                    'THB' => 1/700,
                    'RUB' => 1/270,
                    'EUR' => 1/27000,
                    'MYR' => 1/5600,
                ];
            });
        }
        return self::$cachedRates;
    }

    // Danh sách các locale được thanh toán qua PayPal bằng tiền tệ địa phương (PayPal hỗ trợ);
    // các locale còn lại (VND không phải tiền PayPal, RUB bị PayPal chặn từ 2022, v.v.) mặc định về USD.
    // MYR đã thử trực tiếp qua API PayPal live và bị từ chối (CURRENCY_NOT_SUPPORTED) -> không đưa vào danh sách.
    public const PAYPAL_LOCALE_CURRENCY = [
        'de' => 'EUR', 'fr' => 'EUR', 'it' => 'EUR', 'es' => 'EUR', 'pt' => 'EUR',
        'ja' => 'JPY', 'th' => 'THB',
    ];

    // Các tiền tệ trong bộ đổi tiền tệ của site (VND/USD/CNY/JPY/KRW/THB/RUB) mà merchant PayPal này
    // THỰC SỰ chấp nhận — đã kiểm tra trực tiếp qua API live: CNY/KRW/VND bị từ chối (CURRENCY_NOT_SUPPORTED),
    // RUB tuy được chấp nhận tạo đơn nhưng chủ động không bật do lệnh cấm vận Nga từ PayPal (2022), rủi ro capture thất bại.
    public const PAYPAL_SUPPORTED_CURRENCIES = ['USD', 'JPY', 'THB', 'EUR'];

    public static function paypalCurrencyForLocale(string $locale): string
    {
        return self::PAYPAL_LOCALE_CURRENCY[$locale] ?? 'USD';
    }

    // Ưu tiên đúng tiền tệ khách đang chọn ở bộ đổi tiền tệ của site (session('currency')) nếu PayPal hỗ trợ;
    // nếu không hỗ trợ (CNY/KRW/RUB/VND) thì rơi về ánh xạ theo ngôn ngữ, cuối cùng mặc định USD.
    public static function paypalCurrencyForSelection(string $sessionCurrency, string $locale): string
    {
        if (in_array($sessionCurrency, self::PAYPAL_SUPPORTED_CURRENCIES, true)) {
            return $sessionCurrency;
        }
        return self::paypalCurrencyForLocale($locale);
    }

    // % margin admin tự đặt (Cài đặt > Tỷ giá quy đổi), cộng thêm vào tỷ giá thị trường thực khi
    // BÁN cho khách (checkout/hiển thị giá) — tách biệt hoàn toàn với tỷ giá MUA (kinguin_eur_rate
    // ở trang Quản lý Game, dùng để quy đổi giá nhập EUR/USD từ nhà cung cấp về VNĐ khi đồng bộ sản phẩm).
    // Margin dương -> khách trả bằng ngoại tệ phải trả nhiều hơn tương ứng cho cùng 1 sản phẩm giá VNĐ.
    protected const MARGIN_PERCENT_SETTINGS = [
        'USD' => 'margin_percent_usd',
        'EUR' => 'margin_percent_eur',
    ];

    // Tỷ giá VND -> $currencyCode bất kỳ, đã cộng thêm % margin (nếu USD/EUR có cấu hình) trên nền tỷ giá thị trường thực.
    public static function rate(string $currencyCode): float
    {
        $rates = self::loadRates();
        $rate = $rates[$currencyCode] ?? (1/25000);

        if (isset(self::MARGIN_PERCENT_SETTINGS[$currencyCode])) {
            $marginPercent = (float) \App\Modules\Core\Models\Setting::getValue(self::MARGIN_PERCENT_SETTINGS[$currencyCode], 0);
            if ($marginPercent != 0) {
                $rate *= (1 + $marginPercent / 100);
            }
        }

        return $rate;
    }

    // Tỷ giá thị trường thực, CHƯA cộng % margin — dùng để hiển thị xem trước ở trang Cài đặt
    // (so sánh trước/sau khi cộng %), tách riêng khỏi rate() vốn luôn áp margin cho mọi nơi khác.
    public static function rateWithoutMargin(string $currencyCode): float
    {
        $rates = self::loadRates();
        return $rates[$currencyCode] ?? (1/25000);
    }

    // Tỷ giá VND -> USD, dùng để tính phí phương thức quốc tế ở trang checkout
    public static function usdRate()
    {
        return self::rate('USD');
    }

    // Ví chỉ còn 1 số dư thật duy nhất, tính bằng USD (gộp lại từ ví VNĐ + ví USD trước đây để
    // đỡ rắc rối cho khách — nạp bằng kênh nào cũng quy đổi về cùng 1 số dư USD).
    public static function formatWalletBalance($balanceUsd): string
    {
        if (is_null($balanceUsd) || $balanceUsd === '') return '$0.00';

        return '$' . number_format($balanceUsd, 2);
    }

    public static function formatPrice($priceInVND)
    {
        if (is_null($priceInVND) || $priceInVND === '') return '';

        $currency = session('currency', 'VND');
        
        $symbols = [
            'VND' => 'đ',
            'USD' => '$',
            'EUR' => '€',
            'CNY' => '¥',
            'JPY' => '¥',
            'KRW' => '₩',
            'THB' => '฿',
            'RUB' => '₽'
        ];

        if ($currency === 'VND') {
            return number_format($priceInVND) . 'đ';
        }

        $rates = self::loadRates();
        $rate = $rates[$currency] ?? (1/25000); // mặc định rate USD nếu không tìm thấy
        $converted = $priceInVND * $rate;

        $symbol = $symbols[$currency] ?? $currency . ' ';

        return $symbol . number_format($converted, 2);
    }
}
