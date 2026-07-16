@extends('client::layouts.app')

@section('title', 'Đăng nhập - KeyGame')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full relative">
            
            <!-- Glow Effects -->
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-600/30 rounded-full mix-blend-screen filter blur-[50px] animate-pulse"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[50px] animate-pulse" style="animation-delay: 2s;"></div>

            <div class="glass-card p-8 sm:p-10 rounded-3xl relative z-10 border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 shadow-2xl">
                
                <div class="text-center mb-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-500/20 transform -rotate-3">
                        <i class="fa-solid fa-gamepad text-3xl text-slate-900 dark:text-white"></i>
                    </div>
                    <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Đăng Nhập</h2>
                    <p class="text-slate-500 dark:text-slate-400">Chào mừng trở lại! Vui lòng đăng nhập để tiếp tục.</p>
                </div>
                
                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl flex items-start gap-3 mb-6 shadow-inner">
                        <i class="fa-solid fa-circle-exclamation mt-1"></i>
                        <span class="text-sm">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="email">Địa chỉ Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-slate-500"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" class="w-full bg-white dark:bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Mật khẩu</label>
                            <a href="#" class="text-xs text-blue-400 hover:text-blue-300 hover:underline">Quên mật khẩu?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-500"></i>
                            </div>
                            <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full bg-white dark:bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-blue-600 focus:ring-blue-500">
                        <label for="remember-me" class="ml-2 block text-sm text-slate-500 dark:text-slate-400 cursor-pointer">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary-glow text-slate-900 dark:text-white font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 group">
                        ĐĂNG NHẬP <i class="fa-solid fa-arrow-right-to-bracket group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    
                    <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                        Chưa có tài khoản? 
                        <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-semibold hover:underline transition-colors">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
