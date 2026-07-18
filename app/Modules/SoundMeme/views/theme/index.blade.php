@extends('theme::layouts.app')

@section('title', 'Sound Meme - SoftwarePays')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-display font-black text-slate-900 dark:text-white mb-2">
            <i class="fa-solid fa-music text-blue-600 dark:text-blue-400"></i> Sound Meme
        </h1>
        <p class="text-slate-500 dark:text-slate-400">Kho âm thanh chế miễn phí — nghe, tải, chia sẻ thoải mái.</p>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-8">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm sound theo tên hoặc tag..."
               class="flex-1 min-w-[200px] px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30">

        <select name="category" class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="sort" class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
            <option value="downloads" {{ request('sort') === 'downloads' ? 'selected' : '' }}>Tải nhiều nhất</option>
        </select>

        <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
            <i class="fa-solid fa-magnifying-glass"></i> Tìm
        </button>
    </form>

    @if($sounds->isEmpty())
        <div class="text-center py-20 text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-music text-5xl mb-4 opacity-30"></i>
            <p>Chưa có sound nào phù hợp.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
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
</script>
<script src="{{ asset('js/sound-player.js') }}"></script>
@endsection
