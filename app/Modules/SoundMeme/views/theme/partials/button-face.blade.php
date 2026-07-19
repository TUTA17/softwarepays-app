@php
    $size = $size ?? 85;
@endphp

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

/* Nước miếng chảy dọc từ trên xuống ngay trong khung miệng khi đang phát nhạc — đầy hết
   (100% chiều cao khung miệng) đúng vào lúc bài hát kết thúc, đồng bộ theo % thời lượng. */
.drool-fill {
  height: 0%;
  transition: height 0.1s linear, opacity 0.2s;
}
</style>
@endonce

<div class="relative mx-auto" style="width: {{ $size }}px; height: {{ $size }}px;">
    <button type="button" class="uiverse-btn-new play-btn" style="--c-light: {{ $colorSet['light'] }}; --c-dark: {{ $colorSet['dark'] }}; --c-base: {{ $colorSet['base'] }};">
        <div class="button-top">
            <!-- Đôi mắt "- -" (Sleepy eyes) -->
            <div class="absolute top-[30%] left-0 right-0 flex justify-center gap-2.5 opacity-100 pointer-events-none z-20" style="gap: {{ round($size * 0.03, 1) }}px;">
                <div class="bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.9),inset_0_1px_2px_rgba(0,0,0,0.3)]" style="width: {{ round($size * 0.19, 1) }}px; height: {{ round($size * 0.07, 1) }}px;"></div>
                <div class="bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.9),inset_0_1px_2px_rgba(0,0,0,0.3)]" style="width: {{ round($size * 0.19, 1) }}px; height: {{ round($size * 0.07, 1) }}px;"></div>
            </div>

            <!-- Cái miệng — nước miếng chảy dọc từ trên xuống ngay trong khung miệng, đầy hết
                 (100%) đúng lúc bài hát kết thúc, chỉ hiện khi đang phát -->
            <div class="absolute bottom-[25%] left-[30%] right-[30%] bg-black/30 rounded-full overflow-hidden pointer-events-none z-20 shadow-[inset_0_1px_2px_rgba(0,0,0,0.5),0_1px_1px_rgba(255,255,255,0.2)]" style="height: {{ round($size * 0.07, 1) }}px;">
                <div class="drool-fill w-full bg-gradient-to-b from-white/95 to-white/60 opacity-0 group-[.playing]:opacity-100 group-[.drooled]:opacity-100"></div>
            </div>
        </div>
        <div class="button-bottom"></div>
        <div class="button-base"></div>
    </button>
</div>
