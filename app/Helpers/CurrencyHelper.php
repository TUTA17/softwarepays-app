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

    // Tỷ giá VND -> $currencyCode bất kỳ
    public static function rate(string $currencyCode): float
    {
        $rates = self::loadRates();
        return $rates[$currencyCode] ?? (1/25000);
    }

    // Tỷ giá VND -> USD, dùng để tính phí phương thức quốc tế ở trang checkout
    public static function usdRate()
    {
        return self::rate('USD');
    }

    // Số dư Ví luôn hiển thị kèm VNĐ + $ (chỉ 2 loại này, không đổi theo tiền tệ site đang chọn) để khách
    // dễ đối chiếu, vì nạp qua PayPal/crypto luôn quy đổi qua $ trước khi cộng vào ví (gốc VNĐ).
    public static function formatWalletBalance($priceInVND): string
    {
        if (is_null($priceInVND) || $priceInVND === '') return '';

        $usd = $priceInVND * self::rate('USD');

        return number_format($priceInVND) . 'đ <span class="opacity-70 text-[0.85em]">(~$' . number_format($usd, 2) . ')</span>';
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
