@extends('theme::layouts.app')

@section('title', __('smmclient.page_title'))

@push('styles')
<style>
    /* Custom Radio Buttons */
    .custom-radio {
        appearance: none;
        background-color: #fff;
        margin: 0;
        font: inherit;
        color: currentColor;
        width: 1.15em;
        height: 1.15em;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        display: grid;
        place-content: center;
        transition: all 0.2s ease-in-out;
    }
    .custom-radio::before {
        content: "";
        width: 0.6em;
        height: 0.6em;
        border-radius: 50%;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em var(--radio-color, #475569);
    }
    .custom-radio:checked {
        border-color: var(--radio-color, #475569);
    }
    .custom-radio:checked::before {
        transform: scale(1);
    }
    
    /* Server option selected state */
    .server-option {
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .server-option.selected {
        background-color: #fff7ed; /* orange-50 */
        border-color: #ffedd5; /* orange-100 */
    }
    .dark .server-option.selected {
        background-color: rgba(234, 88, 12, 0.1);
        border-color: rgba(234, 88, 12, 0.2);
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="relative overflow-hidden pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-4">
            {!! __('smmclient.hero_title') !!}
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-lg max-w-2xl mx-auto mb-8">{{ __('smmclient.hero_subtitle') }}</p>
    </div>
</div>

<div class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    
    <!-- Platform Tabs -->
    <div class="flex flex-wrap justify-center gap-2 sm:gap-3 pb-4 mb-8" id="platform_tabs">
        @foreach($categories as $category => $services)
            @php
                $faType = 'fa-brands';
                $icon = 'fa-globe';
                $colorClass = 'text-slate-500';
                $bgHover = 'hover:bg-slate-100 dark:hover:bg-slate-800';
                $customSvg = null;
                $gradientClass = 'from-slate-600 to-slate-800';
                
                $catLower = strtolower($category);
                if (str_contains($catLower, 'facebook')) { $icon = 'fa-facebook-f'; $colorClass = 'text-blue-600'; $bgHover = 'hover:bg-blue-50 dark:hover:bg-blue-900/30'; $gradientClass = 'from-blue-600 to-indigo-800'; }
                elseif (str_contains($catLower, 'tiktok')) { $icon = 'fa-tiktok'; $colorClass = 'text-slate-900 dark:text-white'; $bgHover = 'hover:bg-slate-100 dark:hover:bg-slate-800'; $gradientClass = 'from-slate-900 via-slate-800 to-slate-900'; }
                elseif (str_contains($catLower, 'instagram')) { $icon = 'fa-instagram'; $colorClass = 'text-fuchsia-500'; $bgHover = 'hover:bg-fuchsia-50 dark:hover:bg-fuchsia-900/30'; $gradientClass = 'from-purple-600 via-pink-500 to-orange-500'; }
                elseif (str_contains($catLower, 'youtube')) { $icon = 'fa-youtube'; $colorClass = 'text-red-600'; $bgHover = 'hover:bg-red-50 dark:hover:bg-red-900/30'; $gradientClass = 'from-red-600 to-red-800'; }
                elseif (str_contains($catLower, 'twitter') || str_contains($catLower, 'x.com') || str_contains($catLower, 'x - twitter')) { 
                    $customSvg = '<svg viewBox="0 0 1200 1227" class="w-4 h-4 fill-current text-slate-900 dark:text-white" xmlns="http://www.w3.org/2000/svg"><path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/></svg>'; 
                    $gradientClass = 'from-slate-800 to-black';
                }
                elseif (str_contains($catLower, 'shopee')) { 
                    $customSvg = '<svg viewBox="0 0 24 24" class="w-5 h-5 rounded-[4px]" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#EE4D2D"/><path d="M8 9.2V7.5a4 4 0 1 1 8 0v1.7" fill="none" stroke="#fff" stroke-width="1.3"/><rect x="6" y="9" width="12" height="9.2" rx="2" fill="#fff"/><text x="12" y="16.2" text-anchor="middle" font-family="Arial, sans-serif" font-weight="800" font-size="8" fill="#EE4D2D">S</text></svg>';
                    $gradientClass = 'from-orange-500 to-red-600';
                }
                elseif (str_contains($catLower, 'lazada')) {
                    $customSvg = '<svg viewBox="0 0 24 24" class="w-5 h-5 rounded-[4px]" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#0F146D"/><path d="M8 9.2V7.5a4 4 0 1 1 8 0v1.7" fill="none" stroke="#fff" stroke-width="1.3"/><rect x="6" y="9" width="12" height="9.2" rx="2" fill="#fff"/><text x="12" y="16.2" text-anchor="middle" font-family="Arial, sans-serif" font-weight="800" font-size="8" fill="#0F146D">L</text></svg>';
                    $gradientClass = 'from-blue-800 to-indigo-900';
                }
                elseif (str_contains($catLower, 'threads')) {
                    $customSvg = '<svg viewBox="0 0 24 24" class="w-5 h-5 rounded-[4px]" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#000"/><text x="12" y="16.6" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="13" fill="#fff">@</text></svg>';
                    $gradientClass = 'from-slate-800 to-black';
                }
                elseif (str_contains($catLower, 'linkedin')) {
                    $customSvg = '<svg viewBox="0 0 24 24" class="w-5 h-5 rounded-[4px]" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#0A66C2"/><text x="12" y="16.6" text-anchor="middle" font-family="Arial, sans-serif" font-weight="800" font-size="11" fill="#fff">in</text></svg>';
                    $gradientClass = 'from-blue-600 to-blue-800';
                }
                elseif (str_contains($catLower, 'google')) {
                    $customSvg = '<svg viewBox="0 0 24 24" class="w-5 h-5 rounded-[4px] bg-white border" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#fff"/><circle cx="12" cy="12" r="6" fill="none" stroke="#EA4335" stroke-width="3" stroke-dasharray="9.4 28.3" stroke-dashoffset="0"/><circle cx="12" cy="12" r="6" fill="none" stroke="#4285F4" stroke-width="3" stroke-dasharray="9.4 28.3" stroke-dashoffset="-9.4"/><circle cx="12" cy="12" r="6" fill="none" stroke="#34A853" stroke-width="3" stroke-dasharray="9.4 28.3" stroke-dashoffset="-18.8"/><circle cx="12" cy="12" r="6" fill="none" stroke="#FBBC05" stroke-width="3" stroke-dasharray="9.4 28.3" stroke-dashoffset="-28.2"/><rect x="12" y="10.5" width="6" height="3" fill="#4285F4"/></svg>';
                    $gradientClass = 'from-slate-100 to-slate-200';
                }
                elseif (str_contains($catLower, 'telegram')) { $icon = 'fa-telegram'; $colorClass = 'text-blue-400'; $gradientClass = 'from-blue-400 to-blue-600'; }
                else { $faType = 'fa-solid'; } 
            @endphp
            <button type="button" class="platform-btn shrink-0 px-4 py-2 sm:px-5 sm:py-2.5 rounded-full bg-white dark:bg-slate-800 border flex items-center gap-2 transition-all duration-300 border-slate-200/80 text-slate-700 font-semibold hover:border-slate-300 dark:border-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700" data-cat-id="{{ md5($category) }}" data-name="{{ explode(' ', $category)[0] }}" data-icon="{{ $icon }}" data-fatype="{{ $faType }}" data-svg="{{ $customSvg ? 'true' : 'false' }}" data-gradient="{{ $gradientClass }}">
                @if($customSvg)
                    {!! $customSvg !!}
                @else
                    <i class="{{ $faType }} {{ $icon }} {{ $colorClass }} text-[15px] w-5 text-center"></i>
                @endif
                <span class="text-sm tracking-wide">{{ \Illuminate\Support\Str::limit($categoryLabels[$category] ?? $category, 15) }}</span>
            </button>
        @endforeach
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-14">
        
        <!-- ====== CỘT TRÁI (Ảnh Cover) ====== -->
        <div class="md:col-span-5 lg:col-span-6">
            <div id="coverBlock" class="w-full aspect-[4/3] rounded-[24px] bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-800 dark:to-slate-900 flex flex-col items-center justify-center p-8 text-center shadow-lg relative overflow-hidden group transition-all duration-500">
                <div class="absolute inset-0 bg-black/10"></div>
                
                <!-- Icon in center -->
                <div class="w-20 h-20 md:w-28 md:h-28 rounded-3xl bg-white/10 backdrop-blur-md flex items-center justify-center text-5xl md:text-6xl text-white shadow-2xl mb-6 border border-white/20 relative z-10 transform group-hover:scale-105 transition-transform duration-500" id="coverIconWrapper">
                    <i id="coverIcon" class="fa-solid fa-globe"></i>
                </div>
                
                <!-- Text Below Icon -->
                <div class="flex items-center gap-2 relative z-10">
                    <span class="text-2xl font-black text-white drop-shadow-md">SMM</span>
                    <span id="coverPlatformName" class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-sm font-semibold border border-white/30 drop-shadow-md">{{ __('smmclient.platform_badge') }}</span>
                </div>
            </div>
        </div>

        <!-- ====== CỘT PHẢI (Thông tin & Form) ====== -->
        <div class="md:col-span-7 lg:col-span-6 flex flex-col">
            
            <div class="flex items-start justify-between mb-3">
                <h1 class="text-2xl md:text-[28px] font-bold text-slate-900 dark:text-white leading-tight" id="formTitle">
                    {{ __('Vui lòng chọn nền tảng') }}
                </h1>
            </div>
            
            <div class="mb-8">
                <span class="inline-block px-3 py-1 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[11px] font-bold rounded-md">
                    {{ __('Dịch vụ MXH') }}
                </span>
            </div>

            <form action="{{ route('smm.order') }}" method="POST" id="smmForm" class="space-y-6 flex-grow">
                @csrf
                <select id="category_select" class="hidden">
                    <option value="">{{ __('-- Chọn nền tảng --') }}</option>
                    @foreach($categories as $category => $services)
                        <option value="{{ md5($category) }}">{{ $categoryLabels[$category] ?? $category }}</option>
                    @endforeach
                </select>

                <!-- Chọn Loại Dịch Vụ -->
                <div class="relative">
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">{{ __('Loại Dịch Vụ') }}</label>
                    <select id="service_group" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 transition-colors shadow-sm text-sm appearance-none font-semibold">
                        <option value="">{{ __('-- Vui lòng chọn Nền tảng ở trên --') }}</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 top-7 flex items-center px-4 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Chọn Máy Chủ -->
                <div>
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">{{ __('Chọn máy chủ') }}</label>
                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden bg-slate-50/50 dark:bg-slate-800/30" id="serverGrid">
                        <div class="p-4 text-sm text-slate-500 text-center font-medium">{{ __('Vui lòng chọn loại dịch vụ') }}</div>
                    </div>
                    <input type="hidden" name="service_id" id="service_id" required>
                </div>

                <!-- Nhập Link -->
                <div>
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">{{ __('Nhập link cần tăng') }}</label>
                    <div class="flex gap-2">
                        <input type="url" name="link" required placeholder="https://..." class="flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 transition-colors shadow-sm text-sm">
                        <button type="button" onclick="navigator.clipboard.readText().then(t => this.previousElementSibling.value = t)" class="px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm whitespace-nowrap">
                            <i class="fa-regular fa-clipboard text-slate-400"></i> {{ __('Dán') }}
                        </button>
                    </div>
                </div>

                <!-- Số lượng -->
                <div>
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">{{ __('Số lượng') }}</label>
                    <input type="number" name="quantity" id="qtyInput" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 font-medium text-base shadow-sm">
                    
                    <div class="mt-4 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-4 text-center">
                        <span id="rangeHint" class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('Vui lòng chọn máy chủ để xem giới hạn.') }}</span>
                    </div>
                </div>

                <!-- Tổng tiền & Submit -->
                <div class="pt-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-[22px] font-bold text-orange-600 dark:text-orange-500">{{ __('Tạm tính:') }}</span>
                        <span id="totalPrice" class="text-[22px] font-bold text-orange-600 dark:text-orange-500">0</span>
                    </div>
                    
                    @auth
                        <button type="submit" class="w-auto min-w-[200px] px-8 bg-[#d97706] hover:bg-[#b45309] text-white py-3 rounded-lg text-sm font-bold transition-colors shadow-md">
                            {{ __('Thêm vào giỏ hàng') }}
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex w-auto min-w-[200px] px-8 bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-lg text-sm font-bold transition-colors shadow-md justify-center items-center">
                            {{ __('Đăng nhập để đặt hàng') }}
                        </a>
                    @endauth
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.APP_CONFIG = {
    currency: '{{ session('currency', 'VND') }}',
    rates: {
        'VND': 1,
        'USD': 25000,
        'CNY': 3500,
        'JPY': 160,
        'KRW': 18,
        'THB': 680,
        'RUB': 270
    }
};

function formatCurrency(amountVnd) {
    const rate = window.APP_CONFIG.rates[window.APP_CONFIG.currency] || 1;
    const amount = amountVnd / rate;
    
    return new Intl.NumberFormat('{{ app()->getLocale() }}', {
        style: 'currency',
        currency: window.APP_CONFIG.currency,
        minimumFractionDigits: (window.APP_CONFIG.currency === 'VND' || window.APP_CONFIG.currency === 'JPY' || window.APP_CONFIG.currency === 'KRW') ? 0 : 2
    }).format(amount);
}

document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_select');
    const serviceGroupSelect = document.getElementById('service_group');
    const serverGrid = document.getElementById('serverGrid');
    const hiddenServiceId = document.getElementById('service_id');
    const qtyInput = document.getElementById('qtyInput');
    const hint = document.getElementById('rangeHint');
    const priceText = document.getElementById('totalPrice');
    const formTitle = document.getElementById('formTitle');
    const coverBlock = document.getElementById('coverBlock');
    const coverIconWrapper = document.getElementById('coverIconWrapper');
    const coverPlatformName = document.getElementById('coverPlatformName');
    
    let currentServer = null;
    let currentPlatform = '';

    // Handle Custom Platform Tabs Click
    const platformBtns = document.querySelectorAll('.platform-btn');
    
    platformBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active styles
            platformBtns.forEach(b => {
                b.classList.remove('border-blue-500', 'text-blue-600', 'font-bold', 'shadow-sm', 'dark:border-blue-400', 'dark:text-blue-400');
                b.classList.add('border-slate-200/80', 'text-slate-700', 'font-semibold', 'dark:border-slate-700', 'dark:text-slate-200');
            });
            this.classList.remove('border-slate-200/80', 'text-slate-700', 'font-semibold', 'dark:border-slate-700', 'dark:text-slate-200');
            this.classList.add('border-blue-500', 'text-blue-600', 'font-bold', 'shadow-sm', 'dark:border-blue-400', 'dark:text-blue-400');
            
            // Update UI
            currentPlatform = this.getAttribute('data-name');
            coverPlatformName.textContent = currentPlatform;
            
            // Handle Icon
            if(this.getAttribute('data-svg') === 'true') {
                const svgClone = this.querySelector('svg').cloneNode(true);
                svgClone.classList.remove('w-4', 'h-4', 'w-5', 'h-5', 'text-slate-900', 'rounded-[4px]');
                svgClone.classList.add('w-12', 'h-12', 'text-white', 'md:w-16', 'md:h-16', 'rounded-xl');
                coverIconWrapper.innerHTML = '';
                coverIconWrapper.appendChild(svgClone);
            } else if(currentPlatform.toLowerCase() === 'instagram') {
                coverIconWrapper.innerHTML = '<img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Instagram_logo_2022.svg" class="w-12 h-12 md:w-16 md:h-16 drop-shadow-md" alt="IG">';
            } else {
                coverIconWrapper.innerHTML = `<i class="${this.getAttribute('data-fatype')} ${this.getAttribute('data-icon')}"></i>`;
            }
            
            // Update Gradient
            const oldGradient = coverBlock.className.match(/from-[^\s]+/g);
            if(oldGradient) {
                oldGradient.forEach(g => coverBlock.classList.remove(g));
            }
            const viaClasses = coverBlock.className.match(/via-[^\s]+/g);
            if(viaClasses) viaClasses.forEach(g => coverBlock.classList.remove(g));
            const toClasses = coverBlock.className.match(/to-[^\s]+/g);
            if(toClasses) toClasses.forEach(g => coverBlock.classList.remove(g));
            
            const newGradient = this.getAttribute('data-gradient').split(' ');
            newGradient.forEach(g => coverBlock.classList.add(g));
            
            // Update logic
            const catId = this.getAttribute('data-cat-id');
            categorySelect.value = catId;
            categorySelect.dispatchEvent(new Event('change'));
        });
    });

    const servicesData = {!! $jsCategoriesJson !!};

    categorySelect.addEventListener('change', function() {
        const catId = this.value;
        serviceGroupSelect.innerHTML = '<option value="">{{ __('-- Chọn Loại Dịch Vụ --') }}</option>';
        serverGrid.innerHTML = '<div class="p-4 text-sm text-slate-500 text-center font-medium">{{ __('Vui lòng chọn loại dịch vụ') }}</div>';
        hiddenServiceId.value = '';
        currentServer = null;
        updatePrice();
        
        if (servicesData[catId]) {
            for (const groupName of Object.keys(servicesData[catId])) {
                const opt = document.createElement('option');
                opt.value = groupName;
                opt.textContent = groupName;
                serviceGroupSelect.appendChild(opt);
            }
        }
        if (serviceGroupSelect.options.length > 1) {
            serviceGroupSelect.selectedIndex = 1;
            serviceGroupSelect.dispatchEvent(new Event('change'));
        }
    });
    
    serviceGroupSelect.addEventListener('change', function() {
        const catId = categorySelect.value;
        const groupName = this.value;
        serverGrid.innerHTML = '';
        
        if (!groupName || !servicesData[catId][groupName]) {
            serverGrid.innerHTML = '<div class="p-4 text-sm text-slate-500 text-center font-medium">{{ __('Vui lòng chọn loại dịch vụ') }}</div>';
            hiddenServiceId.value = '';
            qtyInput.value = '';
            qtyInput.min = '';
            qtyInput.max = '';
            hint.textContent = '{{ __('Vui lòng chọn máy chủ để xem giới hạn.') }}';
            updatePrice();
            return;
        }
        
        servicesData[catId][groupName].forEach((service, index) => {
            const formattedRate = formatCurrency(service.rate);
            const isFirst = index === 0;
            const encodedService = JSON.stringify(service).replace(/"/g, '&quot;');
            
            const html = `
            <label class="server-option block cursor-pointer border-b border-slate-200 dark:border-slate-700 last:border-0 relative ${isFirst ? 'selected' : ''}">
                <input type="radio" name="server_radio" value="${service.id}" class="peer sr-only" required onchange="selectServer(${encodedService}, this)" ${isFirst ? 'checked' : ''}>
                <div class="p-4 flex items-center gap-3">
                    <div class="custom-radio shrink-0" style="--radio-color: #334155;"></div>
                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-0">
                        <span class="text-sm font-bold text-slate-400 shrink-0">SV${index+1}</span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">${service.name}</span>
                    </div>
                    <div class="shrink-0">
                        <span class="px-3 py-1 bg-[#3b5998] text-white text-xs font-bold rounded-full shadow-sm">${formattedRate}/1000</span>
                    </div>
                </div>
            </label>`;
            serverGrid.insertAdjacentHTML('beforeend', html);
            
            if(isFirst) {
                selectServer(service, null);
            }
        });
    });

    window.selectServer = function(pkg, element) {
        currentServer = pkg;
        hiddenServiceId.value = pkg.id;
        
        document.querySelectorAll('.server-option').forEach(el => el.classList.remove('selected'));
        if(element) {
            element.closest('.server-option').classList.add('selected');
        } else {
            const checked = document.querySelector('input[name="server_radio"]:checked');
            if(checked) checked.closest('.server-option').classList.add('selected');
        }

        qtyInput.min = pkg.min;
        qtyInput.max = pkg.max;
        
        if(!qtyInput.value) qtyInput.value = pkg.min;
        
        const minStr = pkg.min.toLocaleString('{{ app()->getLocale() }}');
        const maxStr = pkg.max.toLocaleString('{{ app()->getLocale() }}');
        
        // This hint is basic, we could use __('Min :min - Max :max') but simple template literal is okay for now.
        hint.innerHTML = `Min ${minStr} — Max ${maxStr}`;
        updatePrice();
    };

    qtyInput.addEventListener('input', updatePrice);
    
    function updatePrice() {
        if(!currentServer) {
            priceText.textContent = formatCurrency(0);
            return;
        }
        let qty = parseInt(qtyInput.value) || 0;
        const price = (qty * currentServer.rate) / 1000;
        priceText.textContent = formatCurrency(price);
    }
    
    // Auto click tab based on URL parameter or default to first tab
    const urlParams = new URLSearchParams(window.location.search);
    const platformQuery = urlParams.get('platform');
    let btnToClick = null;
    
    if (platformQuery) {
        const queryLower = platformQuery.toLowerCase();
        btnToClick = Array.from(platformBtns).find(b => b.getAttribute('data-name').toLowerCase() === queryLower);
    }
    
    if (btnToClick) {
        btnToClick.click();
        setTimeout(() => btnToClick.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' }), 100);
    } else if(platformBtns.length > 0) {
        platformBtns[0].click();
    }
});
</script>
@endsection
