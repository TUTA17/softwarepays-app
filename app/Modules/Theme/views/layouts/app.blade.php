@php
    $sysSettings = \App\Modules\Core\Models\Setting::getAllGrouped();
    $genSettings = $sysSettings['general_tab'] ?? [];
    $siteName = $genSettings['name'] ?? 'SoftwarePays';
    $siteLogo = $genSettings['logo'] ?? null;
    $siteFavicon = $genSettings['favicon'] ?? null;
    $adminCodeSettings = $sysSettings['admin_setting_tab'] ?? [];
    $socialSettings = $sysSettings['social_tab'] ?? [];
    $supportZalo = $socialSettings['zalo'] ?: ($genSettings['hotline'] ?? null);
    $supportHotline = $genSettings['hotline'] ?? null;
    $seoSettings = $sysSettings['seo_tab'] ?? [];
    $defaultMetaDescription = $seoSettings['default_meta_description'] ?? __('header.default_meta_description');
    $googleAnalyticsId = $seoSettings['google_analytics_id'] ?? null;
    
    // Locale hiện tại đã được middleware SetLocaleAndCurrency thiết lập từ session
    $currentLang = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteName . ' - ' . __('header.title_tagline'))</title>
    <meta name="description" content="@yield('meta_description', $defaultMetaDescription)">
    @if($siteFavicon)
    <link rel="icon" type="image/png" href="{{ asset($siteFavicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Be Vietnam Pro"', 'sans-serif'],
                        display: ['"Be Vietnam Pro"', 'sans-serif'],
                    },
                    colors: {
                        gaming: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                            accent: '#3b82f6',
                            hover: '#60a5fa',
                            steam: '#171a21',
                            steamLight: '#2a475e',
                            green: '#4c6b22',
                            greenLight: '#a4d007',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        /* Hide Google Translate Elements */
        body { top: 0 !important; overflow-x: hidden; }
        .skiptranslate iframe { display: none !important; }
        #goog-gt-tt { display: none !important; }
        .goog-te-spinner-pos { display: none !important; }

        .glass-nav {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .dark .glass-nav {
            background-color: #0f172a;
            border-bottom: 1px solid #1e293b;
        }

        .glass-card {
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .glass-card {
            background-color: rgba(30, 41, 59, 0.65);
            border: 1px solid rgba(51, 65, 85, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .glass-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .dark .glass-card:hover {
            border-color: #475569;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
        }

        /* Modern Product Card CSS */
        .product-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }
        .dark .product-card {
            background: #1e293b;
            border-color: #334155;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0,0,0,0.15);
            border-color: #3b82f6;
        }
        .dark .product-card:hover {
            box-shadow: 0 12px 24px -10px rgba(0,0,0,0.5);
            border-color: #3b82f6;
        }
        .product-card-media {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
        }
        .product-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .product-card-media img {
            transform: scale(1.05);
        }
        .discount-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
            z-index: 10;
        }
        .discount-badge small {
            font-size: 0.6rem;
            font-weight: 500;
            opacity: 0.9;
        }
        .category-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 20px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
        }
        .dark .category-card {
            background: #1e293b;
            border-color: #334155;
        }
        .category-card:hover {
            transform: translateY(-4px);
            border-color: #3b82f6;
            box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.15);
        }
        .category-card img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .category-card:hover img {
            transform: scale(1.1);
        }
        .category-card span {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
        }
        .dark .category-card span {
            color: #e2e8f0;
        }

        .nav-link {
            position: relative;
            color: #475569;
            transition: color 0.2s ease;
        }
        .dark .nav-link {
            color: #cbd5e1;
        }

        .nav-link:hover, .dark .nav-link:hover {
            color: #3b82f6;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-primary-glow {
            background-color: #2563eb;
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary-glow:hover {
            background-color: #1d4ed8;
        }

        .btn-steam {
            background: linear-gradient(to right, #4c6b22 5%, #3d561b 95%);
            color: #d2efa9;
            transition: all 0.3s ease;
        }

        .btn-steam:hover {
            background: linear-gradient(to right, #8ed629 5%, #6aa31a 95%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(142, 214, 41, 0.3);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
    <script>
        // Check local storage for theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
    @stack('styles')
    @if(!empty($googleAnalyticsId))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalyticsId }}');
        </script>
    @endif
    @if(!empty($adminCodeSettings['admin_head_code']))
        {!! $adminCodeSettings['admin_head_code'] !!}
    @endif
</head>
<body class="antialiased min-h-screen flex flex-col bg-slate-50 dark:bg-[#0b0f19] text-slate-900 dark:text-slate-200 transition-colors duration-300 relative">
    
    <!-- Global Ambient Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-[-1] transition-opacity duration-700">
        <!-- Dark Mode Background Elements -->
        <div class="absolute inset-0 hidden dark:block">
            <!-- Glowing Orbs -->
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-600/20 mix-blend-screen filter blur-[120px] animate-[pulse_8s_ease-in-out_infinite]"></div>
            <div class="absolute top-[40%] -right-[10%] w-[40%] h-[60%] rounded-full bg-indigo-600/10 mix-blend-screen filter blur-[120px] animate-[pulse_10s_ease-in-out_infinite_alternate]"></div>
            <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[40%] rounded-full bg-purple-600/10 mix-blend-screen filter blur-[150px] animate-[pulse_12s_ease-in-out_infinite_alternate-reverse]"></div>
            <!-- Tech Grid Overlay -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>
        
        <!-- Light Mode Background Elements -->
        <div class="absolute inset-0 block dark:hidden">
            <!-- Glowing Orbs -->
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-400/30 mix-blend-multiply filter blur-[100px] animate-[pulse_8s_ease-in-out_infinite]"></div>
            <div class="absolute top-[40%] -right-[10%] w-[40%] h-[60%] rounded-full bg-indigo-300/30 mix-blend-multiply filter blur-[120px] animate-[pulse_10s_ease-in-out_infinite_alternate]"></div>
            <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[40%] rounded-full bg-purple-300/20 mix-blend-multiply filter blur-[150px] animate-[pulse_12s_ease-in-out_infinite_alternate-reverse]"></div>
            <!-- Tech Grid Overlay -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#000000 1px, transparent 1px), linear-gradient(90deg, #000000 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>
    </div>

    @if(isset($genSettings['demo_mode']) && $genSettings['demo_mode'] == '1')
    <!-- Demo Notice Banner -->
    <div class="bg-gradient-to-r from-rose-600 to-red-500 text-white px-4 py-2.5 text-center text-sm font-semibold z-[100] relative shadow-md flex items-center justify-center gap-2">
        <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
        <span>{{ __('header.demo_banner') }}</span>
    </div>
    @endif

    <!-- Header / Navbar -->
    <header class="glass-nav sticky top-0 z-50 w-full transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        @if($siteLogo)
                            <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" class="h-14 md:h-16 w-auto max-w-[200px] object-contain">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white transition-colors duration-300">
                                <i class="fa-solid fa-gamepad text-xl"></i>
                            </div>
                            <span class="font-display font-bold text-2xl tracking-wide text-slate-900 dark:text-white group-hover:text-blue-400 transition-colors ml-1">{{ mb_strtoupper($siteName) }}</span>
                        @endif
                    </a>

                    <nav class="hidden md:flex ml-4 lg:ml-8 items-center space-x-3 lg:space-x-5">
                        <!-- Dropdown Trang Chủ (Danh Mục) -->
                        <div class="relative group h-full flex items-center">
                            <a href="{{ route('home') }}" class="text-[13px] font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5 py-6 relative after:absolute after:-bottom-0.5 after:left-0 after:w-full after:h-0.5 after:bg-blue-600 dark:after:bg-blue-500 after:transition-transform after:origin-left {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 after:scale-x-100' : 'text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 after:scale-x-0 hover:after:scale-x-100' }}">
                                {{ __('nav.home') }} <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </a>
                            <div class="absolute left-0 top-full pt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden py-2 flex flex-col">
                                    <a href="{{ route('catalog.simple', 'qua-tang') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('catalog.simple') && request()->route('slug') === 'qua-tang' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors"><i class="fa-solid fa-gift w-4"></i> {{ __('home_categories.giftcards') }}</a>
                                    <a href="{{ route('catalog.simple', 'goi-dang-ky') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600 transition-colors"><i class="fa-solid fa-clipboard-check w-4"></i> {{ __('home_categories.subscriptions') }}</a>
                                    <a href="{{ route('catalog.simple', 'phan-mem') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600 transition-colors"><i class="fa-solid fa-desktop w-4"></i> {{ __('home_categories.software') }}</a>
                                    <a href="{{ route('catalog.esim') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600 transition-colors"><i class="fa-solid fa-sim-card w-4"></i> {{ __('home_categories.esim') }}</a>
                                    <a href="{{ route('sounds.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('sounds.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors"><i class="fa-solid fa-music w-4"></i> Sound World</a>
                                    <a href="{{ route('Gifs.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('Gifs.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors"><i class="fa-solid fa-images w-4"></i> GIF World</a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('catalog.card') }}" class="text-[13px] font-bold uppercase tracking-wider transition-colors whitespace-nowrap relative after:absolute after:-bottom-6 after:left-0 after:w-full after:h-0.5 after:bg-blue-600 dark:after:bg-blue-500 after:transition-transform after:origin-left {{ request()->routeIs('catalog.card*') ? 'text-blue-600 dark:text-blue-400 after:scale-x-100' : 'text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 after:scale-x-0 hover:after:scale-x-100' }}">
                            {{ __('nav.cards') }}
                        </a>
                        <a href="{{ route('shop') }}" class="text-[13px] font-bold uppercase tracking-wider transition-colors relative after:absolute after:-bottom-6 after:left-0 after:w-full after:h-0.5 after:bg-blue-600 dark:after:bg-blue-500 after:transition-transform after:origin-left {{ request()->routeIs('shop') ? 'text-blue-600 dark:text-blue-400 after:scale-x-100' : 'text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 after:scale-x-0 hover:after:scale-x-100' }}">
                            {{ __('nav.shop') }}
                        </a>
                        <a href="{{ route('smm.index') }}" class="text-[13px] font-bold uppercase tracking-wider transition-colors relative after:absolute after:-bottom-6 after:left-0 after:w-full after:h-0.5 after:bg-blue-600 dark:after:bg-blue-500 after:transition-transform after:origin-left {{ request()->routeIs('smm.*') ? 'text-blue-600 dark:text-blue-400 after:scale-x-100' : 'text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 after:scale-x-0 hover:after:scale-x-100' }}">
                            {{ __('DỊCH VỤ MXH') }}
                        </a>

                        <!-- Dropdown Khám Phá -->
                        <div class="relative group h-full flex items-center">
                            <button class="text-[13px] font-bold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 uppercase tracking-wider transition-colors flex items-center gap-1.5 py-6">
                                {{ __('KHÁM PHÁ') }} <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute left-0 top-full pt-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden py-2 flex flex-col">
                                    <a href="{{ route('blog.index') }}" class="px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('blog.index') || request()->routeIs('blog.show') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors">{{ __('nav.news') }}</a>
                                    <a href="{{ route('blog.guides') }}" class="px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('blog.guides') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors">{{ __('nav.guide') }}</a>
                                    <a href="{{ route('coupons.index') }}" class="px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('coupons.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors">{{ __('header.promotions') }}</a>
                                    <a href="{{ route('pages.support') }}" class="px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('pages.support') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-slate-700' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600' }} transition-colors">{{ __('header.support') }}</a>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    
                    @php
                        $currentCurrency = session('currency', 'VND');

                        // Khách nước ngoài là chính -> English và các ngôn ngữ phổ biến quốc tế xếp trước,
                        // Tiếng Việt xếp cuối thay vì ưu tiên đầu danh sách.
                        $languages = [
                            'en' => ['name' => 'English', 'flag' => 'us'],
                            'zh' => ['name' => '中文', 'flag' => 'cn'],
                            'es' => ['name' => 'Español', 'flag' => 'es'],
                            'fr' => ['name' => 'Français', 'flag' => 'fr'],
                            'de' => ['name' => 'Deutsch', 'flag' => 'de'],
                            'pt' => ['name' => 'Português', 'flag' => 'pt'],
                            'pt-BR' => ['name' => 'Português (BR)', 'flag' => 'br'],
                            'ru' => ['name' => 'Русский', 'flag' => 'ru'],
                            'ja' => ['name' => '日本語', 'flag' => 'jp'],
                            'ko' => ['name' => '한국어', 'flag' => 'kr'],
                            'it' => ['name' => 'Italiano', 'flag' => 'it'],
                            'id' => ['name' => 'Bahasa Indonesia', 'flag' => 'id'],
                            'th' => ['name' => 'ไทย', 'flag' => 'th'],
                            'ms' => ['name' => 'Bahasa Melayu', 'flag' => 'my'],
                            'vi' => ['name' => 'Tiếng Việt', 'flag' => 'vn'],
                            'lo' => ['name' => 'ລາວ', 'flag' => 'la'],
                            'km' => ['name' => 'ខ្មែរ', 'flag' => 'kh'],
                        ];

                        $currencies = [
                            'USD' => 'US Dollar',
                            'EUR' => 'Euro',
                            'JPY' => 'Japanese Yen',
                            'KRW' => 'South Korean Won',
                            'CNY' => 'Chinese Yuan',
                            'THB' => 'Thai Baht',
                            'RUB' => 'Russian Ruble',
                            'VND' => 'Vietnamese Dong',
                        ];
                    
                        $activeLang = $languages[$currentLang] ?? $languages['vi'];
                    @endphp
                    <!-- Language & Currency -->
                    <div class="relative group hidden lg:block z-50">
                        <button type="button" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm whitespace-nowrap">
                            <img src="https://flagcdn.com/w40/{{ $activeLang['flag'] }}.png" alt="{{ strtoupper($currentLang) }}" class="w-4 h-3 rounded-[2px] object-cover">
                            <span class="notranslate uppercase">{{ $currentCurrency }}</span>
                            <i class="fa-solid fa-chevron-down text-[9px] opacity-50 ml-1"></i>
                        </button>
                        
                        <div class="absolute right-0 top-full pt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden max-h-[80vh] overflow-y-auto custom-scrollbar">
                                <div class="p-3 border-b border-slate-100 dark:border-slate-700/50">
                                    <p class="text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-widest px-1">{{ __('header.language') }}</p>
                                    <div class="space-y-1">
                                        @foreach($languages as $code => $lang)
                                            <a href="{{ route('language.switch', $code) }}" class="w-full flex items-center justify-between px-3 py-2 rounded-lg {{ $currentLang === $code ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium' }} text-sm transition-colors">
                                                <div class="flex items-center gap-2"><img src="https://flagcdn.com/w40/{{ $lang['flag'] }}.png" alt="{{ strtoupper($code) }}" class="w-5 h-3.5 rounded-[2px] object-cover shadow-sm"> {{ $lang['name'] }}</div>
                                                @if($currentLang === $code)
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="p-3">
                                    <p class="text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-widest px-1">{{ __('header.currency') }}</p>
                                    <div class="space-y-1">
                                        @foreach($currencies as $code => $name)
                                            <a href="{{ route('currency.switch', $code) }}" class="w-full flex items-center justify-between px-3 py-2 rounded-lg {{ $currentCurrency === $code ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium' }} text-sm transition-colors">
                                                <span>{{ $code }} ({{ $name }})</span>
                                                @if($currentCurrency === $code)
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    <div class="relative group">
                        <a href="{{ route('cart.index') }}" class="relative w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" aria-label="{{ __('header.cart') }}">
                            <i class="fa-solid fa-cart-shopping"></i>
                            @if(session()->has('cart') && count(session('cart')) > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-slate-900">
                                    {{ count(session('cart')) }}
                                </span>
                            @endif
                        </a>

                        <!-- Mini Cart Dropdown -->
                        <div class="hidden sm:block absolute right-0 mt-0 pt-2 w-80 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                                    <h4 class="font-bold text-slate-900 dark:text-white">{{ __('cart.title') }}</h4>
                                    @if(session()->has('cart') && count(session('cart')) > 0)
                                        <span class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-1 rounded-md">{{ count(session('cart')) }} {{ __('header.cart_items_suffix') }}</span>
                                    @endif
                                </div>
                                <div class="p-2 max-h-[320px] overflow-y-auto">
                                    @if(session()->has('cart') && count(session('cart')) > 0)
                                        @foreach(array_slice(session('cart'), 0, 3, true) as $id => $item)
                                            <a href="{{ route('cart.index') }}" class="flex items-center gap-3 p-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors mb-1">
                                                <img src="{{ $item['image'] }}" alt="Cover" class="w-12 h-12 object-contain bg-white rounded-md shadow-sm">
                                                <div class="flex-1 min-w-0">
                                                    <h5 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item['name'] }}</h5>
                                                    <div class="text-xs text-slate-500 mt-1">{{ __('header.quantity_label') }}: {{ $item['quantity'] }}</div>
                                                </div>
                                                <div class="text-sm font-bold text-emerald-500">{!! \App\Helpers\CurrencyHelper::formatPrice($item['price']) !!}</div>
                                            </a>
                                        @endforeach
                                        @if(count(session('cart')) > 3)
                                            <div class="text-center py-2 text-xs text-slate-500 dark:text-slate-400 font-medium border-t border-slate-100 dark:border-slate-800 mt-2">
                                                <i class="fa-solid fa-plus text-[10px]"></i> {{ count(session('cart')) - 3 }} {{ __('header.cart_more_items_suffix') }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-8">
                                            <i class="fa-solid fa-cart-arrow-down text-4xl text-slate-200 dark:text-slate-700 mb-3"></i>
                                            <p class="text-sm font-medium text-slate-500">{{ __('header.cart_empty') }}</p>
                                        </div>
                                    @endif
                                </div>
                                @if(session()->has('cart') && count(session('cart')) > 0)
                                    <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                        <a href="{{ route('cart.index') }}" class="flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-600/20">
                                            {{ __('header.view_all') }} <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Search Toggle -->
                    <div class="relative hidden sm:block" id="global-search-container">
                        <button id="search-toggle-btn" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" aria-label="{{ __('header.search_aria') }}">
                            <i class="fa-solid fa-search"></i>
                        </button>
                        
                        <!-- Search Dropdown Box -->
                        <div id="search-dropdown" class="absolute right-0 top-12 w-[350px] opacity-0 invisible transition-all duration-200 z-50 transform translate-y-2">
                            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <form action="{{ route('shop') }}" method="GET" class="p-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2 bg-slate-50 dark:bg-slate-800/50">
                                    <i class="fa-solid fa-search text-slate-400 ml-2"></i>
                                    <input type="text" id="global-search-input" name="q" placeholder="{{ __('header.search_placeholder') }}" autocomplete="off" class="w-full bg-transparent border-none focus:ring-0 text-slate-900 dark:text-white text-sm py-2 px-1 outline-none">
                                    <button type="submit" class="hidden">{{ __('header.search_button') }}</button>
                                </form>
                                <div id="search-results" class="max-h-[350px] overflow-y-auto custom-scrollbar p-2 bg-white dark:bg-slate-900">
                                    <div class="text-center py-6 text-slate-500 text-sm" id="search-placeholder">
                                        <i class="fa-solid fa-gamepad text-2xl mb-2 opacity-50"></i><br>
                                        {{ __('header.search_hint') }}
                                    </div>
                                </div>
                                <div id="search-loading" class="hidden text-center py-6 text-slate-500 text-sm">
                                    <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2 text-blue-500"></i><br>
                                    {{ __('header.search_loading') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Search Button -->
                    <button onclick="document.getElementById('mobile-search-modal').classList.remove('hidden')" class="sm:hidden w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" aria-label="{{ __('header.mobile_search_aria') }}">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" aria-label="Toggle Theme">
                        <i class="fa-solid fa-moon dark:hidden"></i>
                        <i class="fa-solid fa-sun hidden dark:block"></i>
                    </button>
                    @auth
                        <div class="relative">
                            <div id="user-avatar-btn" onclick="toggleUserMenu(event)" class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-slate-50 dark:bg-slate-50/50 dark:bg-slate-800/50 transition-colors">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-blue-400 font-semibold"><i class="fa-solid fa-wallet mr-1"></i> {!! \App\Helpers\CurrencyHelper::formatWalletBalance(Auth::user()->balance) !!}</div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-700 to-slate-600 flex items-center justify-center text-slate-900 dark:text-white font-bold shadow-inner border border-slate-500 overflow-hidden">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:text-white transition-colors"></i>
                            </div>

                            <!-- Dropdown -->
                            <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-64 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg opacity-0 invisible transition-all duration-150 z-50">
                                <div class="p-3 border-b border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 sm:hidden">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-blue-400 font-semibold mt-1"><i class="fa-solid fa-wallet mr-1"></i> {!! \App\Helpers\CurrencyHelper::formatWalletBalance(Auth::user()->balance) !!}</div>
                                </div>
                                <div class="p-2 border-b border-slate-200 dark:border-slate-700/50">
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-blue-500/10 hover:border-blue-500/20 border border-transparent transition-all">
                                        <i class="fa-solid fa-gamepad w-5 text-center text-blue-500"></i> {{ __('dashboard.title') }}
                                    </a>
                                    <a href="{{ route('wallet.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-emerald-500/10 hover:border-emerald-500/20 border border-transparent transition-all mt-1">
                                        <i class="fa-solid fa-wallet w-5 text-center text-emerald-500"></i> {{ __('header.topup') }}
                                    </a>
                                </div>
                                <div class="p-2 border-b border-slate-200 dark:border-slate-700/50">
                                    <a href="{{ route('profile.transactions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-amber-500/10 hover:border-amber-500/20 border border-transparent transition-all">
                                        <i class="fa-solid fa-clock-rotate-left w-5 text-center text-amber-500"></i> {{ __('header.transaction_history') }}
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-500/10 hover:border-slate-500/20 border border-transparent transition-all mt-1">
                                        <i class="fa-solid fa-user-gear w-5 text-center text-slate-500"></i> {{ __('sidebar.account_settings') }}
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-rose-400 hover:text-rose-500 hover:bg-rose-500/10 hover:border-rose-500/20 border border-transparent transition-all font-semibold">
                                            <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> {{ __('header.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @else
                        <div class="hidden sm:flex items-center gap-2 lg:gap-3">
                                <a href="{{ route('login') }}" class="text-[13px] font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors px-2">
                                    {{ __('header.login') }}
                                </a>
                                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-bold transition-all shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/40 hover:-translate-y-0.5">
                                    {{ __('header.register') }}
                                </a>
                            </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Off-Canvas Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/60 z-[60] opacity-0 invisible transition-all duration-300 backdrop-blur-sm"></div>

        <!-- Mobile Off-Canvas Drawer -->
        <div id="mobile-menu-drawer" class="fixed top-0 left-0 w-[280px] h-full bg-white dark:bg-slate-900 z-[70] transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                <a href="{{ route('home') }}" class="font-display font-bold text-xl text-slate-900 dark:text-white">
                    {{ mb_strtoupper($siteName) }}
                </a>
                <button id="mobile-menu-close" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            @auth
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md overflow-hidden">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <div class="font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-semibold text-blue-500"><i class="fa-solid fa-wallet"></i> {!! \App\Helpers\CurrencyHelper::formatWalletBalance(Auth::user()->balance) !!}</div>
                </div>
            </div>
            @endauth

            <div class="flex-grow overflow-y-auto px-4 py-6 space-y-2 custom-scrollbar">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-house w-6 text-center text-lg"></i> {{ __('nav.home') }}
                </a>
                <a href="{{ route('shop') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('shop') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-store w-6 text-center text-lg"></i> {{ __('nav.shop') }}
                </a>
                <a href="{{ route('catalog.simple', 'qua-tang') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('catalog.simple') && request()->route('slug') === 'qua-tang' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-gift w-6 text-center text-lg"></i> {{ __('home_categories.giftcards') }}
                </a>
                <a href="{{ route('smm.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('smm.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-thumbs-up w-6 text-center text-lg"></i> {{ __('home_categories.smm') }}
                </a>
                <a href="{{ route('blog.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('blog.index') || request()->routeIs('blog.show') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-newspaper w-6 text-center text-lg"></i> {{ __('nav.news') }}
                </a>
                <a href="{{ route('sounds.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('sounds.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-music w-6 text-center text-lg"></i> Sound World
                </a>
                <a href="{{ route('Gifs.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('Gifs.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-images w-6 text-center text-lg"></i> GIF World
                </a>
                <a href="{{ route('blog.guides') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('blog.guides') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <i class="fa-solid fa-book w-6 text-center text-lg"></i> {{ __('nav.guide') }}
                </a>

                @auth
                <div class="h-px bg-slate-200 dark:bg-slate-800 my-4"></div>
                <div class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('header.account') }}</div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-gamepad w-6 text-center text-lg text-blue-500"></i> {{ __('dashboard.title') }}
                </a>
                <a href="{{ route('wallet.show') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-wallet w-6 text-center text-lg text-emerald-500"></i> {{ __('header.topup') }}
                </a>
                <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-gear w-6 text-center text-lg text-slate-500"></i> {{ __('header.settings_short') }}
                </a>
                @endauth
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                @guest
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full text-center py-3.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">{{ __('header.login') }}</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-3.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-600/30 transition-all">{{ __('header.register_account') }}</a>
                </div>
                @else
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 font-bold hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                        <i class="fa-solid fa-right-from-bracket"></i> {{ __('header.logout') }}
                    </button>
                </form>
                @endguest
            </div>
        </div>
        
        <!-- Mobile Search Modal -->
        <div id="mobile-search-modal" class="fixed inset-0 z-[80] bg-white dark:bg-slate-900 hidden flex-col">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3 shadow-sm">
                <button onclick="document.getElementById('mobile-search-modal').classList.add('hidden')" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </button>
                <form action="{{ route('shop') }}" method="GET" class="flex-grow flex items-center bg-slate-100 dark:bg-slate-800 rounded-full px-4 py-2 border border-transparent focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all">
                    <i class="fa-solid fa-search text-slate-400 mr-2"></i>
                    <input type="text" name="q" placeholder="{{ __('header.search_placeholder') }}" class="w-full bg-transparent border-none focus:ring-0 text-slate-900 dark:text-white py-1 px-1 outline-none font-medium" autofocus>
                </form>
            </div>
            <div class="p-8 text-center text-slate-500 flex-grow flex flex-col justify-center">
                <i class="fa-solid fa-gamepad text-5xl mb-4 opacity-20"></i>
                <p class="font-medium text-lg">{{ __('header.mobile_search_prompt') }}</p>
                <p class="text-sm opacity-70 mt-2">{{ __('header.mobile_search_hint') }}</p>
            </div>
        </div>

    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-menu-drawer');
            const overlay = document.getElementById('mobile-menu-overlay');
            const closeBtn = document.getElementById('mobile-menu-close');

            function openDrawer() {
                drawer.classList.remove('-translate-x-full');
                overlay.classList.remove('opacity-0', 'invisible');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            }

            function closeDrawer() {
                drawer.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0', 'invisible');
                document.body.style.overflow = '';
            }

            mobileBtn.addEventListener('click', openDrawer);
            closeBtn.addEventListener('click', closeDrawer);
            overlay.addEventListener('click', closeDrawer);
        });
    </script>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-16 pb-8 mt-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 mb-6">
                        @if($siteLogo)
                            <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" class="h-16 md:h-20 w-auto max-w-[250px] object-contain hover:scale-105 transition-transform">
                        @else
                            <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white">
                                <i class="fa-solid fa-gamepad"></i>
                            </div>
                            <span class="font-display font-bold text-xl text-slate-900 dark:text-white">{{ mb_strtoupper($siteName) }}</span>
                        @endif
                    </a>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        {{ __('footer.tagline') }}
                    </p>
                    <div class="flex space-x-4 mt-6">
                        @if(\App\Modules\Core\Models\Setting::getValue('facebook_link'))
                        <a href="{{ \App\Modules\Core\Models\Setting::getValue('facebook_link') }}" target="_blank" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-600 hover:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-facebook-f"></i></a>
                        @endif
                        @if(\App\Modules\Core\Models\Setting::getValue('telegram_link'))
                        <a href="{{ \App\Modules\Core\Models\Setting::getValue('telegram_link') }}" target="_blank" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-sky-500 hover:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-telegram"></i></a>
                        @endif
                        @if(\App\Modules\Core\Models\Setting::getValue('discord_link'))
                        <a href="{{ \App\Modules\Core\Models\Setting::getValue('discord_link') }}" target="_blank" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-indigo-600 hover:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-discord"></i></a>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-display font-semibold text-slate-900 dark:text-white text-lg mb-6">{{ __('footer.products_title') }}</h3>
                    <ul class="grid grid-cols-2 gap-x-4 gap-y-3">
                        <li><a href="{{ route('smm.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.smm') }}</a></li>
                        <li><a href="{{ route('shop') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.games') }}</a></li>
                        <li><a href="{{ route('catalog.simple', 'goi-dang-ky') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.subscriptions') }}</a></li>
                        <li><a href="{{ route('catalog.simple', 'phan-mem') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.software') }}</a></li>
                        <li><a href="{{ route('catalog.card') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.cards') }}</a></li>
                        <li><a href="{{ route('catalog.simple', 'qua-tang') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.giftcards') }}</a></li>
                        <li><a href="{{ route('catalog.esim') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('home_categories.esim') }}</a></li>
                        <li><a href="{{ route('sounds.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Sound World</a></li>
                        <li><a href="{{ route('Gifs.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> GIF World</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-display font-semibold text-slate-900 dark:text-white text-lg mb-6">{{ __('footer.support_title') }}</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('blog.show.guide', 'how-to-buy-on-softwarepays') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('footer.link_buying_guide') }}</a></li>
                        <li><a href="{{ route('blog.show.guide', 'how-to-activate-your-purchase') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('footer.link_activation_guide') }}</a></li>
                        <li><a href="{{ route('pages.warranty') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('footer.link_warranty_policy') }}</a></li>
                        <li><a href="{{ route('pages.support') }}#faq" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> {{ __('footer.link_faq') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-display font-semibold text-slate-900 dark:text-white text-lg mb-6">{{ __('footer.payment_title') }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">{{ __('footer.payment_desc') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-qrcode"></i> Bank Transfer (QR)</div>
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-brands fa-cc-visa"></i><i class="fa-brands fa-cc-mastercard"></i> Visa / Mastercard</div>
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-brands fa-paypal"></i> PayPal</div>
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-brands fa-bitcoin"></i> Crypto (BTC, ETH, USDT...)</div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} {{ $siteName }}. {{ __('footer.rights') }}
                </div>
                <div class="flex space-x-6 text-sm">
                    <a href="{{ route('pages.terms') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-300 transition-colors">{{ __('footer.policy_terms') }}</a>
                    <a href="{{ route('pages.privacy') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-300 transition-colors">{{ __('footer.policy_privacy') }}</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <!-- Purchase Success Toast -->
    @if(session('purchase_toast'))
    <div id="purchase-toast" class="fixed bottom-6 left-6 z-50 transform translate-y-20 opacity-0 transition-all duration-500 w-full max-w-[320px]">
        <div class="bg-slate-800 border border-slate-700 rounded-lg shadow-2xl flex items-center p-3 cursor-pointer hover:bg-slate-700 transition-colors">
            <img src="{{ session('purchase_toast')['image'] }}" alt="Game Cover" class="w-12 h-12 rounded bg-slate-900 object-contain border border-slate-700 shrink-0">
            <div class="ml-3 flex-1 text-white overflow-hidden">
                <div class="flex items-center justify-between mb-0.5">
                    <h4 class="font-bold text-sm truncate mr-2">{{ session('purchase_toast')['title'] }}</h4>
                    <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">{{ session('purchase_toast')['time'] }}</span>
                </div>
                <p class="text-xs text-emerald-400 font-medium flex items-center gap-1 truncate">
                    <i class="fa-solid fa-circle-check"></i> {{ session('purchase_toast')['message'] }}
                </p>
            </div>
            <button onclick="document.getElementById('purchase-toast').style.display='none'" class="ml-2 pl-2 border-l border-slate-700 text-slate-400 hover:text-white transition-colors h-8 flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('purchase-toast');
            if (toast) {
                // Animate In
                setTimeout(() => {
                    toast.classList.remove('translate-y-20', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                }, 500);

                // Auto hide after 5 seconds
                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-20', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 5500);
            }
        });
    </script>
    @endif

    <!-- Global Instant Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchBtn = document.getElementById('search-toggle-btn');
            const searchDropdown = document.getElementById('search-dropdown');
            const searchInput = document.getElementById('global-search-input');
            const searchResults = document.getElementById('search-results');
            const searchLoading = document.getElementById('search-loading');
            const searchPlaceholder = document.getElementById('search-placeholder');
            let searchTimeout = null;

            // Toggle Search Dropdown
            searchBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = searchDropdown.classList.contains('opacity-0');
                if (isHidden) {
                    searchDropdown.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                    searchInput.focus();
                } else {
                    searchDropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                    searchDropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
                }
            });

            // Prevent dropdown from closing when clicking inside
            searchDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Handle Input with Debounce
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                if (query.length === 0) {
                    searchResults.innerHTML = '';
                    searchResults.appendChild(searchPlaceholder);
                    searchLoading.classList.add('hidden');
                    searchResults.classList.remove('hidden');
                    return;
                }

                if (searchTimeout) clearTimeout(searchTimeout);
                
                searchPlaceholder.style.display = 'none';
                searchResults.classList.add('hidden');
                searchLoading.classList.remove('hidden');

                searchTimeout = setTimeout(() => {
                    fetch(`/api/search?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            searchLoading.classList.add('hidden');
                            searchResults.classList.remove('hidden');
                            searchResults.innerHTML = '';

                            if (data.length === 0) {
                                searchResults.innerHTML = `
                                    <div class="text-center py-6 text-slate-500 text-sm">
                                        <i class="fa-solid fa-ghost text-2xl mb-2 opacity-50"></i><br>
                                        {{ __('header.no_games_found') }}
                                    </div>`;
                                return;
                            }

                            data.forEach(item => {
                                let priceHtml = `<div class="text-sm font-bold text-emerald-500">${item.price}</div>`;
                                if (item.original_price) {
                                    priceHtml = `
                                        <div>
                                            <div class="text-[10px] text-slate-400 line-through">${item.original_price}</div>
                                            <div class="text-sm font-bold text-emerald-500">${item.price}</div>
                                        </div>
                                    `;
                                }

                                const html = `
                                    <a href="${item.url}" class="flex items-center gap-3 p-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors mb-1 group">
                                        <img src="${item.image}" alt="${item.name}" class="w-16 h-9 object-cover rounded shadow-sm border border-slate-200 dark:border-slate-700">
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-blue-500 transition-colors">${item.name}</h5>
                                        </div>
                                        ${priceHtml}
                                    </a>
                                `;
                                searchResults.insertAdjacentHTML('beforeend', html);
                            });

                            // Add View All link
                            searchResults.insertAdjacentHTML('beforeend', `
                                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <a href="/shop?q=${encodeURIComponent(query)}" class="block w-full text-center text-xs font-bold text-blue-500 hover:text-blue-600 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded transition-colors">
                                        {{ __('header.view_all_results') }}
                                    </a>
                                </div>
                            `);
                        })
                        .catch(err => {
                            searchLoading.classList.add('hidden');
                            searchResults.classList.remove('hidden');
                            searchResults.innerHTML = `<div class="text-center py-4 text-rose-500 text-xs">{{ __('header.search_connection_error') }}</div>`;
                        });
                }, 400); // 400ms debounce
            });
        });
    </script>

    <!-- Floating Contact Widget -->
    <div id="floating-contact" class="fixed bottom-6 right-6 z-[60] flex flex-col items-end gap-3">
        <!-- Contact Modal Panel -->
        <div id="fc-modal" class="absolute bottom-16 right-0 w-[320px] max-w-[90vw] rounded-2xl bg-slate-900 text-white shadow-2xl p-5 opacity-0 invisible scale-95 transition-all duration-300 transform origin-bottom-right pointer-events-none">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">{{ __('support.title') }}</h3>
                <button type="button" onclick="fcToggle()" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white transition-colors" aria-label="{{ __('support.close_label') }}">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                @if($supportZalo)
                <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $supportZalo) }}" target="_blank" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 rounded-xl px-3 py-3 transition-colors">
                    <span class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">Zalo</span>
                    <span class="text-sm font-semibold truncate">Zalo</span>
                </a>
                @endif
                @if($supportHotline)
                <a href="tel:{{ $supportHotline }}" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 rounded-xl px-3 py-3 transition-colors">
                    <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0"><i class="fa-solid fa-phone"></i></span>
                    <span class="text-sm font-semibold truncate">{{ $supportHotline }}</span>
                </a>
                @endif
            </div>
            <p class="text-sm text-slate-300 mb-3">{{ __('support.hours') }}</p>
            <p class="text-sm text-slate-300">{{ __('support.business_contact') }}: <a href="mailto:support@softwarepays.com" class="text-blue-400 hover:underline">support@softwarepays.com</a></p>
        </div>

        <!-- Main Button -->
        <button id="floating-contact-btn" type="button" onclick="fcToggle()" class="w-14 h-14 rounded-full bg-blue-600 text-white shadow-xl hover:bg-blue-700 hover:scale-105 hover:shadow-blue-600/30 transition-all duration-300 flex items-center justify-center text-2xl relative z-10" aria-label="{{ __('support.open_label') }}">
            <i id="fc-icon-main" class="fa-solid fa-headset absolute transition-transform duration-300 transform"></i>
            <i id="fc-icon-close" class="fa-solid fa-xmark absolute transition-transform duration-300 transform -rotate-90 opacity-0"></i>
        </button>
    </div>

    <script>
        let fcIsOpen = false;
        function fcToggle() {
            const modal = document.getElementById('fc-modal');
            const iconMain = document.getElementById('fc-icon-main');
            const iconClose = document.getElementById('fc-icon-close');
            fcIsOpen = !fcIsOpen;
            if (fcIsOpen) {
                modal.classList.remove('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
                modal.classList.add('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
                iconMain.classList.add('rotate-90', 'opacity-0');
                iconClose.classList.remove('-rotate-90', 'opacity-0');
                iconClose.classList.add('rotate-0', 'opacity-100');
            } else {
                modal.classList.add('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
                modal.classList.remove('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
                iconMain.classList.remove('rotate-90', 'opacity-0');
                iconClose.classList.add('-rotate-90', 'opacity-0');
                iconClose.classList.remove('rotate-0', 'opacity-100');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                if (fcIsOpen && !document.getElementById('floating-contact').contains(e.target)) {
                    fcToggle();
                }
            });
        });
    </script>
    <script>
        function toggleUserMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('user-dropdown-menu');
            if (menu) {
                menu.classList.toggle('opacity-0');
                menu.classList.toggle('invisible');
            }
        }
        
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-dropdown-menu');
            const btn = document.getElementById('user-avatar-btn');
            if (menu && !menu.contains(event.target) && btn && !btn.contains(event.target)) {
                menu.classList.add('opacity-0', 'invisible');
            }
        });
    </script>
    <script src="//instant.page/5.2.0" type="module" integrity="sha384-jnZcgoEq3zzkHy170428Q9A0wY20XvKtt0+q5f5aDq0N22H91vD3w4u6uTqK0P9P"></script>
    @if(!empty($adminCodeSettings['admin_footer_code']))
        {!! $adminCodeSettings['admin_footer_code'] !!}
    @endif
</body>
</html>
