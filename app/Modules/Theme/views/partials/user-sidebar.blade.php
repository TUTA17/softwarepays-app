<!-- User Profile Card -->
<div class="glass-card p-6 rounded-2xl relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
    <div class="flex items-center gap-4 mb-6">
        @if(Auth::user()->avatar)
            <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-14 h-14 rounded-full border-2 border-blue-500 shadow-lg object-cover">
        @else
            <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-slate-700 to-slate-600 border-2 border-slate-500 flex items-center justify-center text-xl text-white font-bold shadow-lg">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        @endif
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</p>
        </div>
    </div>
    
    <div class="space-y-4">
        <div class="bg-white dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50">
            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider font-semibold">{{ __('sidebar.wallet_balance') }}</div>
            <div class="text-xl font-display font-bold text-blue-400">{!! \App\Helpers\CurrencyHelper::formatWalletBalance(Auth::user()->balance) !!}</div>
        </div>
        <div class="bg-white dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center">
            <div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider font-semibold">{{ __('sidebar.reward_points') }}</div>
                <div class="text-lg font-bold text-emerald-400">{{ number_format(Auth::user()->points) }} pts</div>
            </div>
            <i class="fa-solid fa-gift text-2xl text-emerald-500/20"></i>
        </div>
    </div>

    <a href="{{ route('wallet.show') }}" class="mt-6 flex items-center justify-center gap-2 w-full btn-primary-glow text-slate-900 dark:text-white py-3 rounded-xl font-semibold transition-all shadow-lg group">
        <i class="fa-solid fa-wallet group-hover:scale-110 transition-transform"></i> {{ __('wallet.deposit_page_title') }}
    </a>
</div>

<!-- Navigation Menu -->
<div class="glass-card p-4 rounded-2xl mt-6">
    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors' }}">
            <i class="fa-solid fa-layer-group w-5 text-center"></i> {{ __('dashboard.title') }}
        </a>
        <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('cart.index') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors' }}">
            <i class="fa-solid fa-cart-shopping w-5 text-center"></i> {{ __('header.cart') }}
            @if(session()->has('cart') && count(session('cart')) > 0)
                <span class="ml-auto bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count(session('cart')) }}</span>
            @endif
        </a>
        <a href="{{ route('wallet.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('wallet.show') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-50 dark:bg-slate-800 transition-colors' }}">
            <i class="fa-solid fa-money-bill-wave w-5 text-center"></i> {{ __('sidebar.topup_history') }}
        </a>
        <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('profile.settings') ? 'bg-slate-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400' : '' }}">
            <i class="fa-solid fa-user-gear w-5"></i>
            {{ __('sidebar.account_settings') }}
        </a>

        <a href="{{ route('coupons.my') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('coupons.my') ? 'bg-slate-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400' : '' }}">
            <i class="fa-solid fa-ticket-simple w-5"></i>
            {{ __('sidebar.my_coupons') }}
        </a>

        <a href="{{ route('referrals.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 {{ request()->routeIs('referrals.*') ? 'bg-slate-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400' : '' }}">
            <i class="fa-solid fa-users w-5"></i>
            {{ __('sidebar.referral_friends') }}
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-4 border-t border-slate-200 dark:border-slate-700/50 pt-2">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors text-left">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> {{ __('header.logout') }}
            </button>
        </form>
    </nav>
</div>
