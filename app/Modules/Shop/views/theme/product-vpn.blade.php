@extends('theme::layouts.app')

@section('title', $product->name . ' - ' . __('productvpn.meta_title_suffix'))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">{{ __('nav.home') }}</a>
        <span class="mx-2">/</span>
        <a href="{{ route('catalog.vpn') }}" class="hover:text-blue-600">{{ __('home_categories.vpn') }}</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ $product->name }}</span>
    </nav>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-6">
        @if($product->header_image)
            <div class="relative h-64 sm:h-80 w-full overflow-hidden bg-gradient-to-br from-blue-500/20 to-blue-700/10 flex items-center justify-center">
                <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
            </div>
        @endif
        <div class="p-6">
        <div class="flex items-center gap-3 mb-3">
            @unless($product->header_image)
                <i class="fa-solid fa-shield-alt text-3xl text-blue-500"></i>
            @endunless
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $product->name }}</h1>
        </div>
        @if($product->description)
            <div class="text-sm text-slate-500 dark:text-slate-400 prose dark:prose-invert max-w-none">{!! nl2br(e(strip_tags($product->description, '<b><i><br><ul><li><strong>'))) !!}</div>
        @endif
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-blue-500"></i> {{ __('productvpn.choose_package') }}
        </h2>

        @if($packages->isEmpty())
            <p class="text-slate-500">{{ __('productvpn.no_packages') }}</p>
        @else
            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-6">
                    @foreach($packages as $i => $pkg)
                    <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-blue-400 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-blue-500/10 transition">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="variant_id" value="{{ $pkg->id }}" {{ $i === 0 ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">{{ $pkg->name }}</p>
                                <p class="text-xs text-slate-500">
                                    @if($pkg->months) {{ $pkg->months }} {{ __('productvpn.month_unit') }} @endif
                                    @if($pkg->gig) &middot; {{ $pkg->gig }} GB @endif
                                </p>
                            </div>
                        </div>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{!! \App\Helpers\CurrencyHelper::formatPrice($pkg->price) !!}</span>
                    </label>
                    @endforeach
                </div>

                @auth
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
                        <i class="fa-solid fa-cart-plus mr-2"></i> {{ __('product.add_to_cart_full') }}
                    </button>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="block text-center w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 rounded-xl transition">
                        {{ __('product.login_to_buy') }}
                    </a>
                @endauth
            </form>
        @endif
    </div>
</div>
@endsection
