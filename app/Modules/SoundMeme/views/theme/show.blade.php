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
    $colorSets = [
        ['light' => '#ef4444', 'dark' => '#b91c1c', 'base' => '#7f1d1d'], // Red
        ['light' => '#3b82f6', 'dark' => '#1d4ed8', 'base' => '#1e3a8a'], // Blue
        ['light' => '#10b981', 'dark' => '#047857', 'base' => '#064e3b'], // Green
        ['light' => '#f59e0b', 'dark' => '#b45309', 'base' => '#78350f'], // Yellow
        ['light' => '#8b5cf6', 'dark' => '#5b21b6', 'base' => '#4c1d95'], // Purple
        ['light' => '#06b6d4', 'dark' => '#0e7490', 'base' => '#164e63'], // Cyan
        ['light' => '#ec4899', 'dark' => '#be185d', 'base' => '#831843'], // Pink
        ['light' => '#f97316', 'dark' => '#c2410c', 'base' => '#7c2d12'], // Orange
    ];
    // Cùng công thức hash với card.blade.php để 1 sound luôn ra đúng 1 màu ở mọi nơi trên site.
    $c = $colorSets[hexdec(substr(md5($sound->id . 'salt'), -5)) % count($colorSets)];
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">

    <div class="text-sm text-slate-500 dark:text-slate-400 mb-8 font-medium">
        <a href="{{ route('sounds.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ __('soundshow.breadcrumb_home') }}</a>
        <span class="mx-2">></span>
        <span class="text-slate-800 dark:text-slate-200">{{ $sound->title }}</span>
    </div>

    <h1 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-12 font-display uppercase tracking-tight break-words">{{ $sound->title }}</h1>

    <div class="sound-button-wrapper inline-block mb-10" data-slug="{{ $sound->slug }}" data-play-url="{{ $sound->play_url }}">
        <div class="mb-8" style="filter: drop-shadow(0 15px 25px rgba(0,0,0,0.4));">
            @include('soundmeme::theme.partials.button-face', ['colorSet' => $c, 'size' => 200])
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
                    @php
                        $rc = $colorSets[hexdec(substr(md5($r->id . 'salt'), -5)) % count($colorSets)];
                    @endphp
                    <a href="{{ route('sounds.show', $r->slug) }}" class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow">
                        <div class="relative w-12 h-12 rounded-xl shrink-0 shadow-inner" style="background-image: radial-gradient({{ $rc['light'] }}, {{ $rc['dark'] }});">
                            <div class="absolute top-[28%] left-0 right-0 flex justify-center gap-1 pointer-events-none">
                                <div class="w-2 h-[3px] bg-white rounded-full"></div>
                                <div class="w-2 h-[3px] bg-white rounded-full"></div>
                            </div>
                            <div class="absolute bottom-[28%] left-[30%] right-[30%] h-[3px] bg-black/30 rounded-full pointer-events-none"></div>
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
