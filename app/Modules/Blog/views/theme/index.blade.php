@extends('theme::layouts.app')

@php($isGuidePage = $isGuidePage ?? false)
@section('title', $isGuidePage ? __('guideindex.page_title') : __('blogindex.page_title'))
@section('meta_description', $isGuidePage ? __('guideindex.meta_description') : __('blogindex.meta_description'))

@section('content')
@include('blog::theme.partials.auto-translate', ['autoTranslateSourceLang' => $isGuidePage ? 'en' : 'vi'])
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <div class="mb-10 text-center">
        <h1 class="text-4xl md:text-5xl font-display font-black text-slate-900 dark:text-white mb-4">{!! $isGuidePage ? __('guideindex.hero_title') : __('blogindex.hero_title') !!}</h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto text-lg">{{ $isGuidePage ? __('guideindex.hero_subtitle') : __('blogindex.hero_subtitle') }}</p>
    </div>

    @if($posts->isEmpty())
        <div class="text-center py-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <i class="fa-regular fa-newspaper text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700 dark:text-slate-300">{{ __('blogindex.empty_title') }}</h3>
            <p class="text-slate-500 mt-2">{{ __('blogindex.empty_subtitle') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 group hover:shadow-xl hover:shadow-blue-500/10 transition-all hover:-translate-y-1 flex flex-col h-full">
                    <!-- Thumbnail -->
                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if($post->image)
                            <img src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600">
                                <i class="fa-solid fa-gamepad text-5xl"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-grow">
                        <div class="flex items-center gap-2 mb-3 text-xs font-bold">

                            <span class="text-slate-400">{{ $post->pub_date ? $post->pub_date->format('d/m/Y') : $post->created_at->format('d/m/Y') }}</span>
                        </div>
                        
                        <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white leading-tight mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-3">
                            {{ $post->title }}
                        </h3>
                        
                        <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-3 mt-auto">
                            {{ $post->summary }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
