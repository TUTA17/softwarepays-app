@extends('theme::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('mycoupons.page_title') }}</h1>
        <p class="text-slate-500 dark:text-slate-400">{{ __('mycoupons.page_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @include('theme::partials.user-sidebar')
        </div>

        <!-- Dashboard Content -->
        <div class="lg:col-span-3 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="flex border-b border-slate-200 dark:border-slate-700">
                    <button id="tab-active" onclick="filterCoupons('active')" class="px-6 py-4 text-sm font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400">
                        {{ __('mycoupons.tab_unused') }} ({{ $coupons->filter(function($c) { return $c->pivot->status == 'saved' && $c->isValid(); })->count() }})
                    </button>
                    <button id="tab-used" onclick="filterCoupons('used')" class="px-6 py-4 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                        {{ __('mycoupons.tab_used_expired') }}
                    </button>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4" id="coupon-list">
                    @forelse($coupons as $coupon)
                        @php
                            $isUsed = $coupon->pivot->status == 'used';
                            $isExpired = !$coupon->isValid();
                            $isDisabled = $isUsed || $isExpired;
                            $filterClass = $isDisabled ? 'coupon-used-item hidden' : 'coupon-active-item';
                        @endphp
                        
                        <div class="coupon-item {{ $filterClass }} relative flex items-stretch border {{ $isDisabled ? 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 opacity-75' : 'border-blue-100 dark:border-blue-500/20 bg-blue-50/50 dark:bg-blue-500/5' }} rounded-xl overflow-hidden group">
                            
                            <!-- Left part (Discount Value) -->
                            <div class="{{ $isDisabled ? 'bg-slate-200 dark:bg-slate-700 text-slate-500' : 'bg-blue-500 text-white' }} w-1/3 flex flex-col items-center justify-center p-4 relative border-r border-dashed border-white dark:border-slate-800">
                                <!-- Cutout dots -->
                                <div class="absolute -top-2 -right-2 w-4 h-4 bg-white dark:bg-slate-800 rounded-full"></div>
                                <div class="absolute -bottom-2 -right-2 w-4 h-4 bg-white dark:bg-slate-800 rounded-full"></div>
                                
                                <span class="text-xs uppercase tracking-wider mb-1 opacity-80 font-medium">{{ __('mycoupons.discount_label') }}</span>
                                @if($coupon->discount_type == 'fixed')
                                    <span class="text-xl font-bold">{{ number_format($coupon->discount_value) }}đ</span>
                                @else
                                    <span class="text-2xl font-bold">{{ $coupon->discount_value }}%</span>
                                @endif
                            </div>
                            
                            <!-- Right part (Details) -->
                            <div class="w-2/3 p-4 flex flex-col justify-center">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $coupon->code }}</span>
                                    @if($isUsed)
                                        <span class="text-[10px] px-2 py-1 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded font-bold uppercase">{{ __('mycoupons.status_used') }}</span>
                                    @elseif($isExpired)
                                        <span class="text-[10px] px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded font-bold uppercase">{{ __('mycoupons.status_expired') }}</span>
                                    @else
                                        <span class="text-[10px] px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded font-bold uppercase">{{ __('mycoupons.status_ready') }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 line-clamp-2">{{ $coupon->description }}</p>
                                <div class="text-[11px] text-slate-500 mt-auto">
                                    @if($coupon->min_order_amount > 0)
                                        <div class="mb-0.5">{{ __('mycoupons.min_order_prefix') }}: <span class="font-medium">{{ number_format($coupon->min_order_amount) }}đ</span></div>
                                    @endif
                                    @if($coupon->valid_until)
                                        <div>{{ __('mycoupons.expiry_prefix') }}: <span class="font-medium {{ $isExpired ? 'text-red-500' : '' }}">{{ $coupon->valid_until->format('d/m/Y') }}</span></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center">
                            <div class="text-5xl text-slate-300 dark:text-slate-600 mb-4"><i class="fa-solid fa-ticket"></i></div>
                            <p class="text-slate-500 mb-4">{{ __('mycoupons.empty_text') }}</p>
                            <a href="{{ route('coupons.index') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm">{{ __('mycoupons.find_now') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function filterCoupons(type) {
        // Toggle tabs
        const tabActive = document.getElementById('tab-active');
        const tabUsed = document.getElementById('tab-used');
        
        if (type === 'active') {
            tabActive.className = "px-6 py-4 text-sm font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400";
            tabUsed.className = "px-6 py-4 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300";
            
            document.querySelectorAll('.coupon-active-item').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.coupon-used-item').forEach(el => el.classList.add('hidden'));
        } else {
            tabUsed.className = "px-6 py-4 text-sm font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400";
            tabActive.className = "px-6 py-4 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300";
            
            document.querySelectorAll('.coupon-used-item').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.coupon-active-item').forEach(el => el.classList.add('hidden'));
        }
    }
</script>
@endsection
