@extends('theme::layouts.app')

@section('title', $sound->title . ' - Sound World - SoftwarePays')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="text-sm text-slate-500 dark:text-slate-400 mb-6">
        <a href="{{ route('sounds.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Sound World</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ $sound->title }}</span>
    </div>

@php
    $colors = [
        'bg-[#FF0000]', // Red
        'bg-[#0000FF]', // Blue
        'bg-[#008000]', // Green
        'bg-[#FFFF00]', // Yellow
        'bg-[#800080]', // Purple
        'bg-[#00FFFF]', // Cyan
        'bg-[#FF00FF]', // Magenta
        'bg-[#FFA500]', // Orange
    ];
    $c = $colors[$sound->id % count($colors)];
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">

    <div class="text-sm text-slate-500 dark:text-slate-400 mb-8 font-medium">
        <a href="{{ route('sounds.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ __('soundshow.breadcrumb_home') }}</a>
        <span class="mx-2">></span>
        <span class="text-slate-800 dark:text-slate-200">{{ $sound->title }}</span>
    </div>

    <h1 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-12 font-display uppercase tracking-tight break-words">{{ $sound->title }}</h1>

    <div class="sound-button-wrapper inline-block mb-10" data-slug="{{ $sound->slug }}" data-play-url="{{ $sound->play_url }}">
        <style>
            .myinstants-btn-huge {
                background-image: url('https://www.myinstants.com/media/images/transparent_button_sprite.png') !important;
                background-size: 200% 100% !important;
                background-position: 0% 0% !important;
                background-repeat: no-repeat !important;
                transition: none !important;
            }
            .myinstants-btn-huge:active,
            .sound-button-wrapper.playing .myinstants-btn-huge {
                background-position: 100% 0% !important;
            }
        </style>
        <div class="relative mb-8 flex items-center justify-center mx-auto" style="width: 200px; height: 200px; filter: drop-shadow(0 15px 25px rgba(0,0,0,0.4));">
            <!-- Colored Base -->
            <div class="absolute rounded-full {{ $c }} z-0" style="width: 86%; height: 86%;"></div>
            
            <!-- Transparent Sprite Button -->
            <button type="button" class="play-btn myinstants-btn-huge absolute inset-0 z-10 focus:outline-none cursor-pointer">
                <!-- Progress Overlay -->
                <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none opacity-0 group-[.playing]:opacity-100" viewBox="0 0 100 100">
                    <circle class="progress-ring text-black/20" stroke-width="4" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" style="stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 0.1s linear;"></circle>
                </svg>
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="text-slate-600 dark:text-slate-400 mb-10">
        <div class="flex items-center justify-center gap-2 mb-3 text-lg md:text-xl">
            <i class="fa-solid fa-heart text-red-500 animate-pulse"></i> 
            <span class="font-bold text-slate-900 dark:text-white like-count">{{ number_format($sound->like_count) }}</span>
            {{ __('soundshow.like_suffix') }}
        </div>
        <div class="text-sm font-medium">
            {{ __('soundshow.uploaded_by') }} SoftwarePays &bull; {{ number_format($sound->play_count) }} {{ __('soundshow.plays_suffix') }} &bull; {{ number_format($sound->download_count) }} {{ __('soundshow.downloads_suffix') }}
        </div>
    </div>

    <!-- Actions (Like Myinstants style) -->
    <div class="flex flex-wrap justify-center gap-3 mb-12 max-w-3xl mx-auto">
        <button onclick="likeSound('{{ $sound->slug }}', this)" class="like-btn flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-regular fa-heart text-lg"></i> {{ __('soundshow.like_button') }}
        </button>
        <button onclick="shareSound('{{ $sound->slug }}'); window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('sounds.show', $sound->slug)) }}', '_blank')" class="flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-brands fa-facebook-f text-lg"></i> {{ __('soundshow.share_button') }}
        </button>
        <button onclick="copySoundLink('{{ route('sounds.show', $sound->slug) }}', this)" class="flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-solid fa-link text-lg"></i> {{ __('soundshow.copy_link_button') }}
        </button>
        <a href="{{ route('sounds.download', $sound->slug) }}" class="flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-solid fa-download text-lg"></i> {{ __('soundshow.download_button') }}
        </a>
    </div>

    <!-- Embed Code -->
    <div class="max-w-xl mx-auto mb-12">
        <p class="text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider mb-2">{{ __('soundshow.embed_title') }}</p>
        <textarea readonly class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-600 dark:text-slate-300 font-mono text-center focus:outline-none focus:border-blue-500 cursor-text" rows="2" onclick="this.select()"><iframe width="110" height="200" src="{{ route('sounds.show', $sound->slug) }}" frameborder="0"></iframe></textarea>
    </div>

        <div>
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-4">{{ __('soundshow.related_title') }}</h3>
            <div class="flex flex-col gap-3">
                @forelse($related as $r)
                    <a href="{{ route('sounds.show', $r->slug) }}" class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden shrink-0">
                            @if($r->thumbnail_url)
                                <img src="{{ $r->thumbnail_url }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-music text-slate-300 dark:text-slate-600"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900 dark:text-white line-clamp-1 text-sm">{{ $r->title }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400"><i class="fa-solid fa-headphones"></i> {{ number_format($r->play_count) }}</div>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('soundshow.no_related') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<audio id="global-player" class="hidden"></audio>

<script>
    window.SOUND_CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/sound-player.js?v=' . time()) }}"></script>
@endsection
