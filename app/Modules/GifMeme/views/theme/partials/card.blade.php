    <div class="flex flex-col items-center justify-start group text-center">
    <div class="relative w-full aspect-square mb-3 mx-auto group cursor-pointer hover:scale-105 transition-transform duration-300 rounded-xl overflow-hidden shadow-sm">
        <a href="{{ route('Gifs.show', $Gif->slug) }}">
            <img src="{{ $Gif->play_url }}" alt="{{ $Gif->title }}" class="w-full h-full object-cover" loading="lazy">
        </a>
    </div>

    <!-- Title -->
    <a href="{{ route('Gifs.show', $Gif->slug) }}" class="block font-bold text-slate-800 dark:text-slate-200 text-sm px-1 text-center hover:text-blue-600 dark:hover:text-blue-400 transition-colors w-full break-words line-clamp-2 overflow-hidden leading-snug mb-2 h-9" title="{{ $Gif->title }}">
        {{ Str::limit($Gif->title, 30) }}
    </a>

    <!-- Action Icons -->
    <div class="flex items-center justify-center gap-4 text-lg">
        <button type="button" onclick="likeGif('{{ $Gif->slug }}', this)" class="text-red-500 hover:scale-125 transition-transform" title="{{ __('Gifshow.like_button') }}">
            <i class="fa-solid fa-heart"></i>
        </button>
        <button type="button" onclick="copyGifLink('{{ route('Gifs.show', $Gif->slug) }}', this)" class="text-blue-500 hover:scale-125 transition-transform" title="{{ __('Gifshow.share_button') }}">
            <i class="fa-solid fa-share-nodes"></i>
        </button>
        <a href="{{ route('Gifs.download', $Gif->slug) }}" class="text-indigo-500 hover:scale-125 transition-transform" title="{{ __('Gifshow.download_button') }}">
            <i class="fa-solid fa-download"></i>
        </a>
    </div>
</div>



