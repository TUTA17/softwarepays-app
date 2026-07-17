@extends('theme::layouts.app')

@section('title', __('cart.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('cart.title') }}</h1>
        <p class="text-slate-500 dark:text-slate-400">{{ __('cart.subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @include('theme::partials.user-sidebar')
        </div>

        <!-- Cart Content -->
        <div class="lg:col-span-3">
            @if(count($cart) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cart as $id => $item)
                            <div class="glass-card p-4 rounded-xl flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 relative group text-center sm:text-left">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full sm:w-32 h-40 sm:h-20 object-contain bg-white rounded-lg shadow-md">
                                
                                <div class="flex-1 w-full">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ $item['name'] }}</h3>
                                    <div class="text-emerald-400 font-bold mb-2 sm:mb-0">{!! \App\Helpers\CurrencyHelper::formatPrice($item['price']) !!}</div>
                                </div>

                                <div class="text-slate-500 dark:text-slate-400 text-sm w-full sm:w-auto flex justify-between sm:block items-center border-t border-slate-200 dark:border-slate-700 sm:border-0 pt-3 sm:pt-0">
                                    <span>{{ __('cart.quantity_prefix') }}: <span class="font-bold text-slate-900 dark:text-white">{{ $item['quantity'] }}</span></span>
                                    
                                    <form action="{{ route('cart.remove', $id) }}" method="POST" class="sm:ml-4 inline-block">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 flex items-center justify-center transition" title="{{ __('cart.remove') }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <div class="glass-card p-6 rounded-2xl sticky top-28 shadow-2xl">
                            <h2 class="text-xl font-display font-bold text-slate-900 dark:text-white mb-6 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 pb-4">{{ __('cart.summary_heading') }}</h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                                    <span>{{ __('cart.subtotal_label') }} ({{ count($cart) }} {{ __('cart.items_suffix') }})</span>
                                    <span class="font-bold">{!! \App\Helpers\CurrencyHelper::formatPrice($total) !!}</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                                    <span>{{ __('cart.discount_label') }}</span>
                                    <span>0đ</span>
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <span class="text-lg font-bold text-slate-900 dark:text-white">{{ __('cart.total') }}</span>
                                    <span class="text-2xl font-bold text-emerald-400">{!! \App\Helpers\CurrencyHelper::formatPrice($total) !!}</span>
                                </div>
                            </div>

                            <a href="{{ route('cart.checkout') }}" class="checkout-btn group relative flex w-full items-center gap-3 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-4 py-4 text-white shadow-lg shadow-blue-500/20 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-emerald-500/30">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white/15 text-lg">
                                    <i class="fa-solid fa-credit-card"></i>
                                </span>
                                <span class="min-w-0 flex-1 text-left text-base font-extrabold leading-tight tracking-wide sm:text-lg">{{ __('cart.checkout_button') }}</span>
                                <i class="fa-solid fa-arrow-right shrink-0 text-base transition-transform duration-300 group-hover:translate-x-1"></i>
                            </a>
                            
                            <div class="mt-4 text-center">
                                <a href="/" class="text-sm text-blue-500 hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> {{ __('cart.continue_shopping') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="glass-card p-12 rounded-2xl text-center shadow-xl h-full flex flex-col items-center justify-center min-h-[400px]">
                    <div class="text-slate-300 dark:text-slate-700 mb-6"><i class="fa-solid fa-cart-shopping text-6xl"></i></div>
                    <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('cart.empty') }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">{{ __('cart.empty_hint') }}</p>
                    <a href="/" class="inline-flex btn-primary-glow text-slate-900 dark:text-white px-8 py-3 rounded-xl font-bold items-center gap-2">
                        <i class="fa-solid fa-gamepad"></i> {{ __('cart.back_to_shop') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
