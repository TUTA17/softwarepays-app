@extends('theme::layouts.app')

@section('title', __('smmpage.page_title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-4">
            {!! __('smmpage.hero_title') !!}
        </h1>
        <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            {{ __('smmpage.hero_subtitle') }}
        </p>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
        <!-- Facebook -->
        <div class="glass-card rounded-2xl p-8 border border-slate-200 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-brands fa-facebook-f text-9xl text-blue-600"></i>
            </div>
            <div class="w-16 h-16 rounded-xl bg-blue-600 text-white flex items-center justify-center text-3xl mb-6 shadow-lg shadow-blue-500/30 relative z-10">
                <i class="fa-brands fa-facebook-f"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 relative z-10">{{ __('smmpage.fb_heading') }}</h2>
            <ul class="space-y-3 mb-8 relative z-10">
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.fb_feature1') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.fb_feature2') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.fb_feature3') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.fb_feature4') }}</li>
            </ul>
            <a href="{{ route('pages.support') }}" class="inline-flex items-center justify-center w-full py-3 px-6 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white dark:bg-slate-800 dark:hover:bg-blue-600 font-bold transition-all relative z-10">
                {{ __('smmpage.get_quote_button') }} <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

        <!-- TikTok -->
        <div class="glass-card rounded-2xl p-8 border border-slate-200 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-brands fa-tiktok text-9xl text-slate-900 dark:text-white"></i>
            </div>
            <div class="w-16 h-16 rounded-xl bg-slate-900 dark:bg-slate-700 text-white flex items-center justify-center text-3xl mb-6 shadow-lg relative z-10">
                <i class="fa-brands fa-tiktok"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 relative z-10">{{ __('smmpage.tiktok_heading') }}</h2>
            <ul class="space-y-3 mb-8 relative z-10">
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.tiktok_feature1') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.tiktok_feature2') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.tiktok_feature3') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.tiktok_feature4') }}</li>
            </ul>
            <a href="{{ route('pages.support') }}" class="inline-flex items-center justify-center w-full py-3 px-6 rounded-lg bg-slate-100 hover:bg-slate-900 text-slate-900 hover:text-white dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700 font-bold transition-all relative z-10">
                {{ __('smmpage.get_quote_button') }} <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

        <!-- Instagram -->
        <div class="glass-card rounded-2xl p-8 border border-slate-200 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-brands fa-instagram text-9xl text-fuchsia-600"></i>
            </div>
            <div class="w-16 h-16 rounded-xl bg-gradient-to-tr from-amber-500 via-rose-500 to-fuchsia-600 text-white flex items-center justify-center text-3xl mb-6 shadow-lg shadow-rose-500/30 relative z-10">
                <i class="fa-brands fa-instagram"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 relative z-10">{{ __('smmpage.ig_heading') }}</h2>
            <ul class="space-y-3 mb-8 relative z-10">
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.ig_feature1') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.ig_feature2') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.ig_feature3') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.ig_feature4') }}</li>
            </ul>
            <a href="{{ route('pages.support') }}" class="inline-flex items-center justify-center w-full py-3 px-6 rounded-lg bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white dark:bg-slate-800 dark:hover:bg-rose-600 font-bold transition-all relative z-10">
                {{ __('smmpage.get_quote_button') }} <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

        <!-- YouTube -->
        <div class="glass-card rounded-2xl p-8 border border-slate-200 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-brands fa-youtube text-9xl text-red-600"></i>
            </div>
            <div class="w-16 h-16 rounded-xl bg-red-600 text-white flex items-center justify-center text-3xl mb-6 shadow-lg shadow-red-500/30 relative z-10">
                <i class="fa-brands fa-youtube"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 relative z-10">{{ __('smmpage.yt_heading') }}</h2>
            <ul class="space-y-3 mb-8 relative z-10">
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.yt_feature1') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.yt_feature2') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.yt_feature3') }}</li>
                <li class="flex items-center text-slate-600 dark:text-slate-300"><i class="fa-solid fa-check text-emerald-500 mr-3"></i> {{ __('smmpage.yt_feature4') }}</li>
            </ul>
            <a href="{{ route('pages.support') }}" class="inline-flex items-center justify-center w-full py-3 px-6 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white dark:bg-slate-800 dark:hover:bg-red-600 font-bold transition-all relative z-10">
                {{ __('smmpage.get_quote_button') }} <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Info CTA -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-700 rounded-3xl p-8 md:p-12 text-center shadow-xl border border-slate-700">
        <h3 class="text-2xl md:text-3xl font-display font-bold text-white mb-4">{{ __('smmpage.cta_heading') }}</h3>
        <p class="text-slate-300 mb-8 max-w-2xl mx-auto">{{ __('smmpage.cta_subtitle') }}</p>
        <a href="{{ route('pages.support') }}" class="inline-flex items-center px-8 py-3 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-lg transition-colors shadow-lg shadow-blue-500/25">
            <i class="fa-solid fa-headset mr-2"></i> {{ __('smmpage.cta_button') }}
        </a>
    </div>
</div>
@endsection
