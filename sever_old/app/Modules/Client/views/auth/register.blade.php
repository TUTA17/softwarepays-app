@extends('client::layouts.app')

@section('title', 'Đăng ký - KeyGame')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full relative">
            
            <!-- Glow Effects -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-600/30 rounded-full mix-blend-screen filter blur-[50px] animate-pulse"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-600/30 rounded-full mix-blend-screen filter blur-[50px] animate-pulse" style="animation-delay: 2s;"></div>

            <div class="glass-card p-8 sm:p-10 rounded-3xl relative z-10 border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 shadow-2xl">
                
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20 transform rotate-3">
                        <i class="fa-solid fa-user-plus text-3xl text-slate-900 dark:text-white"></i>
                    </div>
                    <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Tạo Tài Khoản</h2>
                    <p class="text-slate-500 dark:text-slate-400">Gia nhập cộng đồng game thủ ngay hôm nay!</p>
                </div>
                
                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl flex items-start gap-3 mb-6 shadow-inner">
                        <i class="fa-solid fa-circle-exclamation mt-1 shrink-0"></i>
                        <ul class="list-disc pl-4 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    
                    @if(isset($ref) && $ref)
                        <input type="hidden" name="ref" value="{{ $ref }}">
                        <div class="bg-blue-500/10 border border-blue-500/30 text-blue-400 px-4 py-3 rounded-xl mb-4 text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-handshake"></i>
                            Bạn được giới thiệu bởi mã <b>{{ $ref }}</b>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="name">Tên hiển thị</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user text-slate-500"></i>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: John Doe" class="w-full bg-white dark:bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="email">Địa chỉ Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-slate-500"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" class="w-full bg-white dark:bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="password">Mật khẩu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-500"></i>
                            </div>
                            <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full bg-white dark:bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="password_confirmation">Xác nhận mật khẩu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-shield-check text-slate-500"></i>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" class="w-full bg-white dark:bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-slate-900 dark:text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2 group mt-6">
                        ĐĂNG KÝ <i class="fa-solid fa-user-check group-hover:scale-110 transition-transform"></i>
                    </button>
                    
                    <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                        Đã có tài khoản? 
                        <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold hover:underline transition-colors">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
