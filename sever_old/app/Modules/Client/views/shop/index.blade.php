@extends('client::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Cửa Hàng Game</h1>
            <p class="text-slate-500">Tìm kiếm và khám phá những tựa game đỉnh cao nhất.</p>
        </div>
        
        <!-- Mobile Filter Toggle -->
        <button class="md:hidden flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-700 dark:text-slate-300 font-medium" onclick="document.getElementById('mobile-filter').classList.toggle('hidden')">
            <i class="fa-solid fa-filter"></i> Bộ Lọc
        </button>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filter (Desktop) -->
        <div id="mobile-filter" class="hidden md:block w-full md:w-64 shrink-0">
            <form action="{{ route('shop') }}" method="GET" class="glass-card rounded-2xl p-6 sticky top-24">
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tìm kiếm</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên game..." class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg pl-10 pr-4 py-2 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Khoảng Giá (VNĐ)</label>
                    
                    <div class="relative w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mb-6 mt-2">
                        <div id="price-slider-progress" class="absolute h-full bg-blue-500 rounded-full" style="left: 0%; right: 0%;"></div>
                        <input type="range" id="range-min" min="0" max="3000000" step="10000" value="{{ request('min_price', 0) }}" class="absolute w-full -top-2 appearance-none bg-transparent pointer-events-none focus:outline-none" style="pointer-events: none; z-index: 2;" oninput="updatePriceSlider(event)">
                        <input type="range" id="range-max" min="0" max="3000000" step="10000" value="{{ request('max_price', 3000000) }}" class="absolute w-full -top-2 appearance-none bg-transparent pointer-events-none focus:outline-none" style="pointer-events: none; z-index: 2;" oninput="updatePriceSlider(event)">
                    </div>

                    <div class="flex items-center gap-2 mb-4">
                        <input type="number" id="input-min-price" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" oninput="updateRangeFromInput()">
                        <span class="text-slate-400">-</span>
                        <input type="number" id="input-max-price" name="max_price" value="{{ request('max_price') }}" placeholder="3000000" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" oninput="updateRangeFromInput()">
                    </div>
                </div>

                <!-- Discount Filter -->
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-bold cursor-pointer">
                        <input type="checkbox" name="is_discounted" value="1" class="text-blue-600 rounded" {{ request('is_discounted') == '1' ? 'checked' : '' }} onchange="this.form.submit()"> 
                        Game đang giảm giá <i class="fa-solid fa-tags text-rose-500 ml-1"></i>
                    </label>
                </div>

                <!-- Genre Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Thể Loại</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @php $currentGenres = request('genres', []); @endphp
                        @foreach($genres as $genre)
                        <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <input type="checkbox" name="genres[]" value="{{ $genre }}" class="text-blue-600 rounded" {{ in_array($genre, $currentGenres) ? 'checked' : '' }} onchange="this.form.submit()"> {{ $genre }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 transition-colors text-white rounded-lg font-bold shadow-lg shadow-blue-500/30">Lọc kết quả</button>
            </form>
        </div>

        <!-- Main Product Grid -->
        <div class="flex-1">
            
            <!-- Sort Bar -->
            <div class="flex justify-between items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 mb-6">
                <div class="text-slate-500 text-sm">
                    Hiển thị <b>{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</b> trên tổng số <b>{{ $products->total() }}</b> game
                </div>
                <div>
                    <select onchange="window.location.href=this.value" class="bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 pl-3 pr-8">
                        @php 
                            $params = request()->except('sort', 'page');
                        @endphp
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="{{ route('shop', array_merge($params, ['sort' => 'discount_desc'])) }}" {{ request('sort') == 'discount_desc' ? 'selected' : '' }}>Giảm giá sâu nhất</option>
                    </select>
                </div>
            </div>

            <!-- Grid -->
            @if($products->isEmpty())
                <div class="glass-card rounded-2xl p-16 text-center text-slate-500">
                    <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
                    <h3 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-2">Không tìm thấy Game nào</h3>
                    <p>Vui lòng thử điều chỉnh lại bộ lọc hoặc từ khóa tìm kiếm.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <a href="{{ route('product.show', ['id' => $product->id, 'slug' => \Illuminate\Support\Str::slug($product->name)]) }}" class="glass-card rounded-2xl overflow-hidden group block hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                            <!-- Image -->
                            <div class="relative aspect-[16/9] overflow-hidden">
                                <img src="{{ $product->header_image ?? 'https://placehold.co/600x337/1e293b/ffffff?text=No+Image' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                
                                @if($product->original_price && $product->original_price > $product->price)
                                    @php
                                        $discount = round((($product->original_price - $product->price) / $product->original_price) * 100);
                                    @endphp
                                    <div class="absolute top-2 right-2 bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded shadow-lg">
                                        -{{ $discount }}%
                                    </div>
                                @endif
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-5 flex flex-col h-[180px]">
                                <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white mb-1 line-clamp-1 group-hover:text-blue-500 transition-colors">{{ $product->name }}</h3>
                                
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 uppercase tracking-wider">STEAM</span>
                                </div>
                                
                                <div class="mt-auto pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-between items-end">
                                    <div>
                                        @if($product->original_price && $product->original_price > $product->price)
                                            <div class="text-xs text-slate-500 line-through mb-0.5">{{ number_format($product->original_price) }}đ</div>
                                        @endif
                                        <div class="text-xl font-bold text-emerald-400">{{ number_format($product->price) }}đ</div>
                                    </div>
                                    
                                    <button class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-500 transition-all flex items-center justify-center shadow-lg transform group-hover:-translate-y-1">
                                        <i class="fa-solid fa-cart-plus"></i>
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
