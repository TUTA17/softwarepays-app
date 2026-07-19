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

@once
<style>
.uiverse-btn-new {
  -webkit-appearance: none;
  appearance: none;
  position: relative;
  border-width: 0;
  padding: 0 8px 12px;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  background: transparent;
  font: inherit;
  cursor: pointer;
}

.uiverse-btn-new .button-top {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 0;
  width: 100%;
  height: 100%;
  transform: translateY(0);
  text-align: center;
  color: #fff;
  text-shadow: 0 -1px rgba(0, 0, 0, .25);
  transition-property: transform;
  transition-duration: .2s;
  -webkit-user-select: none;
  user-select: none;
}

.uiverse-btn-new:active .button-top, .sound-button-wrapper.playing .uiverse-btn-new .button-top {
  transform: translateY(6px);
}

.uiverse-btn-new .button-top::after {
  content: '';
  position: absolute;
  z-index: -1;
  border-radius: 12px;
  width: 100%;
  height: 100%;
  box-sizing: content-box;
  background-image: radial-gradient(var(--c-light), var(--c-dark));
  text-align: center;
  color: #fff;
  box-shadow: inset 0 0 0px 1px rgba(255, 255, 255, .2), 0 1px 2px 1px rgba(255, 255, 255, .2);
  transition-property: border-radius, padding, width, transform;
  transition-duration: .2s;
}

.uiverse-btn-new:active .button-top::after, .sound-button-wrapper.playing .uiverse-btn-new .button-top::after {
  border-radius: 14px;
  padding: 0 2px;
}

.uiverse-btn-new .button-bottom {
  position: absolute;
  z-index: -1;
  bottom: 4px;
  left: 4px;
  border-radius: 16px / 24px 24px 16px 16px;
  padding-top: 6px;
  width: calc(100% - 8px);
  height: calc(100% - 10px);
  box-sizing: content-box;
  background-color: var(--c-base);
  background-image: radial-gradient(4px 8px at 4px calc(100% - 8px), rgba(255, 255, 255, .25), transparent), radial-gradient(4px 8px at calc(100% - 4px) calc(100% - 8px), rgba(255, 255, 255, .25), transparent), radial-gradient(16px at -4px 0, white, transparent), radial-gradient(16px at calc(100% + 4px) 0, white, transparent);
  box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.5), inset 0 -1px 3px 3px rgba(0, 0, 0, .4);
  transition-property: border-radius, padding-top;
  transition-duration: .2s;
}

.uiverse-btn-new:active .button-bottom, .sound-button-wrapper.playing .uiverse-btn-new .button-bottom {
  border-radius: 18px 18px 16px 16px / 16px;
  padding-top: 0;
}

.uiverse-btn-new .button-base {
  position: absolute;
  z-index: -2;
  top: 4px;
  left: 0;
  border-radius: 20px;
  width: 100%;
  height: calc(100% - 4px);
  background-color: rgba(0, 0, 0, .15);
  box-shadow: 0 1px 1px 0 rgba(255, 255, 255, .75), inset 0 2px 2px rgba(0, 0, 0, .25);
}

/* Giọt nước miếng chảy dần xuống khi đang phát nhạc — dài hết cỡ (100% chiều cao khung chứa)
   đúng vào lúc bài hát kết thúc, đồng bộ theo % thời lượng như thanh tiến trình bình thường. */
.drool-drip {
  width: 6px;
  height: 26px;
  overflow: hidden;
}

.drool-drip .drool-fill {
  width: 100%;
  height: 0%;
  background: linear-gradient(to bottom, rgba(255,255,255,.95), rgba(255,255,255,.55));
  border-radius: 0 0 999px 999px;
  box-shadow: 0 1px 2px rgba(0,0,0,.25);
  transition: height 0.1s linear;
}
</style>
@endonce

    <div class="relative w-[85px] h-[85px] mb-7 mx-auto group cursor-pointer hover:brightness-110 transition-all">
        <!-- Nút bấm Uiverse New -->
        <button type="button" class="uiverse-btn-new play-btn" style="--c-light: {{ $c['light'] }}; --c-dark: {{ $c['dark'] }}; --c-base: {{ $c['base'] }};">
            <div class="button-top">
                <!-- Đôi mắt "- -" (Sleepy eyes) -->
                <div class="absolute top-[30%] left-0 right-0 flex justify-center gap-2.5 opacity-100 pointer-events-none z-20">
                    <div class="w-4 h-1.5 bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.9),inset_0_1px_2px_rgba(0,0,0,0.3)]"></div>
                    <div class="w-4 h-1.5 bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.9),inset_0_1px_2px_rgba(0,0,0,0.3)]"></div>
                </div>

                <!-- Cái miệng (khép, đứng yên) -->
                <div class="absolute bottom-[25%] left-[30%] right-[30%] h-1.5 bg-black/30 rounded-full pointer-events-none z-20 shadow-[inset_0_1px_2px_rgba(0,0,0,0.5),0_1px_1px_rgba(255,255,255,0.2)]"></div>

                <!-- Giọt nước miếng chảy xuống theo tiến độ bài hát, chỉ hiện khi đang phát -->
                <div class="drool-drip absolute left-1/2 -translate-x-1/2 top-[58%] opacity-0 group-[.playing]:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                    <div class="drool-fill"></div>
                </div>
            </div>
            <div class="button-bottom"></div>
            <div class="button-base"></div>
        </button>
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
