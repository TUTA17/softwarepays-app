@extends('theme::layouts.app')

@section('title', $sound->title . ' - Sound Meme - SoftwarePays')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="text-sm text-slate-500 dark:text-slate-400 mb-6">
        <a href="{{ route('sounds.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Sound Meme</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ $sound->title }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="sound-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-6"
                 data-slug="{{ $sound->slug }}" data-play-url="{{ $sound->play_url }}" data-duration="{{ $sound->duration }}">

                <div class="relative aspect-[16/9] bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                    @if($sound->thumbnail_url)
                        <img src="{{ $sound->thumbnail_url }}" alt="{{ $sound->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-music text-6xl text-slate-300 dark:text-slate-600"></i>
                    @endif

                    <button type="button" class="play-btn absolute inset-0 flex items-center justify-center bg-black/0 hover:bg-black/20 transition-colors">
                        <span class="w-20 h-20 rounded-full bg-white/90 dark:bg-slate-900/90 flex items-center justify-center shadow-lg">
                            <i class="play-icon fa-solid fa-play text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </span>
                    </button>
                </div>

                <div class="p-5">
                    <h1 class="text-2xl font-display font-black text-slate-900 dark:text-white mb-3">{{ $sound->title }}</h1>

                    <div class="progress-wrap h-2 rounded-full bg-slate-200 dark:bg-slate-700 cursor-pointer mb-2">
                        <div class="progress-fill h-full rounded-full bg-blue-600" style="width:0%"></div>
                    </div>
                    <div class="flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 mb-4">
                        <span><span class="time-current">0:00</span> / {{ $sound->duration ? gmdate('i:s', $sound->duration) : '--:--' }}</span>
                        <span><i class="fa-solid fa-headphones"></i> {{ number_format($sound->play_count) }} lượt nghe &nbsp; <i class="fa-solid fa-download"></i> {{ number_format($sound->download_count) }} lượt tải</span>
                    </div>

                    @if($sound->tags)
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach($sound->tags_array as $tag)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($sound->description)
                        <p class="text-slate-600 dark:text-slate-300 mb-5 whitespace-pre-line">{{ $sound->description }}</p>
                    @endif

                    <div class="flex gap-3">
                        <a href="{{ route('sounds.download', $sound->slug) }}" class="flex-1 text-center px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                            <i class="fa-solid fa-download"></i> Tải xuống
                        </a>
                        <button type="button" onclick="copySoundLink('{{ route('sounds.show', $sound->slug) }}', this)"
                                class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition-colors">
                            <i class="fa-solid fa-link"></i> Sao chép liên kết
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-4">Sound liên quan</h3>
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
                    <p class="text-sm text-slate-500 dark:text-slate-400">Chưa có sound liên quan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<audio id="global-player" class="hidden"></audio>

<script>
    window.SOUND_CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/sound-player.js') }}"></script>
@endsection
