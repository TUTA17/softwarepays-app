<div class="sound-card group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-200"
     data-slug="{{ $sound->slug }}" data-play-url="{{ $sound->play_url }}" data-duration="{{ $sound->duration }}">

    <div class="relative aspect-[16/9] bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden">
        @if($sound->thumbnail_url)
            <img src="{{ $sound->thumbnail_url }}" alt="{{ $sound->title }}" class="w-full h-full object-cover">
        @else
            <i class="fa-solid fa-music text-4xl text-slate-300 dark:text-slate-600"></i>
        @endif

        <button type="button" class="play-btn absolute inset-0 flex items-center justify-center bg-black/0 hover:bg-black/20 transition-colors">
            <span class="w-14 h-14 rounded-full bg-white/90 dark:bg-slate-900/90 flex items-center justify-center shadow-lg">
                <i class="play-icon fa-solid fa-play text-blue-600 dark:text-blue-400 text-lg"></i>
            </span>
        </button>

        @if($sound->category)
            <span class="absolute top-2 left-2 px-2.5 py-1 rounded-full bg-blue-600 text-white text-xs font-bold">{{ $sound->category->name }}</span>
        @endif
    </div>

    <div class="p-4">
        <a href="{{ route('sounds.show', $sound->slug) }}" class="block font-bold text-slate-900 dark:text-white line-clamp-1 mb-1 hover:text-blue-600 dark:hover:text-blue-400">
            {{ $sound->title }}
        </a>

        @if($sound->tags)
            <div class="flex flex-wrap gap-1 mb-2">
                @foreach(array_slice($sound->tags_array, 0, 3) as $tag)
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">#{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        <div class="progress-wrap h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 cursor-pointer mb-2">
            <div class="progress-fill h-full rounded-full bg-blue-600" style="width:0%"></div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-3">
            <span><span class="time-current">0:00</span> / {{ $sound->duration ? gmdate('i:s', $sound->duration) : '--:--' }}</span>
            <span><i class="fa-solid fa-headphones"></i> {{ number_format($sound->play_count) }} &nbsp; <i class="fa-solid fa-download"></i> {{ number_format($sound->download_count) }}</span>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('sounds.download', $sound->slug) }}" class="flex-1 text-center px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                <i class="fa-solid fa-download"></i> Tải xuống
            </a>
            <button type="button" onclick="copySoundLink('{{ route('sounds.show', $sound->slug) }}', this)"
                    class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm transition-colors">
                <i class="fa-solid fa-link"></i>
            </button>
        </div>
    </div>
</div>
