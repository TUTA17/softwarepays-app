@extends('theme::layouts.app')

@section('title', __('checkoutverify.page_title'))

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-rose-500/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 glass-card p-8 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-rose-500 to-orange-500"></div>
        
        <div>
            <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/50 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3 hover:rotate-0 transition-transform">
                <i class="fa-solid fa-lock text-3xl text-rose-600 dark:text-rose-400"></i>
            </div>
            <h2 class="text-center text-3xl font-display font-bold text-slate-900 dark:text-white">
                {{ __('checkoutverify.heading') }}
            </h2>
            <p class="mt-3 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('checkoutverify.description') }}
            </p>
        </div>

        @if (session('error'))
            <div class="bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-sm font-medium border border-rose-100 dark:border-rose-800 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
                {{ session('error') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('cart.checkout.verify.post') }}" method="POST">
            @csrf

            <div class="flex justify-between gap-2 max-w-[300px] mx-auto" id="otp-container">
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
            </div>

            <div class="text-center text-sm">
                <span class="text-slate-500">{{ __('checkoutverify.code_valid_for') }}:</span>
                <span id="countdown" class="font-bold text-rose-500 ml-1">05:00</span>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-rose-600 to-orange-500 hover:from-rose-500 hover:to-orange-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all shadow-lg shadow-rose-500/30 overflow-hidden mt-6">
                    {{ __('checkoutverify.complete_payment_button') }}
                </button>
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('cart.index') }}" class="text-sm font-medium text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> {{ __('checkoutverify.cancel_link') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // OTP Input Logic
        const inputs = document.querySelectorAll('#otp-container input');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text/plain').trim();
                if (/^\d+$/.test(pastedData)) {
                    const digits = pastedData.split('').slice(0, 6);
                    digits.forEach((digit, i) => {
                        if (inputs[i]) {
                            inputs[i].value = digit;
                            if (i < 5) inputs[i + 1].focus();
                        }
                    });
                }
            });
        });

        // Focus first input
        if (inputs.length > 0) {
            inputs[0].focus();
        }

        const countdownEl = document.getElementById('countdown');
        const storageKey = 'otp_expires_checkout';
        let endTime = localStorage.getItem(storageKey);
        
        if (!endTime || endTime < Date.now()) {
            endTime = Date.now() + 300 * 1000; // 5 phút
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
    });
</script>
@endsection
