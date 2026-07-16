@extends('theme::layouts.app')

@section('title', __('catalog.card_page_title') . ' - SoftwarePays')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">{{ __('nav.home') }}</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ __('catalog.card_page_title') }}</span>
    </nav>

    <div class="flex items-center gap-3 mb-8">
        <i class="fa-solid fa-credit-card text-3xl text-blue-500"></i>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ __('catalog.card_page_title') }}</h1>
    </div>

    @if($products->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-16 text-center text-slate-500">
            <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('catalog.no_card_providers') }}</h3>
            <p>{{ __('catalog.please_check_back') }}</p>
        </div>
    @else
        @php
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
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                @php
                    $faceValues = $product->cardPackages->pluck('face_value');
                    $minFace = $faceValues->min();
                    $maxFace = $faceValues->max();
                    $telco = str_replace('card_', '', $product->wholesale_product_id ?? '');
                    $brand = $brandMap[$telco] ?? ['from' => '#3b82f6', 'to' => '#1e40af', 'icon' => 'fa-solid fa-credit-card'];
                    // Ưu tiên % Admin nhập ở /admin/card/{id}/packages, không gộp chung với chiết khấu vendor.
                    $maxDiscountPercent = $product->cardPackages
                        ->filter(fn ($pkg) => $pkg->price < $pkg->face_value)
                        ->map(fn ($pkg) => $pkg->promo_discount_percent > 0
                            ? round($pkg->promo_discount_percent)
                            : round((($pkg->face_value - $pkg->price) / $pkg->face_value) * 100))
                        ->max();
                @endphp
                <a href="{{ route('catalog.card.show', $product->id) }}" class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition flex flex-col group">
                    <div class="h-20 relative" style="background: linear-gradient(135deg, {{ $brand['from'] }}, {{ $brand['to'] }});">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 18px 18px;"></div>
                        @if($maxDiscountPercent)
                            <span class="discount-badge" style="left: auto; right: 8px;">
                                <strong>-{{ $maxDiscountPercent }}%</strong>
                                <small>{{ __('productCard.discount') }}</small>
                            </span>
                        @endif
                        <div class="absolute -bottom-7 left-6 w-[68px] h-[68px] rounded-xl bg-white dark:bg-slate-800 shadow-md flex items-center justify-center border-4 border-white dark:border-slate-800 overflow-hidden">
                            @if($product->header_image)
                                <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-12 h-12 object-contain">
                            @else
                                <i class="{{ $brand['icon'] }} text-2xl" style="color: {{ $brand['from'] }}"></i>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 pt-10 flex flex-col flex-1">
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-snug mb-2 group-hover:text-blue-500 transition-colors">{{ $product->name }}</h3>

                        @if($product->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ Str::limit(strip_tags($product->description), 100) }}</p>
                        @endif

                        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-xs text-slate-500">{{ $product->cardPackages->count() }} {{ __('catalog.denominations_count') }}</span>
                            @if($faceValues->isNotEmpty())
                                <span class="font-bold" style="color: {{ $brand['from'] }}">
                                    @if($minFace == $maxFace)
                                        {{ number_format($minFace) }}đ
                                    @else
                                        {{ number_format($minFace) }}đ - {{ number_format($maxFace) }}đ
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
