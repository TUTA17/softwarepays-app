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

<div class="sound-button-wrapper flex flex-col items-center justify-start group text-center"
     data-slug="{{ $sound->slug }}" data-play-url="{{ $sound->play_url }}">

    <style>
        .myinstants-btn-small {
            background-image: url('https://www.myinstants.com/media/images/transparent_button_sprite.png') !important;
            background-size: 200% 100% !important;
            background-position: 0% 0% !important;
            background-repeat: no-repeat !important;
            transition: none !important;
        }
        .myinstants-btn-small:active,
        .sound-button-wrapper.playing .myinstants-btn-small {
            background-position: 100% 0% !important;
        }
    </style>
    <div class="relative w-[85px] h-[85px] mb-3 flex items-center justify-center transition-transform hover:scale-105">
        <!-- Colored Base -->
        <div class="absolute rounded-full {{ $c }} z-0" style="width: 86%; height: 86%;"></div>
        
        <!-- Transparent Sprite Button -->
        <button type="button" class="play-btn myinstants-btn-small absolute inset-0 z-10 focus:outline-none cursor-pointer">
            <!-- Progress Overlay -->
            <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none opacity-0 group-[.playing]:opacity-100" viewBox="0 0 100 100">
                <circle class="progress-ring text-black/20" stroke-width="6" stroke="currentColor" fill="transparent" r="42" cx="50" cy="50" style="stroke-dasharray: 264; stroke-dashoffset: 264; transition: stroke-dashoffset 0.1s linear;"></circle>
            </svg>
        </button>
    </div>

    <!-- Title -->
    <a href="{{ route('sounds.show', $sound->slug) }}" class="block font-bold text-slate-800 dark:text-slate-200 text-[11px] px-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors w-full break-words line-clamp-2 leading-snug mb-1.5 h-7 flex items-center justify-center" title="{{ $sound->title }}">
        {{ Str::limit($sound->title, 40) }}
    </a>

    <!-- Action Icons -->
    <div class="flex items-center justify-center gap-2.5 text-slate-400 dark:text-slate-500 text-[11px]">
        <button type="button" onclick="likeSound('{{ $sound->slug }}', this)" class="hover:text-red-500 transition-colors" title="Thích">
            <i class="fa-solid fa-heart"></i>
        </button>
        <button type="button" onclick="copySoundLink('{{ route('sounds.show', $sound->slug) }}', this)" class="hover:text-blue-500 transition-colors" title="Chia sẻ">
            <i class="fa-solid fa-share-nodes"></i>
        </button>
        <a href="{{ route('sounds.download', $sound->slug) }}" class="hover:text-indigo-500 transition-colors" title="Tải xuống">
            <i class="fa-solid fa-download"></i>
        </a>
    </div>
</div>
