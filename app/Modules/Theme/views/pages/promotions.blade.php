@extends('theme::layouts.app')

@section('title', __('promopage.page_title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-4">
            {!! __('promopage.hero_title') !!}
        </h1>
        <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            {{ __('promopage.hero_subtitle') }}
        </p>
    </div>



    <!-- Promo Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Promo 1 -->
        <div class="glass-card rounded-2xl overflow-hidden group">
            <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden flex items-center justify-center p-6">
                <i class="fa-solid fa-gem text-6xl text-white/20 absolute -right-4 -bottom-4 transform -rotate-12 group-hover:rotate-0 transition-transform duration-500"></i>
                <div class="text-center relative z-10 text-white">
                    <div class="text-4xl font-black mb-1">{{ __('promopage.promo1_badge_pct') }}</div>
                    <div class="font-bold uppercase tracking-wider text-sm opacity-90">{{ __('promopage.promo1_badge_label') }}</div>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('promopage.promo1_title') }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">
                    {{ __('promopage.promo1_desc') }}
                </p>
                <div class="flex items-center text-sm font-semibold text-emerald-500">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ __('promopage.status_ongoing') }}
                </div>
            </div>
        </div>

        <!-- Promo 2 -->
        <div class="glass-card rounded-2xl overflow-hidden group">
            <div class="h-48 bg-gradient-to-br from-rose-500 to-orange-500 relative overflow-hidden flex items-center justify-center p-6">
                <i class="fa-solid fa-fire text-6xl text-white/20 absolute -right-4 -bottom-4 transform -rotate-12 group-hover:rotate-0 transition-transform duration-500"></i>
                <div class="text-center relative z-10 text-white">
                    <div class="text-4xl font-black mb-1">{{ __('promopage.promo2_badge_pct') }}</div>
                    <div class="font-bold uppercase tracking-wider text-sm opacity-90">{{ __('promopage.promo2_badge_label') }}</div>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('promopage.promo2_title') }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">
                    {{ __('promopage.promo2_desc') }}
                </p>
                <div class="flex items-center text-sm font-semibold text-emerald-500">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ __('promopage.status_ongoing') }}
                </div>
            </div>
        </div>

        <!-- Promo 3 -->
        <div class="glass-card rounded-2xl overflow-hidden group">
            <div class="h-48 bg-gradient-to-br from-blue-500 to-cyan-500 relative overflow-hidden flex items-center justify-center p-6">
                <i class="fa-solid fa-users text-6xl text-white/20 absolute -right-4 -bottom-4 transform -rotate-12 group-hover:rotate-0 transition-transform duration-500"></i>
                <div class="text-center relative z-10 text-white">
                    <div class="text-4xl font-black mb-1">{{ __('promopage.promo3_badge_pct') }}</div>
                    <div class="font-bold uppercase tracking-wider text-sm opacity-90">{{ __('promopage.promo3_badge_label') }}</div>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('promopage.promo3_title') }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">
                    {{ __('promopage.promo3_desc') }}
                </p>
                <div class="flex items-center text-sm font-semibold text-slate-400">
                    <i class="fa-solid fa-clock mr-2"></i> {{ __('promopage.status_coming_soon') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
