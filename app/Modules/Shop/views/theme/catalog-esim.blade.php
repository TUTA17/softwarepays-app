@extends('theme::layouts.app')

@section('title', __('catalog.esim_page_title') . ' - SoftwarePays')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">{{ __('nav.home') }}</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700 dark:text-slate-300">{{ __('catalog.esim_page_title') }}</span>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-earth-asia text-3xl text-blue-500"></i>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ __('catalog.esim_page_title') }}</h1>
        </div>

        <form action="{{ route('catalog.esim') }}" method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('catalog.esim_search_placeholder') }}"
                   class="w-full md:w-80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if(request()->filled('q'))
                <a href="{{ route('catalog.esim') }}" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    @if($products->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-16 text-center text-slate-500">
            <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('catalog.esim_no_destination_found') }}</h3>
            <p>{{ __('catalog.try_different_keyword') }}</p>
        </div>
    @else
        <div class="text-sm text-slate-500 mb-4">
            {!! __('catalog.esim_found_count', ['count' => '<b>'.$products->total().'</b>']) !!}
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $product)
                <a href="{{ route('catalog.esim.show', $product->id) }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:border-blue-400 hover:shadow-md transition flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                        @if($product->header_image)
                            <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-sim-card text-2xl text-blue-500"></i>
                        @endif
                    </div>
                    <h3 class="font-semibold text-slate-900 dark:text-white text-sm leading-snug line-clamp-2">{{ $product->name }}</h3>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
