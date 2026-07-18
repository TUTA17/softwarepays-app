@extends('theme::layouts.app')

@section('title', '403 - Forbidden Zone')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 relative overflow-hidden group">
    <!-- Cyberpunk Grid Background -->
    <div class="absolute inset-0 opacity-20 dark:opacity-40" style="background-image: linear-gradient(#ef4444 1px, transparent 1px), linear-gradient(90deg, #ef4444 1px, transparent 1px); background-size: 50px 50px; transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px); animation: gridMove 20s linear infinite;"></div>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-5deg); }
        }
        @keyframes glitch {
            0% { text-shadow: 0.05em 0 0 #ef4444, -0.05em -0.025em 0 #f97316, -0.025em 0.05em 0 #b91c1c; }
            14% { text-shadow: 0.05em 0 0 #ef4444, -0.05em -0.025em 0 #f97316, -0.025em 0.05em 0 #b91c1c; }
            15% { text-shadow: -0.05em -0.025em 0 #ef4444, 0.025em 0.025em 0 #f97316, -0.05em -0.05em 0 #b91c1c; }
            49% { text-shadow: -0.05em -0.025em 0 #ef4444, 0.025em 0.025em 0 #f97316, -0.05em -0.05em 0 #b91c1c; }
            50% { text-shadow: 0.025em 0.05em 0 #ef4444, 0.05em 0 0 #f97316, 0 -0.05em 0 #b91c1c; }
            99% { text-shadow: 0.025em 0.05em 0 #ef4444, 0.05em 0 0 #f97316, 0 -0.05em 0 #b91c1c; }
            100% { text-shadow: -0.025em 0 0 #ef4444, -0.025em -0.025em 0 #f97316, -0.025em -0.05em 0 #b91c1c; }
        }
        .glitch-text-403 { animation: glitch 1.5s linear infinite; }
    </style>

    <!-- Floating Game Elements -->
    <i class="fa-solid fa-shield-alt text-6xl text-rose-500/20 absolute top-[15%] left-[15%] filter blur-[2px]" style="animation: float 6s ease-in-out infinite;"></i>
    <i class="fa-solid fa-lock text-5xl text-orange-500/20 absolute bottom-[20%] right-[15%] filter blur-[1px]" style="animation: float-reverse 7s ease-in-out infinite;"></i>
    <i class="fa-solid fa-user-secret text-4xl text-rose-600/20 absolute top-[30%] right-[25%] filter blur-[3px]" style="animation: float 5s ease-in-out infinite;"></i>

    <!-- Glowing Orbs -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-rose-600/30 rounded-full blur-[100px] mix-blend-screen animate-pulse pointer-events-none"></div>

    <div class="max-w-3xl w-full text-center relative z-10 glass-card bg-white/60 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/20 dark:border-rose-700/50 rounded-3xl p-10 sm:p-16 shadow-2xl shadow-rose-900/20 transform transition-transform duration-500 hover:scale-[1.02]">
        
        <div class="text-[100px] sm:text-[150px] font-display font-black leading-none drop-shadow-2xl mb-2 text-transparent bg-clip-text bg-gradient-to-br from-rose-600 to-orange-500 dark:from-rose-400 dark:to-orange-400 relative inline-block">
            <span class="glitch-text-403 relative inline-block">403</span>
        </div>
        
        <div class="inline-block px-4 py-1.5 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-bold tracking-widest uppercase mb-6 shadow-sm border border-rose-200 dark:border-rose-500/30">
            <i class="fa-solid fa-hand text-lg mr-1"></i> Dừng Lại
        </div>

        <h2 class="text-3xl sm:text-4xl font-display font-bold text-slate-900 dark:text-white mb-4">
            Khu vực hạn chế!
        </h2>
        
        <p class="text-slate-600 dark:text-slate-400 mb-10 text-lg max-w-xl mx-auto">
            Nhân vật của bạn chưa đủ cấp độ (hoặc quyền hạn) để tiến vào khu vực này. Vui lòng đăng nhập với tài khoản có thẩm quyền hoặc quay lại.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-rose-600 to-orange-500 text-white font-bold hover:from-rose-500 hover:to-orange-400 shadow-lg shadow-rose-600/30 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-600/40 relative overflow-hidden group">
                <div class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                <i class="fa-solid fa-house text-lg"></i> Về Căn Cứ
            </a>
            
            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-rose-500/50 hover:text-rose-600 dark:hover:text-rose-400 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm hover:shadow-md">
                <i class="fa-solid fa-right-to-bracket text-lg"></i> Đăng Nhập Lại
            </a>
        </div>
    </div>
</div>
@endsection
