@extends('theme::layouts.app')

@section('title', '404 - Mission Failed')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 relative overflow-hidden group">
    <!-- Cyberpunk Grid Background -->
    <div class="absolute inset-0 opacity-20 dark:opacity-40" style="background-image: linear-gradient(#3b82f6 1px, transparent 1px), linear-gradient(90deg, #3b82f6 1px, transparent 1px); background-size: 50px 50px; transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px); animation: gridMove 20s linear infinite;"></div>
    
    <style>
        @keyframes gridMove {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px) translateZ(-200px); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-5deg); }
        }
        @keyframes glitch {
            0% { text-shadow: 0.05em 0 0 #ef4444, -0.05em -0.025em 0 #3b82f6, -0.025em 0.05em 0 #10b981; }
            14% { text-shadow: 0.05em 0 0 #ef4444, -0.05em -0.025em 0 #3b82f6, -0.025em 0.05em 0 #10b981; }
            15% { text-shadow: -0.05em -0.025em 0 #ef4444, 0.025em 0.025em 0 #3b82f6, -0.05em -0.05em 0 #10b981; }
            49% { text-shadow: -0.05em -0.025em 0 #ef4444, 0.025em 0.025em 0 #3b82f6, -0.05em -0.05em 0 #10b981; }
            50% { text-shadow: 0.025em 0.05em 0 #ef4444, 0.05em 0 0 #3b82f6, 0 -0.05em 0 #10b981; }
            99% { text-shadow: 0.025em 0.05em 0 #ef4444, 0.05em 0 0 #3b82f6, 0 -0.05em 0 #10b981; }
            100% { text-shadow: -0.025em 0 0 #ef4444, -0.025em -0.025em 0 #3b82f6, -0.025em -0.05em 0 #10b981; }
        }
        .glitch-text { animation: glitch 1s linear infinite; }
    </style>

    <!-- Floating Game Elements -->
    <i class="fa-solid fa-gamepad text-6xl text-blue-500/20 absolute top-[15%] left-[15%] filter blur-[2px]" style="animation: float 6s ease-in-out infinite;"></i>
    <i class="fa-solid fa-headset text-5xl text-rose-500/20 absolute bottom-[20%] right-[15%] filter blur-[1px]" style="animation: float-reverse 7s ease-in-out infinite;"></i>
    <i class="fa-solid fa-ghost text-4xl text-emerald-500/20 absolute top-[30%] right-[25%] filter blur-[3px]" style="animation: float 5s ease-in-out infinite;"></i>

    <!-- Glowing Orbs -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-blue-600/30 rounded-full blur-[100px] mix-blend-screen animate-pulse pointer-events-none"></div>

    <div class="max-w-3xl w-full text-center relative z-10 glass-card bg-white/60 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/20 dark:border-slate-700/50 rounded-3xl p-10 sm:p-16 shadow-2xl shadow-blue-900/20 transform transition-transform duration-500 hover:scale-[1.02]">
        
        <div class="text-[100px] sm:text-[150px] font-display font-black leading-none drop-shadow-2xl mb-2 text-transparent bg-clip-text bg-gradient-to-br from-slate-800 to-slate-900 dark:from-white dark:to-slate-300 relative inline-block">
            <span class="glitch-text relative inline-block">404</span>
        </div>
        
        <div class="inline-block px-4 py-1.5 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-bold tracking-widest uppercase mb-6 shadow-sm border border-rose-200 dark:border-rose-500/30">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Nhiệm vụ thất bại
        </div>

        <h2 class="text-3xl sm:text-4xl font-display font-bold text-slate-900 dark:text-white mb-4">
            Bản đồ này không tồn tại!
        </h2>
        
        <p class="text-slate-600 dark:text-slate-400 mb-10 text-lg max-w-xl mx-auto">
            Có vẻ như bạn đã đi lạc vào vùng đất chưa được khai phá. Hãy kiểm tra lại tọa độ (đường dẫn) hoặc trở về căn cứ an toàn.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-600/40 relative overflow-hidden group">
                <div class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                <i class="fa-solid fa-house text-lg"></i> Về Căn Cứ
            </a>
            
            <a href="{{ route('shop') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-blue-500/50 hover:text-blue-600 dark:hover:text-blue-400 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm hover:shadow-md">
                <i class="fa-solid fa-store text-lg"></i> Khám Phá Cửa Hàng
            </a>
        </div>
    </div>
</div>
@endsection
