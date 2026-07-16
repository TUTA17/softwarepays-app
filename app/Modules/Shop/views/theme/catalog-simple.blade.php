@extends('theme::layouts.app')

@section('title', $title . ' - SoftwarePays')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">{{ __('nav.home') }}</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ $title }}</span>
    </nav>

    <div class="flex items-center gap-3 mb-8">
        <i class="fa-solid fa-box-open text-3xl text-blue-500"></i>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $title }}</h1>
    </div>

    @if($products->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-16 text-center text-slate-500">
            <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('catalog.empty_in_category', ['title' => $title]) }}</h3>
            <p>{{ __('catalog.please_check_back') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:border-blue-400 hover:shadow-md transition flex flex-col">
                    <div class="flex items-center gap-3 mb-3">
                        @if($product->header_image)
                            <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-box-open text-2xl text-blue-500"></i>
                            </div>
                        @endif
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-snug line-clamp-2">{{ $product->name }}</h3>
                    </div>

                    @if($product->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ Str::limit(strip_tags($product->description), 100) }}</p>
                    @endif

                    <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
                        <span class="font-bold text-blue-600 dark:text-blue-400">{!! \App\Helpers\CurrencyHelper::formatPrice($product->price) !!}</span>

                        @auth
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg transition flex items-center gap-2">
                                    <i class="fa-solid fa-cart-plus"></i> {{ __('product.buy_now') }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold px-4 py-2 rounded-lg transition">
                                {{ __('header.login') }}
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
