{{-- Bài viết tin tức chỉ có sẵn tiếng Việt (cào từ GameHub) — dùng widget Google Translate miễn phí
     (không cần API key/billing) để tự dịch sang đúng ngôn ngữ site đang chọn (session locale), ẩn
     hết giao diện mặc định xấu của Google, chỉ giữ lại nội dung đã dịch. Chỉ nhúng khi locale hiện
     tại khác 'vi' — nếu đang xem bằng tiếng Việt thì không cần dịch gì cả. --}}
@php
    // Mã ngôn ngữ Google Translate không hoàn toàn khớp mã locale của site (vd zh phải là zh-CN,
    // pt-BR Google Translate không nhận biến thể vùng nên dùng chung 'pt').
    $googleTranslateCodeMap = [
        'zh' => 'zh-CN', 'pt-BR' => 'pt',
    ];
    // Ngôn ngữ gốc của nội dung: tin tức cào từ GameHub là tiếng Việt, bài Hướng dẫn
    // (import từ softwarepays) là tiếng Anh — truyền $autoTranslateSourceLang khi include.
    $autoTranslateSourceLang = $autoTranslateSourceLang ?? 'vi';
    $currentLocale = app()->getLocale();
    $googleTranslateTarget = $googleTranslateCodeMap[$currentLocale] ?? $currentLocale;
@endphp
@if($currentLocale !== $autoTranslateSourceLang)
<div id="google_translate_element" style="display:none;"></div>
<style>
    .goog-te-banner-frame, .goog-te-gadget-icon { display: none !important; }
    body { top: 0 !important; }
    .skiptranslate iframe { display: none !important; }
</style>
<script>
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({ pageLanguage: {{ Js::from($autoTranslateSourceLang) }}, autoDisplay: false }, 'google_translate_element');

        var target = {{ Js::from($googleTranslateTarget) }};
        var tries = 0;
        var interval = setInterval(function () {
            var select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = target;
                select.dispatchEvent(new Event('change'));
                clearInterval(interval);
            }
            if (++tries > 40) clearInterval(interval);
        }, 250);
    }
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
@endif
