@extends('theme::layouts.app')

@section('title', __('pages_other.page_title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-4">
            {{ __('pages_other.hero_title') }}
        </h1>
        <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            {{ __('pages_other.hero_subtitle') }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
        <!-- Sound Meme -->
        <a href="{{ route('sounds.index') }}" class="glass-card rounded-2xl overflow-hidden group hover:-translate-y-1 hover:shadow-xl transition-all">
            <div class="h-40 bg-gradient-to-br from-orange-500 to-rose-600 relative overflow-hidden flex items-center justify-center">
                <i class="fa-solid fa-volume-high text-6xl text-white/90"></i>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('pages_other.soundmeme_title') }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">
                    {{ __('pages_other.soundmeme_desc') }}
                </p>
                <div class="flex items-center text-sm font-semibold text-orange-500">
                    {{ __('pages_other.explore_now') }} <i class="fa-solid fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>

        <!-- GIF Meme -->
        <a href="{{ route('Gifs.index') }}" class="glass-card rounded-2xl overflow-hidden group hover:-translate-y-1 hover:shadow-xl transition-all">
            <div class="h-40 bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden flex items-center justify-center">
                <i class="fa-solid fa-images text-6xl text-white/90"></i>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('pages_other.gif_title') }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">
                    {{ __('pages_other.gif_desc') }}
                </p>
                <div class="flex items-center text-sm font-semibold text-indigo-500">
                    {{ __('pages_other.explore_now') }} <i class="fa-solid fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
