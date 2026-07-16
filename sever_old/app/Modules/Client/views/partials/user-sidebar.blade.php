<!-- User Profile Card -->
<div class="glass-card p-6 rounded-2xl relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
    <div class="flex items-center gap-4 mb-6">
        <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-slate-700 to-slate-600 border-2 border-slate-500 flex items-center justify-center text-xl text-slate-900 dark:text-white font-bold shadow-lg">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</p>
        </div>
    </div>
    
    <div class="space-y-4">
        <div class="bg-white dark:bg-white/50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50">
            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider font-semibold">Số dư ví</div>
            <div class="text-2xl font-display font-bold text-blue-400">{{ number_format(Auth::user()->balance) }}đ</div>
        </div>
        <div class="bg-white dark:bg-white/50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center">
            <div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider font-semibold">Điểm thưởng</div>
                <div class="text-lg font-bold text-emerald-400">{{ number_format(Auth::user()->points) }} pts</div>
            </div>
            <i class="fa-solid fa-gift text-2xl text-emerald-500/20"></i>
        </div>
    </div>
    
    <a href="{{ route('wallet.show') }}" class="mt-6 flex items-center justify-center gap-2 w-full btn-primary-glow text-slate-900 dark:text-white py-3 rounded-xl font-semibold transition-all shadow-lg group">
        <i class="fa-solid fa-wallet group-hover:scale-110 transition-transform"></i> Nạp Tiền Vào Ví
    </a>
</div>

<!-- Navigation Menu -->
<div class="glass-card p-4 rounded-2xl mt-6">
    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors' }}">
            <i class="fa-solid fa-layer-group w-5 text-center"></i> Kho Game Của Tôi
        </a>
        <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('cart.index') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors' }}">
            <i class="fa-solid fa-cart-shopping w-5 text-center"></i> Giỏ Hàng
            @if(session()->has('cart') && count(session('cart')) > 0)
                <span class="ml-auto bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count(session('cart')) }}</span>
            @endif
        </a>
        <a href="{{ route('wallet.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('wallet.show') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors' }}">
            <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i> Nạp Tiền & Lịch Sử
        </a>
        <a href="javascript:void(0)" onclick="alert('Tính năng Cài đặt đang được phát triển!')" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors">
            <i class="fa-solid fa-gear w-5 text-center"></i> Cài đặt tài khoản
        </a>
        <form action="{{ route('logout') }}" method="POST" class="mt-4 border-t border-slate-200 dark:border-slate-700/50 pt-2">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors text-left">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Đăng xuất
            </button>
        </form>
    </nav>
</div>
