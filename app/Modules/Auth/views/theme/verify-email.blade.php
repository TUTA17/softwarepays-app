@extends('theme::layouts.app')

@section('title', 'Xác minh địa chỉ email')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-blue-500/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 glass-card p-8 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-emerald-500"></div>

        <div>
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3 hover:rotate-0 transition-transform">
                <i class="fa-solid fa-envelope-circle-check text-3xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h2 class="text-center text-3xl font-display font-bold text-slate-900 dark:text-white">
                Xác minh địa chỉ email
            </h2>
            <p class="mt-3 text-center text-sm text-slate-500 dark:text-slate-400">
                Nhập mã 6 chữ số vừa được gửi đến email của bạn.
            </p>
        </div>

        @if (session('error'))
            <div class="bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-sm font-medium border border-rose-100 dark:border-rose-800 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl text-sm font-medium border border-emerald-100 dark:border-emerald-800 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                {{ session('success') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('verify.email.verify') }}" method="POST">
            @csrf

            <div class="flex justify-between gap-2 max-w-[300px] mx-auto" id="otp-container">
                @for ($i = 0; $i < 6; $i++)
                <input type="text" name="otp[]" maxlength="1" class="w-12 h-14 text-center text-2xl font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none text-slate-900 dark:text-white" required autocomplete="off">
                @endfor
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-emerald-500 hover:from-blue-500 hover:to-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg shadow-blue-500/30 overflow-hidden mt-6">
                    Xác minh
                </button>
            </div>
        </form>

        <form action="{{ route('verify.email.resend') }}" method="POST" class="text-center">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <i class="fa-solid fa-rotate-right mr-1"></i> Gửi lại mã
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                Để sau
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        if (inputs.length > 0) inputs[0].focus();
    });
</script>
@endsection
