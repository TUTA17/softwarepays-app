<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocaleService
{
    // Quốc gia -> [locale, currency] — chỉ liệt kê những nước site có ĐỦ CẢ ngôn ngữ lẫn tiền tệ tương ứng.
    // Các nước còn lại (không có trong danh sách) mặc định tiếng Anh + USD theo đúng yêu cầu.
    protected const COUNTRY_MAP = [
        'VN' => ['vi', 'VND'],
        'US' => ['en', 'USD'],
        'CN' => ['zh', 'CNY'],
        'JP' => ['ja', 'JPY'],
        'KR' => ['ko', 'KRW'],
        'TH' => ['th', 'THB'],
        'RU' => ['ru', 'RUB'],
        'DE' => ['de', 'EUR'],
        'FR' => ['fr', 'EUR'],
        'IT' => ['it', 'EUR'],
        'ES' => ['es', 'EUR'],
        'PT' => ['pt', 'EUR'],
    ];

    protected const DEFAULT_LOCALE = 'en';
    protected const DEFAULT_CURRENCY = 'USD';

    // Trả về [locale, currency] dựa theo quốc gia của IP; mặc định [en, USD] nếu không xác định được
    // hoặc quốc gia đó không nằm trong danh sách hỗ trợ đủ cả ngôn ngữ lẫn tiền tệ.
    public function detect(?string $ip): array
    {
        $countryCode = $this->lookupCountryCode($ip);

        return self::COUNTRY_MAP[$countryCode] ?? [self::DEFAULT_LOCALE, self::DEFAULT_CURRENCY];
    }

    // Mã quốc gia thô theo IP (VD: "VN", "US"), dùng để tách hiển thị phương thức thanh toán
    // (nội địa+crypto cho VN, quốc tế cho các nước khác) — độc lập với locale/currency khách tự đổi tay.
    public function detectCountryCode(?string $ip): ?string
    {
        return $this->lookupCountryCode($ip);
    }

    protected function lookupCountryCode(?string $ip): ?string
    {
        if (!$ip || $this->isPrivateOrLocal($ip)) {
            return null;
        }

        return Cache::remember("geoip_country_{$ip}", 60 * 60 * 6, function () use ($ip) {
            try {
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,countryCode',
                ]);

                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json('countryCode');
                }
            } catch (\Exception $e) {
                Log::warning('GeoIP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            return null;
        });
    }

    protected function isPrivateOrLocal(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
