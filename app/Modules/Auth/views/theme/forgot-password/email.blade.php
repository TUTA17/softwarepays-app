@extends('theme::layouts.app')

@section('title', __('fpemail.page_title'))

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <!-- Background Decorators -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-blue-500/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 glass-card p-8 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
        
        <div>
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3 hover:rotate-0 transition-transform">
                <i class="fa-solid fa-envelope-open-text text-3xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h2 class="text-center text-3xl font-display font-bold text-slate-900 dark:text-white">
                {{ __('fpemail.heading') }}
            </h2>
            <p class="mt-3 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('fpemail.description') }}
            </p>
        </div>

        @if (session('status'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl text-sm font-medium border border-emerald-100 dark:border-emerald-800 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                {{ session('status') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('fpemail.email_label') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-slate-400"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none relative block w-full pl-10 px-3 py-3 border border-slate-300 dark:border-slate-600 placeholder-slate-500 text-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-slate-800 transition-colors"
                        placeholder="name@example.com" value="{{ old('email') }}">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-rose-500 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                @enderror
            </div>

            <!-- Google reCAPTCHA v2 / Cloudflare Turnstile -->
            @php
                $sysSettings = \App\Modules\Core\Models\Setting::getAllGrouped();
                $secSettings = $sysSettings['security_tab'] ?? [];
                $siteKey = $secSettings['recaptcha_site_key'] ?? '';
            @endphp
            
            @if($siteKey)
            <div class="flex justify-center">
                <!-- reCAPTCHA v2 -->
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" data-theme="dark"></div>
            </div>
            @endif

            @error('g-recaptcha-response')
                <p class="mt-2 text-sm text-rose-500 flex items-center justify-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror

            <div>
                <button type="submit" id="submitBtn"
                    class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg shadow-blue-500/30 overflow-hidden">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fa-solid fa-paper-plane text-blue-500 group-hover:text-blue-400 transition-colors"></i>
                    </span>
                    <span id="btnText">{{ __('fpemail.send_otp_button') }}</span>
                    <i class="fa-solid fa-circle-notch fa-spin hidden ml-2 text-white" id="loadingIcon"></i>
                </button>
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> {{ __('fpemail.back_to_login') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
        // Có thể thêm bước kiểm tra reCAPTCHA nếu cần thiết
        // e.preventDefault();
        
        const btnText = document.getElementById('btnText');
        const loadingIcon = document.getElementById('loadingIcon');
        const submitBtn = document.getElementById('submitBtn');
        
        btnText.innerText = @json(__('fpemail.sending_text'));
        loadingIcon.classList.remove('hidden');
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    });
</script>
@endsection
