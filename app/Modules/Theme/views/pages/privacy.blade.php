@extends('theme::layouts.app')

@section('title', __('privacy.page_title'))

@section('content')
<div class="relative min-h-screen py-16 overflow-hidden">
    <!-- Abstract Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] rounded-full bg-emerald-600/10 mix-blend-screen blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[40%] h-[40%] rounded-full bg-teal-600/10 mix-blend-screen blur-[100px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 mb-6 border border-emerald-500/30 shadow-[0_0_30px_rgba(16,185,129,0.2)]">
                <i class="fa-solid fa-shield-alt text-4xl text-emerald-500"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-display font-black text-slate-900 dark:text-white tracking-tight mb-4">{{ __('privacy.heading') }}</h1>
            <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                {{ __('privacy.updated_label') }} <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ date('d/m/Y') }}</span>
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 items-start">
            <!-- Sidebar Table of Contents -->
            <div class="lg:w-1/4 w-full sticky top-28 hidden lg:block">
                <div class="glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-slate-700 pb-4">{{ __('privacy.toc_heading') }}</h3>
                    <nav class="space-y-3">
                        <a href="#thu-thap-thong-tin" class="block text-slate-500 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('privacy.toc_1') }}
                        </a>
                        <a href="#su-dung-thong-tin" class="block text-slate-500 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('privacy.toc_2') }}
                        </a>
                        <a href="#bao-ve-du-lieu" class="block text-slate-500 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('privacy.toc_3') }}
                        </a>
                        <a href="#quyen-nguoi-dung" class="block text-slate-500 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('privacy.toc_4') }}
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4 w-full">
                <div class="glass-card p-8 md:p-12 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl">
                    <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">

                        <p class="text-xl text-slate-600 dark:text-slate-300 leading-relaxed mb-10 font-medium">
                            {{ __('privacy.intro') }}
                        </p>

                        <div id="thu-thap-thong-tin" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-lg">1</span>
                                {{ __('privacy.s1_title') }}
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-emerald-500/30">
                                <p>{{ __('privacy.s1_p1') }}</p>
                                <ul class="list-disc pl-6 marker:text-emerald-500">
                                    <li>{{ __('privacy.s1_li1') }}</li>
                                    <li>{{ __('privacy.s1_li2') }}</li>
                                    <li>{{ __('privacy.s1_li3') }}</li>
                                    <li>{{ __('privacy.s1_li4') }}</li>
                                </ul>
                                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 p-4 rounded-xl text-amber-800 dark:text-amber-200 text-sm mt-4">
                                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> {!! __('privacy.s1_notice') !!}
                                </div>
                            </div>
                        </div>

                        <div id="su-dung-thong-tin" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-lg">2</span>
                                {{ __('privacy.s2_title') }}
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-blue-500/30">
                                <p>{{ __('privacy.s2_p1') }}</p>
                                <ul class="list-disc pl-6 marker:text-blue-500">
                                    <li>{!! __('privacy.s2_li1') !!}</li>
                                    <li>{!! __('privacy.s2_li2') !!}</li>
                                    <li>{!! __('privacy.s2_li3') !!}</li>
                                    <li>{!! __('privacy.s2_li4') !!}</li>
                                </ul>
                            </div>
                        </div>

                        <div id="bao-ve-du-lieu" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-lg">3</span>
                                {{ __('privacy.s3_title') }}
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-purple-500/30">
                                <p>{{ __('privacy.s3_p1') }}</p>
                                <ul class="list-disc pl-6 marker:text-purple-500">
                                    <li>{{ __('privacy.s3_li1') }}</li>
                                    <li>{{ __('privacy.s3_li2') }}</li>
                                    <li>{{ __('privacy.s3_li3') }}</li>
                                </ul>
                                <p class="font-bold text-slate-900 dark:text-white mt-4">{{ __('privacy.s3_commit_label') }}</p>
                                <p>{!! __('privacy.s3_p2') !!}</p>
                            </div>
                        </div>

                        <div id="quyen-nguoi-dung">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-500/20 text-teal-600 dark:text-teal-400 text-lg">4</span>
                                {{ __('privacy.s4_title') }}
                            </h2>
                            <p class="text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-teal-500/30">
                                {!! __('privacy.s4_p1') !!}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Bottom CTA -->
                <div class="mt-12 text-center">
                    <p class="text-slate-500 dark:text-slate-400 mb-6">{{ __('privacy.contact_cta') }}</p>
                    <a href="mailto:support@softwarepays.com" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold transition-transform hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                        <i class="fa-solid fa-envelope"></i> support@softwarepays.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    html { scroll-behavior: smooth; }
</style>
@endsection
