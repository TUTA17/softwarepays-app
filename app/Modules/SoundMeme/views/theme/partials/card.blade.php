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
    // Sử dụng Hash (md5) của ID để chọn màu ngẫu nhiên nhưng cố định cho từng nút, tránh bị xếp thành cột dọc
    $randomIndex = hexdec(substr(md5($sound->id . 'salt'), -5)) % count($colorSets);
    $c = $colorSets[$randomIndex];
@endphp

<div class="sound-button-wrapper flex flex-col items-center justify-start group text-center"
     data-slug="{{ $sound->slug }}" data-play-url="{{ $sound->play_url }}">

    <div class="mb-7">
        @include('soundmeme::theme.partials.button-face', ['colorSet' => $c, 'size' => 85])
    </div>

    <!-- Title -->
    <a href="{{ route('sounds.show', $sound->slug) }}" class="block font-bold text-slate-800 dark:text-slate-200 text-sm px-1 text-center hover:text-blue-600 dark:hover:text-blue-400 transition-colors w-full break-words line-clamp-2 overflow-hidden leading-snug mb-2 h-9" title="{{ $sound->title }}">
        {{ Str::limit($sound->title, 30) }}
    </a>

    <!-- Action Icons -->
    <div class="flex items-center justify-center gap-4 text-lg">
        <button type="button" onclick="likeSound('{{ $sound->slug }}', this)" class="text-red-500 hover:scale-125 transition-transform" title="{{ __('soundshow.like_button') }}">
            <i class="fa-solid fa-heart"></i>
        </button>
        <button type="button" onclick="copySoundLink('{{ route('sounds.show', $sound->slug) }}', this)" class="text-blue-500 hover:scale-125 transition-transform" title="{{ __('soundshow.share_button') }}">
            <i class="fa-solid fa-share-nodes"></i>
        </button>
        <a href="{{ route('sounds.download', $sound->slug) }}" class="text-indigo-500 hover:scale-125 transition-transform" title="{{ __('soundshow.download_button') }}">
            <i class="fa-solid fa-download"></i>
        </a>
    </div>
</div>
