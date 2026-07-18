@extends('theme::layouts.app')

@section('title', ($product->seo_title ?? $product->name) . ' - ' . __('product.meta_title_suffix'))
@section('meta_description', $product->seo_description ?? __('product.meta_description', ['name' => $product->name]))

@push('styles')
<style>
    /* CSS Tùy chỉnh cho Content từ Steam */
    .steam-content {
        color: #334155; /* slate-700 */
        font-size: 1.05rem;
        line-height: 1.8;
    }
    .dark .steam-content {
        color: #e2e8f0; /* slate-200 */
    }
    .steam-content img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 24px 0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .dark .steam-content img {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
    }
    .steam-content h2 {
        color: #0f172a;
        font-family: 'Outfit', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .dark .steam-content h2 {
        color: #fff;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .steam-content h3, .steam-content h1 {
        color: #1e293b;
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .dark .steam-content h3, .dark .steam-content h1 {
        color: #f8fafc;
    }
    .steam-content br {
        margin-bottom: 10px;
        display: block;
        content: "";
    }
    .steam-content a {
        color: #2563eb;
        text-decoration: none;
        transition: color 0.2s;
    }
    .dark .steam-content a {
        color: #60a5fa;
    }
    .steam-content a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
    .dark .steam-content a:hover {
        color: #93c5fd;
    }
    .steam-content ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .steam-content li {
        margin-bottom: 0.5rem;
    }
    
    /* Box Panel */
    .glass-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .dark .glass-panel {
        background: #1e293b; /* solid slate-800 */
        border: 1px solid #334155;
    }
    
    /* Scrollbar xịn cho Ảnh */
    .hide-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .hide-scroll::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }
    .dark .hide-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    .hide-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }
    .dark .hide-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
    }
    .hide-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }
    .dark .hide-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>
@endpush

@section('content')
    <!-- Nền Màu Tối Header -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none bg-slate-50 dark:bg-slate-950">
        @if($product->header_image)
            <img src="{{ $product->header_image }}" class="absolute inset-0 w-full h-full object-cover opacity-10" alt="">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-slate-50/80 via-slate-50 to-slate-50 dark:from-slate-950/80 dark:via-slate-950 dark:to-slate-950"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 pb-24 relative z-10">
        
        <!-- Breadcrumb -->
        <div class="text-sm text-slate-400 mb-6 flex items-center space-x-3 font-medium">
            <a href="/" class="hover:text-blue-400 transition flex items-center gap-2"><i class="fa-solid fa-house text-xs"></i> {{ __('product.breadcrumb_home') }}</a>
            <span><i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i></span>
            <a href="{{ route('shop') }}" class="hover:text-blue-400 transition">{{ __('product.breadcrumb_shop') }}</a>
            <span><i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i></span>
            <span class="text-slate-900 dark:text-white">{{ $product->name }}</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 xl:gap-10">
            
            <!-- ====== CỘT TRÁI (Ảnh, Trailer, Thông tin chi tiết) ====== -->
            <div class="xl:col-span-2 space-y-10">
                
                <!-- Main Header / Media Section -->
                <div>
                    @php
                        // Ảnh admin tự upload cho Gói đăng ký/Phần mềm thường là ảnh quảng cáo/logo tỉ lệ
                        // tuỳ ý (không phải box art 16:9 chuẩn như game từ Kinguin) — object-cover cắt mất
                        // phần quan trọng (chữ, logo). Dùng object-contain riêng cho 2 loại này, giữ
                        // nguyên object-cover cho game/giftcard vì ảnh cover Kinguin đã đúng tỉ lệ sẵn.
                        $fitContain = in_array($product->product_type, [
                            \App\Modules\Theme\Models\Product::TYPE_SUBSCRIPTION,
                            \App\Modules\Theme\Models\Product::TYPE_SOFTWARE,
                        ]);
                    @endphp
                    <!-- Ảnh Bìa (Main Image) -->
                    <div class="relative rounded-lg overflow-hidden shadow border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 group mb-4">
                        @if($product->header_image)
                            <img id="main-product-image" src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full aspect-[2/1] md:aspect-[21/9] {{ $fitContain ? 'object-contain bg-white' : 'object-cover' }} transition-transform duration-700">
                        @else
                            <div class="w-full aspect-[2/1] md:aspect-[21/9] flex items-center justify-center text-slate-700 bg-slate-100 dark:bg-slate-800">
                                <i class="fa-solid fa-image text-7xl opacity-50"></i>
                            </div>
                        @endif

                        @unless($fitContain)
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-90 pointer-events-none"></div>

                        <div class="absolute bottom-0 left-0 w-full p-6 md:p-8 pointer-events-none">
                            <h1 class="text-3xl md:text-5xl lg:text-5xl font-display font-bold text-white mb-2 drop-shadow-lg leading-tight">{{ $product->name }}</h1>
                            @if(isset($product->steam_data['developers']) && count($product->steam_data['developers']) > 0)
                                <p class="text-slate-300 font-medium drop-shadow-md">{{ __('product.developed_by_label') }} <span class="text-blue-400 font-bold">{{ implode(', ', $product->steam_data['developers']) }}</span></p>
                            @endif
                        </div>
                        @endunless
                    </div>

                    {{-- Ảnh quảng cáo tự upload (Gói đăng ký/Phần mềm) không phủ vừa toàn khung nên
                         không đặt tiêu đề đè lên ảnh (dễ chồng chữ lên icon/logo trong ảnh) — hiện
                         tên sản phẩm thành tiêu đề riêng bên dưới ảnh, giống cách trang VPN/eSIM làm. --}}
                    @if($fitContain)
                    <h1 class="text-2xl md:text-3xl font-display font-bold text-slate-900 dark:text-white mb-4 leading-tight">{{ $product->name }}</h1>
                    @endif

                    <!-- Danh sách Screenshots (Thumbnails) -->
                    @if(isset($product->steam_data['screenshots']) && count($product->steam_data['screenshots']) > 0)
                        <div class="flex overflow-x-auto gap-3 pb-2 hide-scroll custom-scrollbar">
                            <!-- Thumbnail cho ảnh bìa gốc -->
                            @if($product->header_image)
                            <button type="button" onclick="changeMainImage('{{ $product->header_image }}', this)" class="thumbnail-btn shrink-0 w-24 md:w-32 aspect-video rounded-md overflow-hidden border-2 border-blue-500 shadow-sm transition-all focus:outline-none">
                                <img src="{{ $product->header_image }}" class="w-full h-full object-cover">
                            </button>
                            @endif

                            @foreach($product->steam_data['screenshots'] as $screenshot)
                                <button type="button" onclick="changeMainImage('{{ $screenshot }}', this)" class="thumbnail-btn shrink-0 w-24 md:w-32 aspect-video rounded-md overflow-hidden border-2 border-transparent hover:border-blue-400 shadow-sm opacity-70 hover:opacity-100 transition-all focus:outline-none">
                                    <img src="{{ $screenshot }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                        
                        <script>
                            function changeMainImage(url, btnElement) {
                                // Đổi ảnh to
                                document.getElementById('main-product-image').src = url;
                                
                                // Xóa viền xanh ở tất cả thumbnail
                                document.querySelectorAll('.thumbnail-btn').forEach(btn => {
                                    btn.classList.remove('border-blue-500', 'opacity-100');
                                    btn.classList.add('border-transparent', 'opacity-70');
                                });
                                
                                // Thêm viền xanh cho thumbnail đang được bấm
                                btnElement.classList.remove('border-transparent', 'opacity-70');
                                btnElement.classList.add('border-blue-500', 'opacity-100');
                            }
                        </script>
                    @endif
                </div>
                
                <!-- Bảng Tóm tắt Thông tin (Ngang) -->
                @if(isset($product->steam_data))
                <div class="glass-panel p-6 rounded-lg grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">{{ __('product.steam_release_date_label') }}</div>
                        <div class="text-slate-900 dark:text-white font-medium">{{ $product->steam_data['release_date'] ?? __('product.steam_updating') }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">{{ __('product.steam_publisher_label') }}</div>
                        <div class="text-slate-900 dark:text-white font-medium truncate" title="{{ implode(', ', $product->steam_data['publishers'] ?? []) }}">{{ implode(', ', $product->steam_data['publishers'] ?? ['N/A']) }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest font-bold mb-2">{{ __('product.steam_genre_label') }}</div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($product->categories && $product->categories->count() > 0)
                                @foreach($product->categories as $category)
                                    <a href="{{ route('shop') }}?genres[]={{ urlencode($category->name) }}" class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] px-2.5 py-1 rounded-md font-semibold hover:bg-blue-600 hover:text-white transition-colors duration-200 uppercase tracking-wider">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            @elseif($product->genres)
                                @php
                                    $displayGenres = is_array($product->genres) ? $product->genres : json_decode($product->genres, true);
                                    if(!is_array($displayGenres) && is_string($product->genres)) {
                                        $displayGenres = array_map('trim', explode(',', $product->genres));
                                    }
                                @endphp
                                @if(is_array($displayGenres))
                                    @foreach($displayGenres as $genre)
                                        <a href="{{ route('shop') }}?genres[]={{ urlencode($genre) }}" class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] px-2.5 py-1 rounded-md font-semibold hover:bg-blue-600 hover:text-white transition-colors duration-200 uppercase tracking-wider">
                                            {{ $genre }}
                                        </a>
                                    @endforeach
                                @endif
                            @else
                                <span class="text-slate-400 text-[11px] italic">{{ __('product.steam_updating') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Nội dung Giới thiệu Game (Steam Data) -->
                <div class="glass-panel p-6 md:p-10 rounded-lg relative overflow-hidden">
                    
                    <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-book-open text-blue-500"></i> {{ __('product.steam_about_heading') }}
                    </h2>

                    <div class="steam-content">
                        @if(isset($product->steam_data['detailed_description']) && $product->steam_data['detailed_description'] != '')
                            {!! $product->steam_data['detailed_description'] !!}
                        @elseif($product->description)
                            {!! $product->description !!}
                        @else
                            <p class="text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700 text-center">{{ __('product.no_detailed_info') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Video Giới Thiệu (Trailer) -->
                @if(isset($product->steam_data['videos']) && count($product->steam_data['videos']) > 0)
                <div class="glass-panel p-6 md:p-10 rounded-lg relative overflow-hidden">
                    <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-circle-play text-blue-500"></i> {{ __('product.video_heading') }}
                    </h2>

                    <div class="grid grid-cols-1 {{ count($product->steam_data['videos']) > 1 ? 'md:grid-cols-2' : '' }} gap-6">
                        @foreach($product->steam_data['videos'] as $video)
                            <div class="relative w-full aspect-video rounded-lg overflow-hidden shadow border border-slate-200 dark:border-slate-800">
                                <iframe src="{{ $video['embed_url'] }}" class="absolute inset-0 w-full h-full" loading="lazy" title="{{ $product->name }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Cấu hình hệ thống -->
                @if(isset($product->steam_data['pc_requirements']) && (isset($product->steam_data['pc_requirements']['minimum']) || isset($product->steam_data['pc_requirements']['recommended'])))
                <div class="glass-panel p-6 md:p-10 rounded-lg">
                    <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-desktop text-blue-500"></i> {{ __('product.steam_requirements_heading') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 steam-content text-sm">
                        @if(isset($product->steam_data['pc_requirements']['minimum']))
                            <div class="bg-slate-50 dark:bg-slate-900 p-6 rounded-lg border border-slate-200 dark:border-slate-800">
                                {!! $product->steam_data['pc_requirements']['minimum'] !!}
                            </div>
                        @endif
                        @if(isset($product->steam_data['pc_requirements']['recommended']))
                            <div class="bg-slate-50 dark:bg-slate-900 p-6 rounded-lg border border-slate-200 dark:border-slate-800">
                                {!! $product->steam_data['pc_requirements']['recommended'] !!}
                            </div>
                        @endif
                    </div>
                </div>
                @endif
                
            </div>

            <!-- ====== CỘT PHẢI (Giá & Mua) ====== -->
            <div>
                <!-- Sticky Box -->
                <div class="glass-panel p-6 md:p-8 rounded-lg sticky top-28 border border-slate-700 relative overflow-hidden">

                    <!-- Platform Logo -->
                    @php
                        // Trước đây hardcode "STEAM" cho MỌI sản phẩm — sai vì Kinguin bán key đủ nền
                        // tảng (Xbox, PlayStation, Ubisoft, Battle.net...), và game/giftcard/gói đăng ký
                        // đều dùng chung template này. Dùng Product::platformDisplayLabel()/platformIcon()
                        // (map dùng chung cho cả trang danh sách) thay vì hardcode Steam ở đây.
                        $platformLabel = $product->platformDisplayLabel();
                        $platformIcon = \App\Modules\Theme\Models\Product::platformIcon($platformLabel);
                        $showPlatformBox = in_array($product->product_type, [\App\Modules\Theme\Models\Product::TYPE_GAME, \App\Modules\Theme\Models\Product::TYPE_GIFTCARD, null]) && $platformLabel;
                    @endphp
                    @if($showPlatformBox)
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-800">
                        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ __('product.steam_platform_label') }}</h2>
                        <span class="px-4 py-2 bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 text-slate-800 dark:text-white rounded-xl border border-slate-300 dark:border-slate-700 shadow-lg flex items-center gap-2 font-bold tracking-wide">
                            <i class="{{ $platformIcon }} text-xl text-blue-400"></i> {{ mb_strtoupper($platformLabel) }}
                        </span>
                    </div>
                    @endif
                    
                    <!-- Giá tiền -->
                    <div class="mb-8 relative z-10">
                        @if($product->original_price && $product->original_price > $product->price)
                            @php
                                $discount = round((($product->original_price - $product->price) / $product->original_price) * 100);
                            @endphp
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-blue-500/20 text-blue-400 border border-blue-500/30 text-sm font-extrabold px-3 py-1 rounded-lg">-{{ $discount }}%</span>
                                <span class="text-slate-500 line-through font-medium text-lg">{!! \App\Helpers\CurrencyHelper::formatPrice($product->original_price) !!}</span>
                            </div>
                        @endif
                        
                        <div class="text-right">
                            <div class="text-4xl sm:text-5xl font-display font-black text-slate-900 dark:text-white py-2 drop-shadow-md">{!! \App\Helpers\CurrencyHelper::formatPrice($product->price) !!}</div>
                        </div>
                    </div>
                    
                    <!-- Call to Actions -->
                    <div class="mb-10 space-y-4 relative z-10">
                        @if($availableKeysCount > 0)
                            <div class="flex items-center justify-center gap-2 text-emerald-400 text-sm mb-6 font-bold px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                {{ __('product.instant_delivery') }}
                            </div>
                            
                            @auth
                                <form action="{{ route('product.buy', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-lg text-lg font-bold flex items-center justify-center gap-3 transition-colors mb-3 uppercase">
                                        <i class="fa-solid fa-credit-card"></i> {{ __('product.buy_now') }}
                                    </button>
                                </form>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white py-3.5 rounded-lg text-base font-bold flex items-center justify-center gap-2 transition-colors border border-slate-300 dark:border-slate-700 uppercase">
                                        <i class="fa-solid fa-cart-plus"></i> {{ __('product.add_to_cart_full') }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="flex items-center justify-center gap-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-lg text-lg font-bold transition-colors">
                                    <i class="fa-solid fa-right-to-bracket"></i> {{ __('product.login_to_buy') }}
                                </a>
                            @endauth
                        @else
                            <div class="flex items-center justify-center gap-2 text-rose-400 text-sm mb-6 font-bold px-4 py-3 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                                <i class="fa-solid fa-box-open text-lg"></i> {{ __('product.out_of_stock_note') }}
                            </div>
                            <button disabled class="w-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 py-4 rounded-lg text-lg font-bold cursor-not-allowed flex items-center justify-center gap-3 border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-ban"></i> {{ __('product.sold_out') }}
                            </button>
                        @endif
                    </div>
                    
                    <!-- Guarantees Badges (Thiết kế mới tinh tế hơn) -->
                    <div class="grid grid-cols-3 gap-3 pt-6 border-t border-slate-200 dark:border-slate-800 relative z-10">
                        <div class="text-center group">
                            <div class="w-12 h-12 mx-auto rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 mb-2 transition-colors">
                                <i class="fa-solid fa-shield-alt text-xl"></i>
                            </div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">{{ __('product.trust_licensed') }}</span>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 mx-auto rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 mb-2 transition-colors">
                                <i class="fa-solid fa-bolt text-xl"></i>
                            </div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">{{ __('product.trust_speed') }}</span>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 mx-auto rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-2 transition-colors">
                                <i class="fa-solid fa-key text-xl"></i>
                            </div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">{{ __('product.trust_easy') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-12">
            <div class="glass-panel p-6 md:p-10 rounded-xl shadow-xl">
                <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-circle-question text-blue-500"></i> {{ __('product.faq_heading') }} (FAQ)
                </h2>
                <div class="space-y-4">
                    <details class="group bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-3 p-5 font-medium cursor-pointer text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors">
                            {{ __('product.kinguin_faq_0_q') }}
                            <span class="transition group-open:rotate-180">
                                <i class="fa-solid fa-chevron-down text-slate-400"></i>
                            </span>
                        </summary>
                        <div class="p-5 pt-0 text-slate-600 dark:text-slate-400 text-sm leading-relaxed border-t border-slate-200 dark:border-slate-800 mt-2 bg-slate-50 dark:bg-slate-900">
                            {{ __('product.kinguin_faq_0_a') }}
                        </div>
                    </details>
                    
                    <details class="group bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-3 p-5 font-medium cursor-pointer text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors">
                            {{ __('product.kinguin_faq_1_q') }}
                            <span class="transition group-open:rotate-180">
                                <i class="fa-solid fa-chevron-down text-slate-400"></i>
                            </span>
                        </summary>
                        <div class="p-5 pt-0 text-slate-600 dark:text-slate-400 text-sm leading-relaxed border-t border-slate-200 dark:border-slate-800 mt-2 bg-slate-50 dark:bg-slate-900">
                            {{ __('product.kinguin_faq_1_a') }}
                        </div>
                    </details>
                    
                    <details class="group bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-3 p-5 font-medium cursor-pointer text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors">
                            {{ __('product.kinguin_faq_5_q') }}
                            <span class="transition group-open:rotate-180">
                                <i class="fa-solid fa-chevron-down text-slate-400"></i>
                            </span>
                        </summary>
                        <div class="p-5 pt-0 text-slate-600 dark:text-slate-400 text-sm leading-relaxed border-t border-slate-200 dark:border-slate-800 mt-2 bg-slate-50 dark:bg-slate-900">
                            {{ __('product.kinguin_faq_5_a') }}
                        </div>
                    </details>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile Sticky Bottom Action Bar -->
    <div class="fixed bottom-0 left-0 w-full lg:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 p-4 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.3)] z-50 flex gap-3">
        @if($availableKeysCount > 0)
            @auth
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-1/3">
                    @csrf
                    <button type="submit" class="w-full h-12 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-colors border border-slate-300 dark:border-slate-700">
                        <i class="fa-solid fa-cart-plus"></i>
                    </button>
                </form>
                <form action="{{ route('product.buy', $product->id) }}" method="POST" class="w-2/3">
                    @csrf
                    <button type="submit" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-colors shadow-lg shadow-blue-500/30 uppercase">
                        <i class="fa-solid fa-credit-card"></i> {{ __('product.buy_now') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-colors shadow-lg shadow-blue-500/30">
                    <i class="fa-solid fa-right-to-bracket"></i> {{ __('product.login_to_buy') }}
                </a>
            @endauth
        @else
            <button disabled class="w-full h-12 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-xl text-sm font-bold cursor-not-allowed flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-ban"></i> {{ __('product.sold_out') }}
            </button>
        @endif
    </div>

@endsection
