@extends('theme::layouts.app')

@section('title', $product->name . ' - ' . __('productcard.meta_title_suffix'))

@php
    // Màu thương hiệu theo mã nhà mạng/game (wholesale_product_id dạng "card_VTT") — không có
    // logo ảnh thật cho các nhà cung cấp thẻ, dùng màu đặc trưng để trang đỡ trống trải.
    $telco = str_replace('card_', '', $product->wholesale_product_id ?? '');
    $brandMap = [
        'VTT'  => ['from' => '#dc2626', 'to' => '#991b1b', 'icon' => 'fa-solid fa-sim-card'],
        'VMS'  => ['from' => '#2563eb', 'to' => '#1e3a8a', 'icon' => 'fa-solid fa-sim-card'],
        'VNP'  => ['from' => '#0891b2', 'to' => '#0e7490', 'icon' => 'fa-solid fa-sim-card'],
        'VNM'  => ['from' => '#ea580c', 'to' => '#9a3412', 'icon' => 'fa-solid fa-sim-card'],
        'GAR'  => ['from' => '#f97316', 'to' => '#c2410c', 'icon' => 'fa-solid fa-gamepad'],
        'ZING' => ['from' => '#9333ea', 'to' => '#6b21a8', 'icon' => 'fa-solid fa-gamepad'],
        'GATE' => ['from' => '#3b82f6', 'to' => '#1d4ed8', 'icon' => 'fa-solid fa-gamepad'],
        'VTC'  => ['from' => '#16a34a', 'to' => '#166534', 'icon' => 'fa-solid fa-gamepad'],
    ];
    $brand = $brandMap[$telco] ?? ['from' => '#3b82f6', 'to' => '#1e40af', 'icon' => 'fa-solid fa-credit-card'];
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 pb-24">

    <!-- Breadcrumb -->
    <nav class="text-sm text-slate-500 dark:text-slate-400 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">{{ __('nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('catalog.card') }}" class="hover:text-blue-600 transition">{{ __('catalog.card_page_title') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-700 dark:text-slate-300">{{ $product->name }}</span>
    </nav>

    <!-- Hero Banner -->
    <div class="relative rounded-2xl overflow-hidden mb-8 shadow-lg" style="background: linear-gradient(135deg, {{ $brand['from'] }}, {{ $brand['to'] }});">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative px-6 py-10 md:px-12 md:py-14 flex items-center gap-6">
            @if($product->header_image)
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-white shadow-md flex items-center justify-center shrink-0 overflow-hidden p-2">
                    <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                </div>
            @else
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center shrink-0">
                    <i class="{{ $brand['icon'] }} text-3xl md:text-4xl text-white"></i>
                </div>
            @endif
            <div>
                <h1 class="text-2xl md:text-4xl font-display font-bold text-white mb-2 drop-shadow">{{ $product->name }}</h1>
                <p class="text-white/80 text-sm md:text-base max-w-xl">
                    @if($product->description)
                        {{ Str::limit(strip_tags($product->description), 140) }}
                    @else
                        {{ __('productcard.default_description') }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Trust Badges -->
    <div class="grid grid-cols-3 gap-3 md:gap-4 mb-8">
        <div class="glass-card rounded-xl p-4 text-center border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-bolt text-amber-500 text-xl mb-2"></i>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('productcard.badge_instant') }}</p>
        </div>
        <div class="glass-card rounded-xl p-4 text-center border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-shield-alt text-emerald-500 text-xl mb-2"></i>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('productcard.badge_valid') }}</p>
        </div>
        <div class="glass-card rounded-xl p-4 text-center border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-headset text-blue-500 text-xl mb-2"></i>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('productcard.badge_support') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: chọn mệnh giá + hướng dẫn + FAQ -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-list-check" style="color: {{ $brand['from'] }}"></i> {{ __('productcard.choose_denomination') }}
                </h2>

                @if($packages->isEmpty())
                    <p class="text-slate-500">{{ __('productcard.no_denominations') }}</p>
                @else
                    <form id="card-form" action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
                            @foreach($packages as $i => $pkg)
                            <label class="relative flex items-center justify-between p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer hover:border-blue-400 has-[:checked]:shadow-md transition"
                                style="{{ $i === 0 ? 'border-color:' . $brand['from'] . '; background: linear-gradient(135deg, ' . $brand['from'] . '0d, transparent);' : '' }}"
                                onclick="document.querySelectorAll('#card-form label').forEach(l => { l.style.borderColor=''; l.style.background=''; }); this.style.borderColor='{{ $brand['from'] }}'; this.style.background='linear-gradient(135deg, {{ $brand['from'] }}0d, transparent)';">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="variant_id" value="{{ $pkg->id }}" {{ $i === 0 ? 'checked' : '' }} class="w-4 h-4" style="accent-color: {{ $brand['from'] }}">
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ number_format($pkg->face_value) }}đ</p>
                                        @if($pkg->price < $pkg->face_value)
                                            @php
                                                // Ưu tiên hiện đúng % Admin đã nhập ở /admin/card/{id}/packages (chiết khấu cho khách) thay vì
                                                // % gộp chung với chiết khấu vendor (santhecao) — tránh lệch số so với số Admin thực sự đặt.
                                                // Không có promo riêng thì mới tính % dựa trên giá thật (trường hợp chỉ có chiết khấu vendor).
                                                $displayPercent = $pkg->promo_discount_percent > 0
                                                    ? round($pkg->promo_discount_percent)
                                                    : round((($pkg->face_value - $pkg->price) / $pkg->face_value) * 100);
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                                -{{ $displayPercent }}% <span class="font-medium opacity-90">{{ __('productCard.discount') }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-right">
                                    @if($pkg->price != $pkg->face_value)
                                        <span class="block text-xs text-slate-400 line-through">{{ number_format($pkg->face_value) }}đ</span>
                                    @endif
                                    <span class="font-bold" style="color: {{ $brand['from'] }}">{!! \App\Helpers\CurrencyHelper::formatPrice($pkg->price) !!}</span>
                                </span>
                            </label>
                            @endforeach
                        </div>

                        <p class="text-xs text-slate-400 mb-6"><i class="fa-solid fa-circle-info mr-1"></i> {{ __('productcard.discount_note') }}</p>

                        @auth
                            <button type="submit" class="w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg hover:opacity-90" style="background: linear-gradient(135deg, {{ $brand['from'] }}, {{ $brand['to'] }});">
                                <i class="fa-solid fa-cart-plus mr-2"></i> {{ __('product.add_to_cart_full') }}
                            </button>
                        @else
                            <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="block text-center w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl transition">
                                {{ __('product.login_to_buy') }}
                            </a>
                        @endauth
                    </form>
                @endif
            </div>

            <!-- Cách thức hoạt động -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project" style="color: {{ $brand['from'] }}"></i> {{ __('productcard.how_it_works') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-white font-bold text-lg mb-3" style="background: {{ $brand['from'] }}">1</div>
                        <p class="font-bold text-slate-800 dark:text-white text-sm mb-1">{{ __('productcard.choose_denomination') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('productcard.step1_desc') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-white font-bold text-lg mb-3" style="background: {{ $brand['from'] }}">2</div>
                        <p class="font-bold text-slate-800 dark:text-white text-sm mb-1">{{ __('productcard.step2_title') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('productcard.step2_desc') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-white font-bold text-lg mb-3" style="background: {{ $brand['from'] }}">3</div>
                        <p class="font-bold text-slate-800 dark:text-white text-sm mb-1">{{ __('productcard.step3_title') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('productcard.step3_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question" style="color: {{ $brand['from'] }}"></i> {{ __('product.faq_heading') }}
                </h2>
                <div class="space-y-3">
                    <details class="group bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-3 p-4 font-medium cursor-pointer text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors text-sm">
                            {{ __('productcard.faq_q1') }}
                            <i class="fa-solid fa-chevron-down text-slate-400 transition group-open:rotate-180"></i>
                        </summary>
                        <div class="p-4 pt-0 text-slate-600 dark:text-slate-400 text-sm leading-relaxed border-t border-slate-200 dark:border-slate-800 mt-2">
                            {{ __('productcard.faq_a1') }}
                        </div>
                    </details>
                    <details class="group bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-3 p-4 font-medium cursor-pointer text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors text-sm">
                            {{ __('productcard.faq_q2') }}
                            <i class="fa-solid fa-chevron-down text-slate-400 transition group-open:rotate-180"></i>
                        </summary>
                        <div class="p-4 pt-0 text-slate-600 dark:text-slate-400 text-sm leading-relaxed border-t border-slate-200 dark:border-slate-800 mt-2">
                            {{ __('productcard.faq_a2') }}
                        </div>
                    </details>
                    <details class="group bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-3 p-4 font-medium cursor-pointer text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors text-sm">
                            {{ __('productcard.faq_q3') }}
                            <i class="fa-solid fa-chevron-down text-slate-400 transition group-open:rotate-180"></i>
                        </summary>
                        <div class="p-4 pt-0 text-slate-600 dark:text-slate-400 text-sm leading-relaxed border-t border-slate-200 dark:border-slate-800 mt-2">
                            {{ __('productcard.faq_a3') }}
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <!-- Cột phải: tóm tắt -->
        <div>
            <div class="glass-card rounded-2xl p-6 sticky top-24 border border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">{{ __('productcard.summary_heading') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('productcard.provider_label') }}</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $product->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('productcard.denom_count_label') }}</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $packages->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('productcard.delivery_method_label') }}</span>
                        <span class="font-bold text-emerald-500">{{ __('productcard.delivery_auto') }}</span>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2 text-xs text-slate-400">
                    <i class="fa-solid fa-shield-alt" style="color: {{ $brand['from'] }}"></i>
                    {{ __('productcard.fraud_note') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
