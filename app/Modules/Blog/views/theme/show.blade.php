@extends('theme::layouts.app')

@section('title', $post->title)
@section('meta_description', Str::limit($post->summary, 150))

@push('styles')
<style>
    .news-content {
        font-size: 1.125rem;
        line-height: 1.8;
        color: #334155;
    }
    .dark .news-content {
        color: #cbd5e1;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.75rem;
        margin: 2rem auto;
        display: block;
    }
    .news-content a {
        color: #2563eb;
        text-decoration: underline;
    }
    .dark .news-content a {
        color: #60a5fa;
    }
    .news-content h2, .news-content h3 {
        color: #0f172a;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }
    .dark .news-content h2, .dark .news-content h3 {
        color: #f8fafc;
    }
    .news-content p {
        margin-bottom: 1.25rem;
    }
</style>
@endpush

@php($isGuidePost = optional($post->category)->slug === 'huong-dan')
@section('content')
@include('blog::theme.partials.auto-translate', ['autoTranslateSourceLang' => $isGuidePost ? 'en' : 'vi'])
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <!-- Breadcrumb -->
    <div class="text-sm text-slate-500 dark:text-slate-400 mb-8 flex items-center flex-wrap gap-2 font-medium">
        <a href="/" class="hover:text-blue-600 dark:hover:text-blue-400 transition"><i class="fa-solid fa-house"></i></a>
        <span><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
        <a href="{{ $isGuidePost ? route('blog.guides') : route('blog.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">{{ $isGuidePost ? __('blogshow.breadcrumb_guide') : __('blogshow.breadcrumb_news') }}</a>
        <span><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
        <span class="text-slate-800 dark:text-slate-200 truncate max-w-[200px] sm:max-w-md">{{ $post->title }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Cột Trái (Nội dung) -->
        <div class="lg:col-span-2">
            <article class="bg-white dark:bg-slate-900 rounded-3xl p-6 md:p-10 border border-slate-200 dark:border-slate-800 shadow-sm">
                
                <div class="flex items-center gap-3 mb-6">

                    <span class="text-sm text-slate-500 flex items-center gap-1.5"><i class="fa-regular fa-calendar"></i> {{ $post->pub_date ? $post->pub_date->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-display font-bold text-slate-900 dark:text-white mb-6 leading-tight">{{ $post->title }}</h1>
                
                <div class="text-lg font-medium text-slate-600 dark:text-slate-300 mb-8 pb-8 border-b border-slate-100 dark:border-slate-800 italic">
                    {{ $post->summary }}
                </div>

                @if($post->image && !str_contains($post->content, $post->image))
                    <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full rounded-2xl mb-8 object-cover aspect-video">
                @endif

                <div class="news-content">
                    {!! $post->content !!}
                </div>
                
                <!-- Share -->
                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ __('blogshow.share_label') }}</span>
                    <div class="flex gap-2">
                        <button class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition"><i class="fa-brands fa-facebook-f"></i></button>
                        <button class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center hover:bg-sky-600 transition"><i class="fa-brands fa-twitter"></i></button>
                        <button class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white flex items-center justify-center hover:bg-slate-300 dark:hover:bg-slate-600 transition" onclick="navigator.clipboard.writeText(window.location.href); alert({{ Js::from(__('blogshow.link_copied_alert')) }});"><i class="fa-solid fa-link"></i></button>
                    </div>
                </div>
            </article>
        </div>

        <!-- Cột Phải (Sidebar) -->
        <div class="space-y-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm sticky top-24">
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-fire text-rose-500"></i> {{ __('blogshow.latest_posts_heading') }}
                </h3>
                
                <div class="space-y-6">
                    @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="group flex gap-4 items-start">
                            <div class="w-24 h-20 shrink-0 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800">
                                @if($rel->image)
                                    <img src="{{ $rel->image }}" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600 text-2xl">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug mb-1.5">{{ $rel->title }}</h4>
                                <span class="text-xs text-slate-500"><i class="fa-regular fa-clock"></i> {{ $rel->pub_date ? $rel->pub_date->format('d/m/Y') : $rel->created_at->format('d/m/Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <a href="{{ $isGuidePost ? route('blog.guides') : route('blog.index') }}" class="block w-full text-center mt-6 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/30 dark:hover:text-blue-400 transition-colors">
                    {{ $isGuidePost ? __('blogshow.view_all_guides') : __('blogshow.view_all_news') }}
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
