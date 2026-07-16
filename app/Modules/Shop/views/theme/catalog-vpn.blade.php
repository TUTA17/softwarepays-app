@extends('theme::layouts.app')

@section('title', __('home_categories.vpn') . ' - SoftwarePays')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">{{ __('nav.home') }}</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ __('home_categories.vpn') }}</span>
    </nav>

    <div class="flex items-center gap-3 mb-8">
        <i class="fa-solid fa-shield-alt text-3xl text-blue-500"></i>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ __('home_categories.vpn') }}</h1>
    </div>

    @if($products->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-16 text-center text-slate-500">
            <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('catalog.no_vpn_servers') }}</h3>
            <p>{{ __('catalog.please_check_back') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                @php
                    $prices = $product->vpnPackages->pluck('price');
                    $minPrice = $prices->min();
                    $maxPrice = $prices->max();
                @endphp
                <a href="{{ route('catalog.vpn.show', $product->id) }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:border-blue-400 hover:shadow-md transition flex flex-col">
                    <div class="relative h-56 w-full overflow-hidden bg-gradient-to-br from-blue-500/20 to-blue-700/10 flex items-center justify-center shrink-0">
                        @if($product->header_image)
                            <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                        @else
                            <i class="fa-solid fa-shield-alt text-5xl text-blue-500/40"></i>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-snug mb-3">{{ $product->name }}</h3>

                    @if($product->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ Str::limit(strip_tags($product->description), 100) }}</p>
                    @endif

                    <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-xs text-slate-500">{{ $product->vpnPackages->count() }} {{ __('catalog.package_count') }}</span>
                        @if($prices->isNotEmpty())
                            <span class="font-bold text-blue-600 dark:text-blue-400">
                                @if($minPrice == $maxPrice)
                                    {!! \App\Helpers\CurrencyHelper::formatPrice($minPrice) !!}
                                @else
                                    {!! \App\Helpers\CurrencyHelper::formatPrice($minPrice) !!} - {!! \App\Helpers\CurrencyHelper::formatPrice($maxPrice) !!}
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
