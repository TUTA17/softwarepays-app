@extends('theme::layouts.app')

@section('title', __('support.page_title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-4">
            {!! __('support.hero_title') !!}
        </h1>
        <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            {{ __('support.hero_subtitle') }}
        </p>
    </div>

    <div class="flex flex-col md:flex-row gap-12 mb-16">
        <!-- Contact Info & Socials -->
        <div class="md:w-1/3">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">{{ __('support.contact_info_heading') }}</h2>
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-slate-800 flex items-center justify-center text-blue-500 text-xl mr-4 shrink-0">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">{{ __('support.email_label') }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ \App\Modules\Core\Models\Setting::getValue('support_email', 'support@keygame.com') }}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('support.email_response_note') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-slate-800 flex items-center justify-center text-emerald-500 text-xl mr-4 shrink-0">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">{{ __('support.hotline_label') }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ \App\Modules\Core\Models\Setting::getValue('hotline', '0123 456 789') }}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('support.hotline_hours_note') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-slate-800 flex items-center justify-center text-rose-500 text-xl mr-4 shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">{{ __('support.office_label') }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ __('support.office_location') }}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('support.office_online_only') }}</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mt-12 mb-6">{{ __('support.follow_us_heading') }}</h2>
            <div class="flex gap-4">
                @if(\App\Modules\Core\Models\Setting::getValue('facebook_link'))
                <a href="{{ \App\Modules\Core\Models\Setting::getValue('facebook_link') }}" target="_blank" class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/30">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                @endif
                @if(\App\Modules\Core\Models\Setting::getValue('discord_link'))
                <a href="{{ \App\Modules\Core\Models\Setting::getValue('discord_link') }}" target="_blank" class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-600/30">
                    <i class="fa-brands fa-discord"></i>
                </a>
                @endif
                @if(\App\Modules\Core\Models\Setting::getValue('telegram_link'))
                <a href="{{ \App\Modules\Core\Models\Setting::getValue('telegram_link') }}" target="_blank" class="w-12 h-12 rounded-xl bg-sky-500 text-white flex items-center justify-center text-xl hover:bg-sky-600 transition-colors shadow-lg shadow-sky-500/30">
                    <i class="fa-brands fa-telegram"></i>
                </a>
                @endif
            </div>
        </div>

        <!-- Contact Form -->
        <div class="md:w-2/3">
            <div class="glass-card rounded-3xl p-8 border border-slate-200 dark:border-slate-700 shadow-xl">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('support.form_heading') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8">{{ __('support.form_subtitle') }}</p>

                <form action="#" method="POST" onsubmit="event.preventDefault(); alert({{ Js::from(__('support.form_success_alert')) }});">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('support.full_name_label') }}</label>
                            <input type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="{{ __('support.full_name_placeholder') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('support.contact_email_label') }}</label>
                            <input type="email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="{{ __('support.contact_email_placeholder') }}" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('support.issue_type_label') }}</label>
                        <select class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow appearance-none">
                            <option>{{ __('support.issue_option_1') }}</option>
                            <option>{{ __('support.issue_option_2') }}</option>
                            <option>{{ __('support.issue_option_3') }}</option>
                            <option>{{ __('support.issue_option_4') }}</option>
                            <option>{{ __('support.issue_option_5') }}</option>
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('support.issue_detail_label') }}</label>
                        <textarea class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow h-32 resize-none" placeholder="{{ __('support.issue_detail_placeholder') }}" required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex justify-center items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> {{ __('support.send_request_button') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq" class="mt-20 scroll-mt-28">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">{{ __('support.faq_heading') }}</h2>
            <p class="text-slate-500 dark:text-slate-400">{{ __('support.faq_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
            <div class="glass-card rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-question text-blue-500 mt-1"></i> {{ __('support.faq_q1') }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                    {{ __('support.faq_a1') }}
                </p>
            </div>

            <div class="glass-card rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-question text-blue-500 mt-1"></i> {{ __('support.faq_q2') }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                    {{ __('support.faq_a2') }}
                </p>
            </div>

            <div class="glass-card rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-question text-blue-500 mt-1"></i> {{ __('support.faq_q3') }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                    {{ __('support.faq_a3') }}
                </p>
            </div>

            <div class="glass-card rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-question text-blue-500 mt-1"></i> {{ __('support.faq_q4') }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                    {{ __('support.faq_a4') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
