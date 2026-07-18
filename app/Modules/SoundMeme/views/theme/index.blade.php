@extends('theme::layouts.app')

@section('title', 'Sound Meme - SoftwarePays')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-display font-black text-slate-900 dark:text-white mb-2">
            <i class="fa-solid fa-music text-blue-600 dark:text-blue-400"></i> Sound Meme
        </h1>
        <p class="text-slate-500 dark:text-slate-400">{{ __('soundindex.tagline') }}</p>
    </div>

    @if($isBrowsingDefault)
        @if($editorsPicks->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-500"></i> {{ __('soundindex.editors_picks') }}
            </h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-2 gap-y-6">
                @foreach($editorsPicks as $sound)
                    @include('soundmeme::theme.partials.card', ['sound' => $sound])
                @endforeach
            </div>
        </div>
        @endif

        @if($topMeme->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-fire text-red-500"></i> Top Meme
            </h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-2 gap-y-6">
                @foreach($topMeme as $sound)
                    @include('soundmeme::theme.partials.card', ['sound' => $sound])
                @endforeach
            </div>
        </div>
        @endif

        @if($latest->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock text-blue-500"></i> {{ __('soundindex.sort_newest') }}
            </h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-2 gap-y-6">
                @foreach($latest as $sound)
                    @include('soundmeme::theme.partials.card', ['sound' => $sound])
                @endforeach
            </div>
        </div>
        @endif

        <h2 class="text-xl font-black text-slate-900 dark:text-white mb-4 pt-2 border-t border-slate-200 dark:border-slate-700">{{ __('soundindex.browse_all') }}</h2>
    @endif

    <form method="GET" id="sound-filter-form" class="mb-6">
        <div class="flex flex-wrap gap-3 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('soundindex.search_placeholder') }}"
                   class="flex-1 min-w-[200px] px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30">

            <select name="sort" class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>{{ __('soundindex.sort_newest') }}</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>{{ __('soundindex.sort_popular') }}</option>
                <option value="downloads" {{ request('sort') === 'downloads' ? 'selected' : '' }}>{{ __('soundindex.sort_downloads') }}</option>
            </select>

            <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                <i class="fa-solid fa-magnifying-glass"></i> {{ __('soundindex.search_button') }}
            </button>
        </div>

        <input type="hidden" name="category" id="category-input" value="{{ request('category') }}">
        <div class="flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: thin;">
            <button type="button" data-value=""
                    class="category-chip shrink-0 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-colors {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                {{ __('soundindex.all_categories') }}
            </button>
            @foreach($categories as $cat)
                <button type="button" data-value="{{ $cat->slug }}"
                        class="category-chip shrink-0 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-colors {{ request('category') === $cat->slug ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </form>

    @if($sounds->isEmpty())
        <div class="text-center py-20 text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-music text-5xl mb-4 opacity-30"></i>
            <p>{{ __('soundindex.empty_state') }}</p>
        </div>
    @else
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-x-2 gap-y-6">
            @foreach($sounds as $sound)
                @include('soundmeme::theme.partials.card', ['sound' => $sound])
            @endforeach
        </div>

        <div class="mt-10">
            {{ $sounds->links() }}
        </div>
    @endif
</div>

<audio id="global-player" class="hidden"></audio>

<script>
    window.SOUND_CSRF_TOKEN = '{{ csrf_token() }}';

    document.querySelectorAll('.category-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.getElementById('category-input').value = chip.dataset.value;
            document.getElementById('sound-filter-form').submit();
        });
    });
</script>
<script src="{{ asset('js/sound-player.js?v=' . time()) }}"></script>
@endsection
