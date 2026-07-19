@extends('theme::layouts.app')

@section('title', __('home.page_title'))

@section('content')
    <!-- Main Hero Banner (Carousel Style) -->
    <div class="relative overflow-hidden pt-4 md:pt-6 mb-8 md:mb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            @if(isset($banners) && $banners->isNotEmpty())
                <div id="hero-slider" class="relative rounded-2xl overflow-hidden shadow-2xl aspect-[21/9] md:aspect-[21/7]">
                    @foreach($banners as $i => $banner)
                        <a href="{{ $banner->link_url }}" class="hero-slide absolute inset-0 transition-opacity duration-700 {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                            <img src="{{ $banner->displayImage() }}" alt="Banner {{ $i + 1 }}" class="w-full h-full object-cover" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                        </a>
                    @endforeach

                    @if($banners->count() > 1)
                        <button type="button" onclick="heroSliderMove(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center backdrop-blur-md transition-colors">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" onclick="heroSliderMove(1)" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center backdrop-blur-md transition-colors">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                            @foreach($banners as $i => $banner)
                                <button type="button" onclick="heroSliderGoTo({{ $i }})" class="hero-slider-dot w-2.5 h-2.5 rounded-full transition-all {{ $i === 0 ? 'bg-white w-6' : 'bg-white/50' }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <script>
                    (function () {
                        const slides = document.querySelectorAll('#hero-slider .hero-slide');
                        const dots = document.querySelectorAll('#hero-slider .hero-slider-dot');
                        let current = 0;
                        let timer = null;

                        window.heroSliderGoTo = function (index) {
                            slides[current].classList.remove('opacity-100', 'z-10');
                            slides[current].classList.add('opacity-0', 'z-0');
                            dots[current].classList.remove('bg-white', 'w-6');
                            dots[current].classList.add('bg-white/50');

                            current = (index + slides.length) % slides.length;

                            slides[current].classList.remove('opacity-0', 'z-0');
                            slides[current].classList.add('opacity-100', 'z-10');
                            dots[current].classList.remove('bg-white/50');
                            dots[current].classList.add('bg-white', 'w-6');
                        };

                        window.heroSliderMove = function (delta) {
                            heroSliderGoTo(current + delta);
                            resetTimer();
                        };

                        function resetTimer() {
                            if (timer) clearInterval(timer);
                            if (slides.length > 1) {
                                timer = setInterval(() => heroSliderGoTo(current + 1), 5000);
                            }
                        }

                        resetTimer();
                    })();
                </script>
            @else
                <div class="bg-blue-600 dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl relative flex flex-col md:flex-row md:items-center min-h-[400px] md:min-h-[auto] md:aspect-[21/7]">
                    <!-- Full width Background Image -->
                    <img src="https://media.rawg.io/media/games/20a/20aa03a10cda45239fe22d035c0ebe64.jpg" alt="Featured Game" class="absolute inset-0 w-full h-full object-cover object-top opacity-70 md:opacity-100" loading="lazy">

                    <!-- Smooth Gradient Overlays -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent md:hidden z-10"></div>
                    <div class="absolute inset-0 hidden md:block bg-gradient-to-r from-blue-900 via-blue-900/80 to-transparent dark:from-slate-900 dark:via-slate-900/80 dark:to-transparent z-10 w-full md:w-[70%]"></div>
                    <div class="absolute inset-0 hidden md:block bg-gradient-to-r from-blue-900/50 to-transparent dark:from-slate-900/50 dark:to-transparent z-10 w-full"></div>

                    <!-- Content Area -->
                    <div class="relative z-20 p-6 md:p-12 w-full md:w-[60%] flex flex-col justify-end md:justify-center h-full mt-auto md:mt-0 min-h-[400px] md:min-h-0 pt-40 md:pt-12">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-bold mb-4 w-max backdrop-blur-md border border-white/20 uppercase tracking-wider">
                            <i class="fa-solid fa-bolt text-yellow-400"></i> {{ __('home.hero_badge') }}
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-black text-white mb-4 leading-tight drop-shadow-lg">
                            {{ __('home.hero_headline') }} <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">{{ __('home.hero_headline_highlight') }}</span>
                        </h1>
                        <p class="text-blue-50 dark:text-slate-300 text-sm md:text-lg mb-8 max-w-lg drop-shadow-md">
                            {{ __('home.hero_description') }}
                        </p>
                        <div>
                            <a href="#store" class="bg-white hover:bg-slate-100 text-slate-900 px-8 py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 w-full sm:w-max transition-all shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] hover:-translate-y-1">
                                {{ __('home.hero_cta_secondary') }} <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Danh mục -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-6 border-l-4 border-orange-500 pl-3">
            {{ __('home.categories_heading') }}
        </h2>
        @php
            $homeCategories = [
                ['label' => __('home_categories.smm'), 'icon' => 'fa-solid fa-users', 'href' => route('smm.index')],
                ['label' => __('home_categories.games'), 'icon' => 'fa-solid fa-gamepad', 'href' => route('shop')],
                ['label' => __('home_categories.subscriptions'), 'icon' => 'fa-solid fa-clipboard-check', 'href' => route('catalog.simple', 'goi-dang-ky')],
                ['label' => __('home_categories.software'), 'icon' => 'fa-solid fa-desktop', 'href' => route('catalog.simple', 'phan-mem')],
                ['label' => __('home_categories.cards'), 'icon' => 'fa-solid fa-wallet', 'href' => route('catalog.card')],
                ['label' => __('home_categories.giftcards'), 'icon' => 'fa-solid fa-gift', 'href' => route('catalog.simple', 'qua-tang')],
                ['label' => __('home_categories.esim'), 'icon' => 'fa-solid fa-sim-card', 'href' => route('catalog.esim')],
                ['label' => __('home_categories.other'), 'icon' => 'fa-solid fa-layer-group', 'href' => route('pages.other')],
            ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach($homeCategories as $cat)
                <a href="{{ $cat['href'] }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center gap-3 hover:shadow-lg hover:-translate-y-1 hover:border-blue-400 transition-all">
                    <div class="w-16 h-16 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-2xl text-orange-500">
                        <i class="{{ $cat['icon'] }}"></i>
                    </div>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-tight">{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Cửa Hàng -->
    <div id="store" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-fire text-orange-500"></i> {{ __('home.featured_products_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.featured_subtitle') }}</p>
            </div>

            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-blue-500/25">
                    {{ __('home.featured_view_all') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-6 lg:gap-8">
            @foreach($products as $product)
                @php
                    $homePlatformLabel = $product->platformDisplayLabel();
                @endphp
                <a href="{{ route('product.show', ['id' => $product->id, 'slug' => \Illuminate\Support\Str::slug($product->name) ?: 'game']) }}" class="product-card group">
                    <div class="product-card-media bg-slate-100 dark:bg-slate-800 aspect-square relative overflow-hidden">
                        @if($product->original_price && $product->original_price > $product->price)
                            <span class="discount-badge">
                                <strong>-{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%</strong>
                                <small>{{ __('home.featured_discount_label') }}</small>
                            </span>
                        @endif
                        @if($product->header_image)
                            <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800">
                                <span class="text-slate-400"><i class="fa-solid fa-gamepad text-3xl sm:text-5xl"></i></span>
                            </div>
                        @endif
                        
                        <!-- Quick Actions on Hover -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] z-10 pointer-events-none">
                            <span class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-xl flex items-center gap-2"><i class="fa-solid fa-eye"></i> {{ __('home.featured_quick_view') }}</span>
                        </div>
                    </div>
                    
                    <div class="p-4 flex flex-col flex-grow">
                        <div class="mb-2 h-[48px]">
                            <h3 class="font-display font-semibold text-[15px] text-slate-900 dark:text-white leading-snug group-hover:text-blue-500 transition-colors line-clamp-2" title="{{ $product->name }}">{{ $product->name }}</h3>
                        </div>
                        
                        <div class="flex items-center gap-2 mb-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase"><i class="{{ \App\Modules\Theme\Models\Product::platformIcon($homePlatformLabel) }}"></i> {{ $homePlatformLabel ?: 'GAME' }}</span>
                            @if($product->available_keys > 0)
                                <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-check"></i> {{ __('home.featured_ready_badge') }}</span>
                            @else
                                <span class="text-[10px] text-rose-500 font-bold bg-rose-50 dark:bg-rose-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-xmark"></i> {{ __('home.featured_out_of_stock_badge') }}</span>
                            @endif
                        </div>
                        
                        <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                            <div class="flex flex-col">
                                @if($product->original_price && $product->original_price > $product->price)
                                    <span class="text-[11px] text-slate-400 line-through font-medium">{!! \App\Helpers\CurrencyHelper::formatPrice($product->original_price) !!}</span>
                                @endif
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{!! \App\Helpers\CurrencyHelper::formatPrice($product->price) !!}</span>
                            </div>
                            
                            <button class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all flex items-center justify-center shadow-sm hover:scale-110">
                                <i class="fa-solid fa-cart-shopping text-sm"></i>
                            </button>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($products->isEmpty())
            <div class="glass-card rounded-lg p-16 text-center text-slate-500 mt-8">
                <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
                <h3 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('home.featured_empty_title') }}</h3>
                <p>{{ __('home.featured_empty_desc') }}</p>
            </div>
        @endif
    </div>

    <!-- Thẻ Steam Wallet -->
    @if(isset($giftcards) && $giftcards->isNotEmpty())
    <div id="steamwallet" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-gift text-blue-500"></i> {{ __('home.steamwallet_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.steamwallet_subtitle') }}</p>
            </div>

            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('catalog.simple', 'qua-tang') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-semibold transition-all shadow-sm">
                    {{ __('home.steamwallet_view_all') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @foreach($giftcards->take(10) as $card)
                @php
                    $genres = json_decode($card->genres, true) ?? [];
                    $region = in_array('Vietnam', $genres) ? 'Vietnam' : (in_array('Global', $genres) ? 'Global' : 'Other');
                    $regionColor = $region === 'Vietnam' ? 'text-red-600 bg-red-100 dark:bg-red-900/60 dark:text-red-400' : 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/60 dark:text-emerald-400';
                    // Thẻ quà tặng là sản phẩm giá cố định, mua thẳng (không có trang chi tiết riêng) —
                    // bấm vào là thêm thẳng vào giỏ (giống catalog-simple), khách chưa đăng nhập thì
                    // đưa qua đăng nhập rồi quay lại đúng trang chủ (không phải trang sản phẩm Steam Wallet).
                @endphp
                @auth
                <form action="{{ route('cart.add', $card->id) }}" method="POST" class="glass-card rounded-xl sm:rounded-2xl overflow-hidden group hover:border-blue-500 hover:shadow-xl hover:shadow-blue-500/10 transition-all border border-slate-200 dark:border-slate-700 flex flex-col bg-white dark:bg-slate-800 relative">
                    @csrf
                    <button type="submit" class="contents text-left">
                        <!-- Region Badge -->
                        <div class="absolute top-3 right-3 z-10">
                            <span class="text-[10px] font-bold px-2 py-1 rounded-md shadow-sm {{ $regionColor }} backdrop-blur-md">{{ $region }}</span>
                        </div>

                        <!-- Cover Image -->
                        <div class="aspect-square bg-slate-100 dark:bg-slate-900 relative overflow-hidden">
                            <img src="{{ $card->header_image ?: '/images/steam_wallet_default.png' }}" alt="{{ $card->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>

                        <!-- Content -->
                        <div class="p-3 sm:p-4 border-t border-slate-100 dark:border-slate-700/50 flex-grow flex flex-col justify-between bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-900">
                            <h3 class="font-display font-bold text-slate-900 dark:text-white text-xs sm:text-sm mb-2 group-hover:text-blue-500 transition-colors line-clamp-2">
                                {{ str_replace(['Steam Wallet ', ' (Vietnam)', ' (Global)'], '', $card->name) }}
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-600 dark:text-blue-400 font-black text-sm sm:text-base">{!! \App\Helpers\CurrencyHelper::formatPrice($card->price) !!}</span>
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-plus text-[10px] sm:text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </button>
                </form>
                @else
                <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="glass-card rounded-xl sm:rounded-2xl overflow-hidden group hover:border-blue-500 hover:shadow-xl hover:shadow-blue-500/10 transition-all border border-slate-200 dark:border-slate-700 flex flex-col bg-white dark:bg-slate-800 relative">
                    <!-- Region Badge -->
                    <div class="absolute top-3 right-3 z-10">
                        <span class="text-[10px] font-bold px-2 py-1 rounded-md shadow-sm {{ $regionColor }} backdrop-blur-md">{{ $region }}</span>
                    </div>

                    <!-- Cover Image -->
                    <div class="aspect-square bg-slate-100 dark:bg-slate-900 relative overflow-hidden">
                        <img src="{{ $card->header_image ?: '/images/steam_wallet_default.png' }}" alt="{{ $card->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>

                    <!-- Content -->
                    <div class="p-3 sm:p-4 border-t border-slate-100 dark:border-slate-700/50 flex-grow flex flex-col justify-between bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-900">
                        <h3 class="font-display font-bold text-slate-900 dark:text-white text-xs sm:text-sm mb-2 group-hover:text-blue-500 transition-colors line-clamp-2">
                            {{ str_replace(['Steam Wallet ', ' (Vietnam)', ' (Global)'], '', $card->name) }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 dark:text-blue-400 font-black text-sm sm:text-base">{!! \App\Helpers\CurrencyHelper::formatPrice($card->price) !!}</span>
                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-plus text-[10px] sm:text-xs"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @endauth
            @endforeach
        </div>
    </div>
    @endif

    <!-- Dịch Vụ Mạng Xã Hội (SMM) Section -->
    <div id="smm" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">

        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4 relative z-10">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-heart text-rose-500"></i> {{ __('home.smm_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.smm_subtitle') }}</p>
            </div>

            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('smm.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-indigo-500/25">
                    {{ __('home.smm_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
            <!-- Facebook Card -->
            <a href="{{ route('smm.index') }}?platform=Facebook" class="glass-card rounded-lg p-6 group hover:border-blue-500 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-blue-600 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-facebook-f"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Facebook</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_facebook_desc') }}</p>
                <div class="text-blue-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- TikTok Card -->
            <a href="{{ route('smm.index') }}?platform=TikTok" class="glass-card rounded-lg p-6 group hover:border-slate-900 dark:hover:border-slate-500 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-slate-900 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-tiktok"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">TikTok</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_tiktok_desc') }}</p>
                <div class="text-slate-900 dark:text-slate-400 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- Instagram Card -->
            <a href="{{ route('smm.index') }}?platform=Instagram" class="glass-card rounded-lg p-6 group hover:border-fuchsia-500 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-gradient-to-tr from-amber-500 via-rose-500 to-fuchsia-600 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-instagram"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Instagram</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_instagram_desc') }}</p>
                <div class="text-fuchsia-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- YouTube Card -->
            <a href="{{ route('smm.index') }}?platform=YouTube" class="glass-card rounded-lg p-6 group hover:border-red-500 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-red-600 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-youtube"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">YouTube</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_youtube_desc') }}</p>
                <div class="text-red-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- Threads Card -->
            <a href="{{ route('smm.index') }}?platform=Threads" class="glass-card rounded-lg p-6 group hover:border-slate-900 dark:hover:border-slate-400 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-slate-900 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-solid fa-at"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Threads</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_threads_desc') }}</p>
                <div class="text-slate-900 dark:text-slate-400 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- X (Twitter) Card -->
            <a href="{{ route('smm.index') }}?platform=X" class="glass-card rounded-lg p-6 group hover:border-slate-900 dark:hover:border-slate-400 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-black text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-twitter"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">X (Twitter)</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_twitter_desc') }}</p>
                <div class="text-slate-900 dark:text-slate-400 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- Shopee Card -->
            <a href="{{ route('smm.index') }}?platform=Shopee" class="glass-card rounded-lg p-6 group hover:border-orange-500 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-orange-500 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Shopee</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_shopee_desc') }}</p>
                <div class="text-orange-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- Lazada Card -->
            <a href="{{ route('smm.index') }}?platform=Lazada" class="glass-card rounded-lg p-6 group hover:border-blue-700 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-blue-700 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Lazada</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_lazada_desc') }}</p>
                <div class="text-blue-700 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- LinkedIn Card -->
            <a href="{{ route('smm.index') }}?platform=Linkedin" class="glass-card rounded-lg p-6 group hover:border-sky-700 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-sky-700 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-linkedin"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">LinkedIn</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_linkedin_desc') }}</p>
                <div class="text-sky-700 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- Google Card -->
            <a href="{{ route('smm.index') }}?platform=Google" class="glass-card rounded-lg p-6 group hover:border-emerald-500 transition-colors relative overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded bg-emerald-600 text-white flex items-center justify-center text-2xl mb-6 shadow-sm">
                    <i class="fa-brands fa-google"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Google</h3>
                <p class="text-slate-500 text-sm mb-4">{{ __('home.smm_google_desc') }}</p>
                <div class="text-emerald-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ __('home.smm_view_service') }} <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    @php
        // Các sản phẩm này (gói đăng ký/phần mềm) chưa có ảnh thật trong database (không tự động lấy
        // ảnh bìa như game qua Kinguin) — dùng logo thương hiệu có sẵn (Font Awesome brand icon) cho
        // các tên khớp được, còn lại rơi về icon chung mặc định của section.
        $subscriptionBrandIcons = [
            'Netflix' => ['fa-solid fa-clapperboard', 'bg-red-500/10', 'text-red-600'],
            'Spotify' => ['fa-brands fa-spotify', 'bg-green-500/10', 'text-green-600'],
            'YouTube' => ['fa-brands fa-youtube', 'bg-red-500/10', 'text-red-600'],
            'Microsoft 365' => ['fa-brands fa-microsoft', 'bg-blue-500/10', 'text-blue-600'],
            'Canva' => ['fa-solid fa-palette', 'bg-cyan-500/10', 'text-cyan-600'],
        ];
        $softwareBrandIcons = [
            'Windows' => ['fa-brands fa-windows', 'bg-blue-500/10', 'text-blue-600'],
            'Office' => ['fa-brands fa-microsoft', 'bg-orange-500/10', 'text-orange-600'],
            'WinRAR' => ['fa-solid fa-file-zipper', 'bg-amber-500/10', 'text-amber-600'],
            'Download Manager' => ['fa-solid fa-download', 'bg-teal-500/10', 'text-teal-600'],
            'Kaspersky' => ['fa-solid fa-shield-virus', 'bg-emerald-500/10', 'text-emerald-600'],
        ];
        $resolveBrandIcon = function (string $name, array $map, string $fallbackIcon, string $fallbackBg, string $fallbackText) {
            foreach ($map as $needle => $style) {
                if (str_contains($name, $needle)) return $style;
            }
            return [$fallbackIcon, $fallbackBg, $fallbackText];
        };
    @endphp

    <!-- Gói Đăng Ký -->
    @if(isset($subscriptionProducts) && $subscriptionProducts->isNotEmpty())
    <div id="subscriptions" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-clipboard-check text-indigo-500"></i> {{ __('home.subscriptions_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.subscriptions_subtitle') }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('catalog.simple', 'goi-dang-ky') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-indigo-500/25">
                    {{ __('home.subscriptions_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-4">
            @foreach($subscriptionProducts as $i => $p)
            @php [$icon] = $resolveBrandIcon($p->name, $subscriptionBrandIcons, 'fa-solid fa-clipboard-check', 'bg-indigo-500/10', 'text-indigo-500'); @endphp
            <a href="{{ route('product.show', ['id' => $p->id, 'slug' => \Illuminate\Support\Str::slug($p->name) ?: 'game']) }}" class="product-card group">
                <div class="product-card-media bg-slate-100 dark:bg-slate-800 aspect-square relative overflow-hidden">
                    @if($p->original_price && $p->original_price > $p->price)
                        <span class="discount-badge">
                            <strong>-{{ round((($p->original_price - $p->price) / $p->original_price) * 100) }}%</strong>
                            <small>{{ __('home.featured_discount_label') }}</small>
                        </span>
                    @endif
                    @if($p->header_image)
                        <img src="{{ $p->header_image }}" alt="{{ $p->name }}" class="w-full h-full" style="object-fit: contain; background: #fff; padding: 8px; box-sizing: border-box;" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800">
                            <i class="{{ $icon }} text-3xl sm:text-5xl text-slate-400"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] z-10 pointer-events-none">
                        <span class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-xl flex items-center gap-2"><i class="fa-solid fa-eye"></i> {{ __('home.featured_quick_view') }}</span>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <div class="mb-2 h-[48px]">
                        <h3 class="font-display font-semibold text-[15px] text-slate-900 dark:text-white leading-snug group-hover:text-indigo-500 transition-colors line-clamp-2" title="{{ $p->name }}">{{ $p->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase"><i class="fa-solid fa-clipboard-check"></i> {{ __('home_categories.subscriptions') }}</span>
                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-check"></i> {{ __('home.featured_ready_badge') }}</span>
                    </div>
                    <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                        <div class="flex flex-col">
                            @if($p->original_price && $p->original_price > $p->price)
                                <span class="text-[11px] text-slate-400 line-through font-medium">{!! \App\Helpers\CurrencyHelper::formatPrice($p->original_price) !!}</span>
                            @endif
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{!! \App\Helpers\CurrencyHelper::formatPrice($p->price) !!}</span>
                        </div>
                        <span class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Phần Mềm -->
    @if(isset($softwareProducts) && $softwareProducts->isNotEmpty())
    <div id="software" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-desktop text-blue-500"></i> {{ __('home.software_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.software_subtitle') }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('catalog.simple', 'phan-mem') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-blue-500/25">
                    {{ __('home.software_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-4">
            @foreach($softwareProducts as $p)
            @php [$icon] = $resolveBrandIcon($p->name, $softwareBrandIcons, 'fa-solid fa-desktop', 'bg-blue-500/10', 'text-blue-500'); @endphp
            <a href="{{ route('product.show', ['id' => $p->id, 'slug' => \Illuminate\Support\Str::slug($p->name) ?: 'game']) }}" class="product-card group">
                <div class="product-card-media bg-slate-100 dark:bg-slate-800 aspect-square relative overflow-hidden">
                    @if($p->original_price && $p->original_price > $p->price)
                        <span class="discount-badge">
                            <strong>-{{ round((($p->original_price - $p->price) / $p->original_price) * 100) }}%</strong>
                            <small>{{ __('home.featured_discount_label') }}</small>
                        </span>
                    @endif
                    @if($p->header_image)
                        <img src="{{ $p->header_image }}" alt="{{ $p->name }}" class="w-full h-full" style="object-fit: contain; background: #fff; padding: 8px; box-sizing: border-box;" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800">
                            <i class="{{ $icon }} text-3xl sm:text-5xl text-slate-400"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] z-10 pointer-events-none">
                        <span class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-xl flex items-center gap-2"><i class="fa-solid fa-eye"></i> {{ __('home.featured_quick_view') }}</span>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <div class="mb-2 h-[48px]">
                        <h3 class="font-display font-semibold text-[15px] text-slate-900 dark:text-white leading-snug group-hover:text-blue-500 transition-colors line-clamp-2" title="{{ $p->name }}">{{ $p->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase"><i class="fa-solid fa-desktop"></i> {{ __('home_categories.software') }}</span>
                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-check"></i> {{ __('home.featured_ready_badge') }}</span>
                    </div>
                    <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                        <div class="flex flex-col">
                            @if($p->original_price && $p->original_price > $p->price)
                                <span class="text-[11px] text-slate-400 line-through font-medium">{!! \App\Helpers\CurrencyHelper::formatPrice($p->original_price) !!}</span>
                            @endif
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{!! \App\Helpers\CurrencyHelper::formatPrice($p->price) !!}</span>
                        </div>
                        <span class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Thẻ Nạp & Thẻ Game -->
    @if(isset($cardProducts) && $cardProducts->isNotEmpty())
    <div id="cards" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-wallet text-emerald-500"></i> {{ __('home.cards_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.cards_subtitle') }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('catalog.card') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-emerald-500/25">
                    {{ __('home.cards_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-4">
            @foreach($cardProducts as $p)
            @php
                // Ưu tiên % Admin nhập ở /admin/card/{id}/packages, không gộp chung với chiết khấu vendor.
                $cardMaxDiscount = $p->cardPackages
                    ->filter(fn ($pkg) => $pkg->price < $pkg->face_value)
                    ->map(fn ($pkg) => $pkg->promo_discount_percent > 0
                        ? round($pkg->promo_discount_percent)
                        : round((($pkg->face_value - $pkg->price) / $pkg->face_value) * 100))
                    ->max();
                $cardMinPrice = $p->cardPackages->pluck('price')->min();
            @endphp
            <a href="{{ route('catalog.card.show', $p->id) }}" class="product-card group">
                <div class="product-card-media bg-slate-100 dark:bg-slate-800 aspect-square relative overflow-hidden">
                    @if($cardMaxDiscount)
                        <span class="discount-badge">
                            <strong>-{{ $cardMaxDiscount }}%</strong>
                            <small>{{ __('home.featured_discount_label') }}</small>
                        </span>
                    @endif
                    @if($p->header_image)
                        <img src="{{ $p->header_image }}" alt="{{ $p->name }}" class="w-full h-full" style="object-fit: contain; background: #fff; padding: 8px; box-sizing: border-box;" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800">
                            <i class="fa-solid fa-wallet text-3xl sm:text-5xl text-slate-400"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] z-10 pointer-events-none">
                        <span class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-xl flex items-center gap-2"><i class="fa-solid fa-eye"></i> {{ __('home.featured_quick_view') }}</span>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <div class="mb-2 h-[48px]">
                        <h3 class="font-display font-semibold text-[15px] text-slate-900 dark:text-white leading-snug group-hover:text-emerald-500 transition-colors line-clamp-2" title="{{ $p->name }}">{{ $p->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase"><i class="fa-solid fa-wallet"></i> {{ __('home_categories.cards') }}</span>
                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-check"></i> {{ __('home.featured_ready_badge') }}</span>
                    </div>
                    <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                        @if($cardMinPrice)
                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{!! \App\Helpers\CurrencyHelper::formatPrice($cardMinPrice) !!}</span>
                        @endif
                        <span class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-all flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif


    <!-- eSIM Du Lịch -->
    @if(isset($esimHighlights) && $esimHighlights->isNotEmpty())
    <div id="esim" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-sim-card text-orange-500"></i> {{ __('home.esim_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.esim_subtitle') }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('catalog.esim') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-orange-500/25">
                    {{ __('home.esim_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-4">
            @foreach($esimHighlights as $p)
            @php $esimMinPrice = $p->esimPackages->pluck('price')->min(); @endphp
            <a href="{{ route('catalog.esim.show', $p->id) }}" class="product-card group">
                <div class="product-card-media bg-slate-100 dark:bg-slate-800 aspect-square relative overflow-hidden">
                    @if($p->header_image)
                        <img src="{{ $p->header_image }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800">
                            <i class="fa-solid fa-sim-card text-3xl sm:text-5xl text-slate-400"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] z-10 pointer-events-none">
                        <span class="bg-orange-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-xl flex items-center gap-2"><i class="fa-solid fa-eye"></i> {{ __('home.featured_quick_view') }}</span>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <div class="mb-2 h-[48px]">
                        <h3 class="font-display font-semibold text-[15px] text-slate-900 dark:text-white leading-snug group-hover:text-orange-500 transition-colors line-clamp-2" title="{{ $p->name }}">{{ $p->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase"><i class="fa-solid fa-sim-card"></i> {{ __('home_categories.esim') }}</span>
                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-check"></i> {{ __('home.featured_ready_badge') }}</span>
                    </div>
                    <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                        @if($esimMinPrice)
                        <span class="text-lg font-bold text-orange-600 dark:text-orange-400">{!! \App\Helpers\CurrencyHelper::formatPrice($esimMinPrice) !!}</span>
                        @endif
                        <span class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-orange-600 group-hover:text-white group-hover:border-orange-600 transition-all flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Sound World -->
    @if(isset($homeSounds) && $homeSounds->isNotEmpty())
    <div id="sound-meme" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-music text-fuchsia-500"></i> {{ __('home.soundmeme_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.soundmeme_subtitle') }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('sounds.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-fuchsia-500/25">
                    {{ __('home.soundmeme_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-4">
            @foreach($homeSounds as $s)
                @include('soundmeme::theme.partials.card', ['sound' => $s])
            @endforeach
        </div>
    </div>
    @endif

    <!-- GIF World -->
    @if(isset($homeGifs) && $homeGifs->isNotEmpty())
    <div id="gif-meme" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24 relative">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-images text-teal-500"></i> {{ __('home.gifmeme_heading') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400">{{ __('home.gifmeme_subtitle') }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('Gifs.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-teal-500/25">
                    {{ __('home.gifmeme_cta') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-x-2 gap-y-6">
            @foreach($homeGifs as $g)
                @include('gifmeme::theme.partials.card', ['Gif' => $g])
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($homeSounds) && $homeSounds->isNotEmpty())
    <audio id="global-player" class="hidden"></audio>
    <script>
        window.SOUND_CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/sound-player.js?v=' . time()) }}"></script>
    @endif

    @if(isset($homeGifs) && $homeGifs->isNotEmpty())
    <script>
        window.Gif_CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/gif-player.js?v=' . time()) }}"></script>
    @endif
@endsection
