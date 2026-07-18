@extends('theme::layouts.app')

@section('title', $Gif->title . ' - Gif Meme - SoftwarePays')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">

    <div class="text-sm text-slate-500 dark:text-slate-400 mb-8 font-medium">
        <a href="{{ route('Gifs.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ __('Gifshow.breadcrumb_home') }}</a>
        <span class="mx-2">></span>
        <span class="text-slate-800 dark:text-slate-200">{{ $Gif->title }}</span>
    </div>

    <h1 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-12 font-display uppercase tracking-tight break-words">{{ $Gif->title }}</h1>

    <div id="gif-view-root" data-slug="{{ $Gif->slug }}" class="inline-block mb-16 mx-auto w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl bg-black">
        <img src="{{ $Gif->play_url }}" alt="{{ $Gif->title }}" class="w-full h-auto object-contain max-h-[60vh]">
    </div>

    <!-- Stats -->
    <div class="text-slate-600 dark:text-slate-400 mb-10">
        <div class="flex items-center justify-center gap-2 mb-3 text-lg md:text-xl">
            <i class="fa-solid fa-heart text-red-500 animate-pulse"></i> 
            <span class="font-bold text-slate-900 dark:text-white like-count">{{ number_format($Gif->like_count) }}</span>
            {{ __('Gifshow.like_suffix') }}
        </div>
        <div class="text-sm font-medium">
            {{ __('Gifshow.uploaded_by') }} SoftwarePays &bull; {{ number_format($Gif->play_count) }} {{ __('Gifshow.plays_suffix') }} &bull; {{ number_format($Gif->download_count) }} {{ __('Gifshow.downloads_suffix') }}
        </div>
    </div>

    <!-- Actions (Like Myinstants style) -->
    <div class="flex flex-wrap justify-center gap-3 mb-12 max-w-3xl mx-auto">
        <button onclick="likeGif('{{ $Gif->slug }}', this)" class="like-btn flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-regular fa-heart text-lg"></i> {{ __('Gifshow.like_button') }}
        </button>
        <button onclick="shareGif('{{ $Gif->slug }}'); window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('Gifs.show', $Gif->slug)) }}', '_blank')" class="flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-brands fa-facebook-f text-lg"></i> {{ __('Gifshow.share_button') }}
        </button>
        <button onclick="copyGifLink('{{ route('Gifs.show', $Gif->slug) }}', this)" class="flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-solid fa-link text-lg"></i> {{ __('Gifshow.copy_link_button') }}
        </button>
        <a href="{{ route('Gifs.download', $Gif->slug) }}" class="flex-1 min-w-[150px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all shadow-md active:scale-95">
            <i class="fa-solid fa-download text-lg"></i> {{ __('Gifshow.download_button') }}
        </a>
    </div>

    <!-- Embed Code -->
    <div class="max-w-xl mx-auto mb-12">
        <p class="text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider mb-2">{{ __('Gifshow.embed_title') }}</p>
        <textarea readonly class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-600 dark:text-slate-300 font-mono text-center focus:outline-none focus:border-blue-500 cursor-text" rows="2" onclick="this.select()"><iframe width="110" height="200" src="{{ route('Gifs.show', $Gif->slug) }}" frameborder="0"></iframe></textarea>
    </div>

        <div class="mt-24 border-t border-slate-200 dark:border-slate-800 pt-16">
            <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider mb-10 font-display">
                <i class="fa-solid fa-layer-group text-blue-500 mr-2"></i> {{ __('Gifshow.related_title') }}
            </h3>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-x-2 gap-y-8">
                @forelse($related as $r)
                    @include('gifmeme::theme.partials.card', ['Gif' => $r])
                @empty
                    <div class="col-span-full text-center py-10">
                        <i class="fa-solid fa-ghost text-4xl text-slate-300 dark:text-slate-700 mb-3"></i>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Gifshow.no_related') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    window.Gif_CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/gif-player.js?v=' . time()) }}"></script>
@endsection



