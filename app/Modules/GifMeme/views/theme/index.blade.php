@extends('theme::layouts.app')

@section('title', 'Gif Meme - SoftwarePays')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-display font-black text-slate-900 dark:text-white mb-2">
            <i class="fa-solid fa-images text-blue-600 dark:text-blue-400"></i> Gif Meme
        </h1>
        <p class="text-slate-500 dark:text-slate-400">{{ __('Gifindex.tagline') }}</p>
    </div>

    @if($isBrowsingDefault)
        @if($editorsPicks->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-500"></i> {{ __('Gifindex.editors_picks') }}
            </h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-4 gap-y-8">
                @foreach($editorsPicks as $Gif)
                    <div>
                        @include('gifmeme::theme.partials.card', ['Gif' => $Gif])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($topMeme->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-fire text-red-500"></i> Top Meme
            </h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-4 gap-y-8">
                @foreach($topMeme as $Gif)
                    <div>
                        @include('gifmeme::theme.partials.card', ['Gif' => $Gif])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($latest->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-clock text-blue-500"></i> {{ __('Gifindex.sort_newest') }}
            </h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-4 gap-y-8">
                @foreach($latest as $Gif)
                    <div>
                        @include('gifmeme::theme.partials.card', ['Gif' => $Gif])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <h2 class="text-xl font-black text-slate-900 dark:text-white mb-4 pt-2 border-t border-slate-200 dark:border-slate-700">{{ __('Gifindex.browse_all') }}</h2>
    @endif

    <form method="GET" id="Gif-filter-form" class="mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Gifindex.search_placeholder') }}"
                   class="flex-1 min-w-[200px] px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30">

            <select name="category" class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white md:w-48">
                <option value="">{{ __('Gifindex.all_categories') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="sort" class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white md:w-48">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>{{ __('Gifindex.sort_newest') }}</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>{{ __('Gifindex.sort_popular') }}</option>
                <option value="downloads" {{ request('sort') === 'downloads' ? 'selected' : '' }}>{{ __('Gifindex.sort_downloads') }}</option>
            </select>

            <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                <i class="fa-solid fa-magnifying-glass"></i> {{ __('Gifindex.search_button') }}
            </button>
        </div>
    </form>

    @if($Gifs->isEmpty())
        <div class="text-center py-20 text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-images text-5xl mb-4 opacity-30"></i>
            <p>{{ __('Gifindex.empty_state') }}</p>
        </div>
    @else
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-2 gap-y-6">
            @foreach($Gifs as $Gif)
                @include('gifmeme::theme.partials.card', ['Gif' => $Gif])
            @endforeach
        </div>

        <div class="mt-10">
            {{ $Gifs->links() }}
        </div>
    @endif
</div>

<script>
    window.Gif_CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/gif-player.js?v=' . time()) }}"></script>
@endsection



