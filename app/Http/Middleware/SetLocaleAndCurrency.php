<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleAndCurrency
{
    public const SUPPORTED_LOCALES = [
        'vi', 'en', 'ru', 'de', 'fr', 'it', 'es', 'pt', 'pt-BR',
        'zh', 'ja', 'ko', 'th', 'lo', 'km', 'id', 'ms',
    ];

    public const LOCALE_LABELS = [
        'vi' => 'Tiếng Việt', 'en' => 'English', 'ru' => 'Русский', 'de' => 'Deutsch',
        'fr' => 'Français', 'it' => 'Italiano', 'es' => 'Español', 'pt' => 'Português',
        'pt-BR' => 'Português (BR)', 'zh' => '中文', 'ja' => '日本語', 'ko' => '한국어',
        'th' => 'ไทย', 'lo' => 'ລາວ', 'km' => 'ខ្មែរ', 'id' => 'Bahasa Indonesia', 'ms' => 'Bahasa Melayu',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Lần đầu ghé site (chưa có gì trong session) -> tự nhận diện quốc gia qua IP để chọn sẵn
        // ngôn ngữ + tiền tệ phù hợp; nước không nằm trong danh sách hỗ trợ đủ cả 2 thì mặc định English + USD.
        if (!session()->has('locale')) {
            [$geoLocale, $geoCurrency] = app(\App\Services\GeoLocaleService::class)->detect($request->ip());
            session(['locale' => $geoLocale, 'currency' => $geoCurrency]);
        }

        $locale = session('locale');
        if ($locale && in_array($locale, self::SUPPORTED_LOCALES)) {
            App::setLocale($locale);
        } else {
            // Default locale
            App::setLocale('vi');
            session(['locale' => 'vi']);
        }

        if (!session()->has('currency')) {
            session(['currency' => 'VND']);
        }

        return $next($request);
    }
}
