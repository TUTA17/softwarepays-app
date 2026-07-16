@extends('theme::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('productsList.title') }}</h1>
            <p class="text-slate-500">{{ __('productsList.subtitle') }}</p>
        </div>
        
        <!-- Mobile Filter Toggle -->
        <button class="md:hidden flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-700 dark:text-slate-300 font-medium" onclick="document.getElementById('mobile-filter').classList.toggle('hidden')">
            <i class="fa-solid fa-filter"></i> {{ __('productsList.filter_button') }}
        </button>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filter (Desktop) -->
        <div id="mobile-filter" class="hidden md:block w-full md:w-64 shrink-0">
            <form action="{{ route('shop') }}" method="GET" class="glass-card rounded-lg p-6 sticky top-24">
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('productsList.search_label') }}</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('productsList.search_placeholder') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg pl-10 pr-4 py-2 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">{{ __('productsList.price_range_heading') }}</label>
                    
                    <div class="relative w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mb-6 mt-2">
                        <div id="price-slider-progress" class="absolute h-full bg-blue-500 rounded-full" style="left: 0%; right: 0%;"></div>
                        <input type="range" id="range-min" min="0" max="3000000" step="10000" value="{{ request('min_price', 0) }}" class="absolute w-full -top-2 appearance-none bg-transparent pointer-events-none focus:outline-none" style="pointer-events: none; z-index: 2;" oninput="updatePriceSlider(event)">
                        <input type="range" id="range-max" min="0" max="3000000" step="10000" value="{{ request('max_price', 3000000) }}" class="absolute w-full -top-2 appearance-none bg-transparent pointer-events-none focus:outline-none" style="pointer-events: none; z-index: 2;" oninput="updatePriceSlider(event)">
                    </div>

                    <div class="flex items-center gap-2 mb-4">
                        <input type="number" id="input-min-price" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" oninput="updateRangeFromInput()">
                        <span class="text-slate-400">-</span>
                        <input type="number" id="input-max-price" name="max_price" value="{{ request('max_price') }}" placeholder="3000000" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" oninput="updateRangeFromInput()">
                    </div>
                </div>

                <!-- Discount Filter -->
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-bold cursor-pointer">
                        <input type="checkbox" name="is_discounted" value="1" class="text-blue-600 rounded" {{ request('is_discounted') == '1' ? 'checked' : '' }} onchange="this.form.submit()"> 
                        {{ __('productsList.discount_filter_label') }} <i class="fa-solid fa-tags text-rose-500 ml-1"></i>
                    </label>
                </div>

                <!-- Genre Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('productsList.genre_heading') }}</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @php $currentGenres = request('genres', []); @endphp
                        @foreach($genres as $genre)
                        <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <input type="checkbox" name="genres[]" value="{{ $genre }}" class="text-blue-600 rounded" {{ in_array($genre, $currentGenres) ? 'checked' : '' }} onchange="this.form.submit()"> {{ $genre }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Platform Filter (nền tảng kích hoạt key: Steam, Ubisoft, EA App...) -->
                @if(count($platforms) > 0)
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('productsList.platform_heading') }}</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @php $currentPlatforms = request('platforms', []); @endphp
                        @foreach($platforms as $platform)
                        <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <input type="checkbox" name="platforms[]" value="{{ $platform }}" class="text-blue-600 rounded" {{ in_array($platform, $currentPlatforms) ? 'checked' : '' }} onchange="this.form.submit()"> {{ $platform }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Brand Filter (thẻ nạp thương hiệu: Steam Wallet, PSN, Xbox Live...) -->
                @if(count($brands) > 0)
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('productsList.brand_heading') }}</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @php $currentBrands = request('brands', []); @endphp
                        @foreach($brands as $brand)
                        <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <input type="checkbox" name="brands[]" value="{{ $brand }}" class="text-blue-600 rounded" {{ in_array($brand, $currentBrands) ? 'checked' : '' }} onchange="this.form.submit()"> {{ $brand }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 transition-colors text-white rounded-lg font-bold shadow-lg shadow-blue-500/30 mb-3">{{ __('productsList.filter_submit') }}</button>

                @if(request()->hasAny(['q', 'min_price', 'max_price', 'is_discounted', 'genres', 'platforms', 'brands']))
                    <a href="{{ route('shop') }}" class="block text-center w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 rounded-lg font-bold text-sm border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid fa-eraser"></i> {{ __('productsList.clear_filters') }}
                    </a>
                @endif
            </form>
        </div>

        <!-- Main Product Grid -->
        <div class="flex-1">
            
            <!-- Sort Bar -->
            <div class="flex justify-between items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4 mb-6">
                <div class="text-slate-500 text-sm">
                    {{ __('productsList.showing_label') }} <b>{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</b> {{ __('productsList.showing_of_total') }} <b>{{ $products->total() }}</b> {{ __('productsList.showing_games_suffix') }}
                </div>
                <div>
                    <select onchange="window.location.href=this.value" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 pl-3 pr-8">
                        @php 
                            $params = request()->except('sort', 'page');
                        @endphp
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('productsList.sort_newest') }}</option>
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('productsList.sort_price_asc') }}</option>
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('productsList.sort_price_desc') }}</option>
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'discount_desc'])) }}" {{ request('sort') == 'discount_desc' ? 'selected' : '' }}>{{ __('productsList.sort_discount') }}</option>
                    </select>
                </div>
            </div>

            <!-- Grid -->
            @if($products->isEmpty())
                <div class="glass-card rounded-lg p-16 text-center text-slate-500">
                    <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
                    <h3 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('productsList.no_products') }}</h3>
                    <p>{{ __('productsList.no_products_hint') }}</p>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                    @foreach($products as $product)
                        <a href="{{ route('product.show', ['id' => $product->id, 'slug' => \Illuminate\Support\Str::slug($product->name) ?: 'game']) }}" class="product-card group">
                            <div class="product-card-media bg-slate-100 dark:bg-slate-800 aspect-square relative overflow-hidden">
                                @if($product->original_price && $product->original_price > $product->price)
                                    <span class="discount-badge">
                                        <strong>-{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%</strong>
                                        <small>{{ __('productCard.discount') }}</small>
                                    </span>
                                @endif

                                @if($product->header_image)
                                    <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800">
                                        <span class="text-slate-400"><i class="fa-solid fa-gamepad text-3xl sm:text-5xl"></i></span>
                                    </div>
                                @endif
                                
                                <!-- Quick Actions on Hover -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] z-10 pointer-events-none">
                                    <span class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-xl flex items-center gap-2"><i class="fa-solid fa-eye"></i> {{ __('productCard.view_now') }}</span>
                                </div>
                            </div>
                            
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="mb-2 h-[48px]">
                                    <h3 class="font-display font-semibold text-[15px] text-slate-900 dark:text-white leading-snug group-hover:text-blue-500 transition-colors line-clamp-2" title="{{ $product->name }}">{{ $product->name }}</h3>
                                </div>
                                
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @if($product->categories && $product->categories->count() > 0)
                                        @foreach($product->categories->take(3) as $category)
                                            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded">{{ $category->name }}</span>
                                        @endforeach
                                    @elseif($product->genres)
                                        @php
                                            $displayGenres = is_array($product->genres) ? $product->genres : json_decode($product->genres, true);
                                            if(!is_array($displayGenres) && is_string($product->genres)) $displayGenres = array_map('trim', explode(',', $product->genres));
                                        @endphp
                                        @if(is_array($displayGenres))
                                            @foreach(array_slice($displayGenres, 0, 3) as $genre)
                                                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded">{{ $genre }}</span>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                               
                                @php $listPlatformLabel = $product->platformDisplayLabel(); @endphp
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase"><i class="{{ \App\Modules\Theme\Models\Product::platformIcon($listPlatformLabel) }}"></i> {{ $listPlatformLabel ?: 'GAME' }}</span>
                                    @if($product->available_keys > 0)
                                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-check"></i> {{ __('productCard.in_stock') }}</span>
                                    @else
                                        <span class="text-[10px] text-rose-500 font-bold bg-rose-50 dark:bg-rose-900/20 px-2 py-0.5 rounded"><i class="fa-solid fa-xmark"></i> {{ __('productCard.out_of_stock') }}</span>
                                    @endif
                                </div>
                                
                                <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                                    <div class="flex flex-col">
                                        @if($product->original_price && $product->original_price > $product->price)
                                            <span class="text-[11px] text-slate-400 line-through font-medium">{!! \App\Helpers\CurrencyHelper::formatPrice($product->original_price) !!}</span>
                                        @endif
                                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{!! \App\Helpers\CurrencyHelper::formatPrice($product->price) !!}</span>
                                    </div>
                                    
                                    <button class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all flex items-center justify-center shadow-sm hover:scale-110">
                                        <i class="fa-solid fa-cart-shopping text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Tùy chỉnh thanh cuộn cho danh sách thể loại */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #334155;
}

/* Custom Dual Slider Thumb */
input[type=range]::-webkit-slider-thumb {
    pointer-events: all;
    width: 18px;
    height: 18px;
    -webkit-appearance: none;
    appearance: none;
    background-color: #2563eb;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.3);
}
input[type=range]::-moz-range-thumb {
    pointer-events: all;
    width: 18px;
    height: 18px;
    background-color: #2563eb;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.3);
}
.dark input[type=range]::-webkit-slider-thumb {
    border-color: #1e293b;
}
.dark input[type=range]::-moz-range-thumb {
    border-color: #1e293b;
}
</style>

<script>
    const rangeMin = document.getElementById('range-min');
    const rangeMax = document.getElementById('range-max');
    const inputMin = document.getElementById('input-min-price');
    const inputMax = document.getElementById('input-max-price');
    const progress = document.getElementById('price-slider-progress');
    const minGap = 50000;
    const sliderMax = parseInt(rangeMin.max);

    function updatePriceSlider(e) {
        let minVal = parseInt(rangeMin.value);
        let maxVal = parseInt(rangeMax.value);

        if (maxVal - minVal < minGap) {
            if (e.target.id === 'range-min') {
                rangeMin.value = maxVal - minGap;
                minVal = parseInt(rangeMin.value);
            } else {
                rangeMax.value = minVal + minGap;
                maxVal = parseInt(rangeMax.value);
            }
        } else {
            inputMin.value = minVal;
            inputMax.value = maxVal;
        }
        
        progress.style.left = (minVal / sliderMax) * 100 + "%";
        progress.style.right = 100 - (maxVal / sliderMax) * 100 + "%";
    }

    function updateRangeFromInput() {
        let minVal = parseInt(inputMin.value);
        let maxVal = parseInt(inputMax.value);
        
        if (isNaN(minVal)) minVal = 0;
        if (isNaN(maxVal)) maxVal = sliderMax;

        if (maxVal - minVal >= minGap && maxVal <= sliderMax && minVal >= 0) {
            rangeMin.value = minVal;
            rangeMax.value = maxVal;
            progress.style.left = (minVal / sliderMax) * 100 + "%";
            progress.style.right = 100 - (maxVal / sliderMax) * 100 + "%";
        }
    }

    // Init position on load
    document.addEventListener('DOMContentLoaded', () => {
        if(!inputMin.value) { inputMin.value = 0; rangeMin.value = 0; }
        if(!inputMax.value) { inputMax.value = sliderMax; rangeMax.value = sliderMax; }
        
        let minVal = parseInt(rangeMin.value);
        let maxVal = parseInt(rangeMax.value);
        progress.style.left = (minVal / sliderMax) * 100 + "%";
        progress.style.right = 100 - (maxVal / sliderMax) * 100 + "%";
    });
</script>
@endsection
