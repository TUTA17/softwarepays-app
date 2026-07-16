@extends('theme::layouts.app')

@section('title', __('steamwallet.page_title'))

@section('content')
<div class="bg-slate-50 dark:bg-slate-900 min-h-screen pb-20">
    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-house mr-2"></i> {{ __('nav.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-xs mx-1"></i>
                        <span class="ml-1 text-slate-700 dark:text-slate-200 font-semibold">{{ __('steamwallet.breadcrumb_label') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Product Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 md:p-8 shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row gap-8">
            <!-- Product Image -->
            <div class="w-full md:w-1/3 lg:w-1/4">
                <div class="rounded-xl overflow-hidden shadow-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 aspect-square flex items-center justify-center p-0">
                    <img src="/images/steam_wallet_default.png" alt="Steam Wallet" class="w-full h-full object-cover">
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="w-full md:w-2/3 lg:w-3/4 flex flex-col justify-center">
                <div class="mb-2">
                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 mb-4">
                        <i class="fa-solid fa-bolt"></i> {{ __('steamwallet.badge_auto') }}
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-display font-bold text-slate-900 dark:text-white mb-4">
                    {{ __('steamwallet.heading') }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-6 mb-6">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <div class="flex text-amber-400">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span>{{ __('steamwallet.reviews_count') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <i class="fa-solid fa-truck-fast text-emerald-500"></i>
                        <span>{{ __('steamwallet.instant_delivery') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <i class="fa-solid fa-shield-alt text-blue-500"></i>
                        <span>{{ __('steamwallet.lifetime_warranty') }}</span>
                    </div>
                </div>

                <div class="prose dark:prose-invert text-slate-600 dark:text-slate-400 text-sm max-w-none">
                    <p>{{ __('steamwallet.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left: Options -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Denomination Selection -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-tags text-blue-600"></i> {{ __('steamwallet.choose_denomination') }}
                        </h3>

                        <!-- Region Filter -->
                        <div class="flex items-center bg-slate-100 dark:bg-slate-900 p-1 rounded-lg border border-slate-200 dark:border-slate-700">
                            <button onclick="filterRegion('all', this)" class="region-tab active px-3 py-1.5 text-sm font-semibold rounded-md bg-white dark:bg-slate-800 shadow-sm text-blue-600 dark:text-blue-400 transition-all">{{ __('steamwallet.region_all') }}</button>
                            <button onclick="filterRegion('Vietnam', this)" class="region-tab px-3 py-1.5 text-sm font-semibold rounded-md text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-all">{{ __('steamwallet.region_vietnam') }}</button>
                            <button onclick="filterRegion('Global', this)" class="region-tab px-3 py-1.5 text-sm font-semibold rounded-md text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-all">{{ __('steamwallet.region_global') }}</button>
                        </div>
                    </div>

                    @if($products->isEmpty())
                        <div class="p-4 rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 text-center font-medium border border-orange-200 dark:border-orange-800">
                            {{ __('steamwallet.out_of_stock_notice') }}
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="denomination-grid">
                            @foreach($products as $index => $product)
                                @php
                                    $genres = json_decode($product->genres, true) ?? [];
                                    $region = in_array('Vietnam', $genres) ? 'Vietnam' : (in_array('Global', $genres) ? 'Global' : 'Other');
                                    $regionColor = $region === 'Vietnam' ? 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400' : 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400';
                                @endphp
                                <div class="denomination-option region-{{ $region }} relative cursor-pointer border-2 border-slate-200 dark:border-slate-700 hover:border-blue-400 rounded-xl p-4 transition-all" 
                                     data-id="{{ $product->id }}" 
                                     data-price="{{ $product->price }}" 
                                     data-name="{{ $product->name }}"
                                     onclick="selectDenomination(this)">
                                    
                                    <div class="check-icon absolute top-2 right-2 text-blue-600 dark:text-blue-400 opacity-0 transition-opacity">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $regionColor }}">{{ $region }}</span>
                                    </div>

                                    <div class="font-bold text-slate-900 dark:text-white text-base mb-1">{{ str_replace('Steam Wallet ', '', $product->name) }}</div>
                                    <div class="text-blue-600 dark:text-blue-400 font-semibold text-sm">{!! \App\Helpers\CurrencyHelper::formatPrice($product->price) !!}</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div id="no-region-products" class="hidden p-4 rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 text-center font-medium border border-orange-200 dark:border-orange-800 mt-4">
                            {{ __('steamwallet.no_region_match') }}
                        </div>
                    @endif
                </div>

                <!-- Description/Guide -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-3">{{ __('steamwallet.guide_heading') }}</h3>
                    <div class="prose dark:prose-invert text-sm text-slate-600 dark:text-slate-400 space-y-4">
                        <p>{!! __('steamwallet.guide_step1') !!}</p>
                        <p>{!! __('steamwallet.guide_step2') !!}</p>
                        <p>{!! __('steamwallet.guide_step3') !!}</p>
                        <p>{!! __('steamwallet.guide_step4') !!}</p>
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-800 dark:text-blue-300">
                            {!! __('steamwallet.guide_note') !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Checkout Box -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 sticky top-24">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">{{ __('steamwallet.payment_info_heading') }}</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('steamwallet.product_label') }}:</span>
                            <span class="font-semibold text-slate-900 dark:text-white text-right" id="summary-name">
                                {{ $products->first() ? $products->first()->name : '---' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('steamwallet.unit_price_label') }}:</span>
                            <span class="font-semibold text-slate-900 dark:text-white" id="summary-price">
                                {!! $products->first() ? \App\Helpers\CurrencyHelper::formatPrice($products->first()->price) : \App\Helpers\CurrencyHelper::formatPrice(0) !!}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('steamwallet.quantity_label') }}:</span>
                            <div class="flex items-center border border-slate-300 dark:border-slate-600 rounded-lg overflow-hidden">
                                <button type="button" class="px-3 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 transition-colors" onclick="updateQty(-1)">-</button>
                                <input type="number" id="qty-input" value="1" min="1" max="10" class="w-12 text-center bg-transparent border-none focus:ring-0 text-sm font-semibold text-slate-900 dark:text-white" readonly>
                                <button type="button" class="px-3 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 transition-colors" onclick="updateQty(1)">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mb-6">
                        <div class="flex justify-between items-end">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">{{ __('steamwallet.total_label') }}:</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="summary-total">
                                {!! $products->first() ? \App\Helpers\CurrencyHelper::formatPrice($products->first()->price) : \App\Helpers\CurrencyHelper::formatPrice(0) !!}
                            </span>
                        </div>
                    </div>

                    @if(!$products->isEmpty())
                        <form id="buy-form" action="{{ route('cart.add', ['id' => $products->first()->id]) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="quantity" id="form-qty" value="1">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20">
                                <i class="fa-solid fa-credit-card"></i> {{ __('product.buy_now') }}
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full bg-slate-300 dark:bg-slate-700 text-slate-500 font-bold py-3.5 px-4 rounded-xl cursor-not-allowed">
                            {{ __('steamwallet.out_of_stock_button') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPrice = 0;
    
    function filterRegion(region, btnElement) {
        // Update active tab styles
        document.querySelectorAll('.region-tab').forEach(el => {
            el.classList.remove('active', 'bg-white', 'dark:bg-slate-800', 'shadow-sm', 'text-blue-600', 'dark:text-blue-400');
            el.classList.add('text-slate-500', 'hover:text-slate-700', 'dark:text-slate-400', 'dark:hover:text-slate-200');
        });
        
        btnElement.classList.remove('text-slate-500', 'hover:text-slate-700', 'dark:text-slate-400', 'dark:hover:text-slate-200');
        btnElement.classList.add('active', 'bg-white', 'dark:bg-slate-800', 'shadow-sm', 'text-blue-600', 'dark:text-blue-400');
        
        // Filter options
        let visibleCount = 0;
        let firstVisible = null;
        
        document.querySelectorAll('.denomination-option').forEach(el => {
            if (region === 'all' || el.classList.contains('region-' + region)) {
                el.style.display = 'block';
                visibleCount++;
                if (!firstVisible) firstVisible = el;
            } else {
                el.style.display = 'none';
            }
        });
        
        // Show/hide no products message
        if (visibleCount === 0) {
            document.getElementById('no-region-products').classList.remove('hidden');
            document.getElementById('buy-form').style.display = 'none';
        } else {
            document.getElementById('no-region-products').classList.add('hidden');
            document.getElementById('buy-form').style.display = 'block';
            if (firstVisible) {
                selectDenomination(firstVisible);
            }
        }
    }

    function selectDenomination(element) {
        // Reset all options
        document.querySelectorAll('.denomination-option').forEach(el => {
            el.classList.remove('border-blue-600', 'bg-blue-50', 'dark:bg-blue-900/20');
            el.classList.add('border-slate-200', 'dark:border-slate-700');
            el.querySelector('.check-icon').classList.add('opacity-0');
        });
        
        // Highlight selected
        element.classList.remove('border-slate-200', 'dark:border-slate-700', 'hover:border-blue-400');
        element.classList.add('border-blue-600', 'bg-blue-50', 'dark:bg-blue-900/20');
        element.querySelector('.check-icon').classList.remove('opacity-0');
        
        // Update data
        const id = element.getAttribute('data-id');
        const price = parseInt(element.getAttribute('data-price'));
        const name = element.getAttribute('data-name');
        
        currentPrice = price;
        
        // Update UI
        document.getElementById('summary-name').innerText = name;
        document.getElementById('summary-price').innerText = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        
        // Update Form
        document.getElementById('buy-form').action = "{{ url('/cart/add') }}/" + id;
        
        updateTotal();
    }
    
    function updateQty(change) {
        const input = document.getElementById('qty-input');
        let newVal = parseInt(input.value) + change;
        if(newVal < 1) newVal = 1;
        if(newVal > 10) newVal = 10;
        
        input.value = newVal;
        document.getElementById('form-qty').value = newVal;
        
        updateTotal();
    }
    
    function updateTotal() {
        const qty = parseInt(document.getElementById('qty-input').value);
        const total = currentPrice * qty;
        document.getElementById('summary-total').innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
    }

    // Init first selection
    document.addEventListener('DOMContentLoaded', () => {
        const firstOption = document.querySelector('.denomination-option');
        if (firstOption) {
            selectDenomination(firstOption);
        }
    });
</script>
@endsection
