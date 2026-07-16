@extends('theme::layouts.app')

@section('title', __('terms.page_title'))

@section('content')
<div class="relative min-h-screen py-16 overflow-hidden">
    <!-- Abstract Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] rounded-full bg-blue-600/10 mix-blend-screen blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[40%] h-[40%] rounded-full bg-indigo-600/10 mix-blend-screen blur-[100px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 mb-6 border border-blue-500/30 shadow-[0_0_30px_rgba(59,130,246,0.2)]">
                <i class="fa-solid fa-file-contract text-4xl text-blue-500"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-display font-black text-slate-900 dark:text-white tracking-tight mb-4">{{ __('terms.heading') }}</h1>
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
                        <a href="#quy-dinh-tai-khoan" class="block text-slate-500 hover:text-blue-500 dark:text-slate-400 dark:hover:text-blue-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('terms.toc_1') }}
                        </a>
                        <a href="#quy-dinh-mua-ban" class="block text-slate-500 hover:text-blue-500 dark:text-slate-400 dark:hover:text-blue-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('terms.toc_2') }}
                        </a>
                        <a href="#quy-dinh-gian-lan" class="block text-slate-500 hover:text-blue-500 dark:text-slate-400 dark:hover:text-blue-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('terms.toc_3') }}
                        </a>
                        <a href="#thay-doi" class="block text-slate-500 hover:text-blue-500 dark:text-slate-400 dark:hover:text-blue-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> {{ __('terms.toc_4') }}
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4 w-full">
                <div class="glass-card p-8 md:p-12 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl">
                    <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">

                        <p class="text-xl text-slate-600 dark:text-slate-300 leading-relaxed mb-10 font-medium">
                            {{ __('terms.intro') }}
                        </p>

                        <div id="quy-dinh-tai-khoan" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-lg">1</span>
                                {{ __('terms.s1_title') }}
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-blue-500/30">
                                <p>{!! __('terms.s1_p1') !!}</p>
                                <p>{!! __('terms.s1_p2') !!}</p>
                                <p>{!! __('terms.s1_p3') !!}</p>
                            </div>
                        </div>

                        <div id="quy-dinh-mua-ban" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-lg">2</span>
                                {{ __('terms.s2_title') }}
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-indigo-500/30">
                                <p>{!! __('terms.s2_p1') !!}</p>
                                <p>{!! __('terms.s2_p2') !!}</p>
                                <p>{!! __('terms.s2_p3') !!}</p>
                            </div>
                        </div>

                        <div id="quy-dinh-gian-lan" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-lg">3</span>
                                {{ __('terms.s3_title') }}
                            </h2>
                            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 p-6 rounded-2xl text-slate-700 dark:text-slate-300">
                                <p class="mb-0">
                                    {!! __('terms.s3_p1') !!}
                                </p>
                            </div>
                        </div>

                        <div id="thay-doi">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-lg">4</span>
                                {{ __('terms.s4_title') }}
                            </h2>
                            <p class="text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-emerald-500/30">
                                {{ __('terms.s4_p1') }}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Bottom CTA -->
                <div class="mt-12 text-center">
                    <p class="text-slate-500 dark:text-slate-400 mb-6">{{ __('terms.contact_cta') }}</p>
                    <a href="mailto:support@softwarepays.com" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold transition-transform hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                        <i class="fa-solid fa-envelope"></i> {{ __('terms.contact_button') }}
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
