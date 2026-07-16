@extends('theme::layouts.app')

@section('title', __('twofa.page_title'))

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-blue-500/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 glass-card p-8 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
        
        <div>
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3 hover:rotate-0 transition-transform">
                <i class="fa-solid fa-shield-alt text-3xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h2 class="text-center text-3xl font-display font-bold text-slate-900 dark:text-white">
                {{ __('twofa.heading') }}
            </h2>
            <p class="mt-3 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('twofa.description') }}
            </p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl text-sm font-medium border border-emerald-100 dark:border-emerald-800 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                {{ session('success') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('twofactor.verify.post') }}" method="POST">
            @csrf

            <div>
                <label for="otp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('twofa.otp_input_label') }}</label>
                <div class="relative">
                    <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
                        class="appearance-none relative block w-full px-3 py-4 text-center text-2xl tracking-[0.5em] font-bold border border-slate-300 dark:border-slate-600 placeholder-slate-400 text-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-slate-800 transition-colors"
                        placeholder="••••••" value="{{ old('otp') }}">
                </div>
                @error('otp')
                    <p class="mt-2 text-sm text-rose-500 flex items-center justify-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="text-center text-sm">
                <span class="text-slate-500">{{ __('checkoutverify.code_valid_for') }}:</span>
                <span id="countdown" class="font-bold text-rose-500 ml-1">10:00</span>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg shadow-blue-500/30 overflow-hidden">
                    {{ __('twofa.confirm_button') }}
                </button>
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> {{ __('twofa.back_link') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const countdownEl = document.getElementById('countdown');
    const storageKey = 'otp_expires_2fa';
    let endTime = localStorage.getItem(storageKey);
    
    if (!endTime || endTime < Date.now()) {
        endTime = Date.now() + 600 * 1000;
        localStorage.setItem(storageKey, endTime);
    }
    
    const updateTimer = () => {
        let timeLeft = Math.floor((endTime - Date.now()) / 1000);
        if (timeLeft <= 0) {
            clearInterval(timer);
            countdownEl.innerText = @json(__('checkoutverify.expired_label'));
            localStorage.removeItem(storageKey);
            return;
        }
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        countdownEl.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };
    
    updateTimer();
    const timer = setInterval(updateTimer, 1000);
</script>
@endsection
