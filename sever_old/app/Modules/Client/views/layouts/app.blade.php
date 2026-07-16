@php
    $sysSettings = \App\Modules\Core\Models\Setting::getAllGrouped();
    $genSettings = $sysSettings['general_tab'] ?? [];
    $siteName = $genSettings['name'] ?? 'KEYGAME';
    $siteLogo = $genSettings['logo'] ?? null;
    $siteFavicon = $genSettings['favicon'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteName . ' - Mua Game Bản Quyền Giá Rẻ')</title>
    <meta name="description" content="@yield('meta_description', 'Mua key bản quyền game giá rẻ, tự động giao hàng.')">
    @if($siteFavicon)
    <link rel="icon" type="image/png" href="{{ asset($siteFavicon) }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        gaming: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                            accent: '#3b82f6',
                            hover: '#60a5fa',
                            steam: '#171a21',
                            steamLight: '#2a475e',
                            green: '#4c6b22',
                            greenLight: '#a4d007',
                        }
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        glow: {
                            'from': { boxShadow: '0 0 10px #3b82f6' },
                            'to': { boxShadow: '0 0 20px #60a5fa, 0 0 30px #3b82f6' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .dark body {
            background-color: #0b0f19;
            color: #e2e8f0;
        }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dark .glass-nav {
            background: rgba(15, 23, 42, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }
        
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 0 15px rgba(59, 130, 246, 0.1);
        }
        .dark .glass-card:hover {
            background: rgba(30, 41, 59, 0.8);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 0 15px rgba(59, 130, 246, 0.1);
        }
        
        .nav-link {
            position: relative;
            color: #475569;
            transition: color 0.2s ease;
        }
        .dark .nav-link {
            color: #cbd5e1;
        }
        
        .nav-link:hover, .dark .nav-link:hover {
            color: #3b82f6;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }

        .btn-primary-glow {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
        }
        
        .btn-primary-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
        }

        .btn-steam {
            background: linear-gradient(to right, #4c6b22 5%, #3d561b 95%);
            color: #d2efa9;
            transition: all 0.3s ease;
        }
        
        .btn-steam:hover {
            background: linear-gradient(to right, #8ed629 5%, #6aa31a 95%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(142, 214, 41, 0.3);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
    <script>
        // Check local storage for theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
    @stack('styles')
</head>
<body class="antialiased min-h-screen flex flex-col">

    <!-- Header / Navbar -->
    <header class="glass-nav sticky top-0 z-50 w-full transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        @if($siteLogo)
                            <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" class="h-10 object-contain group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-slate-900 dark:text-white shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all duration-300 transform group-hover:scale-105 group-hover:rotate-3">
                                <i class="fa-solid fa-gamepad text-xl"></i>
                            </div>
                            <span class="font-display font-bold text-2xl tracking-wide text-slate-900 dark:text-white group-hover:text-blue-400 transition-colors">{{ mb_strtoupper($siteName) }}</span>
                        @endif
                    </a>
                    
                    <!-- Main Menu -->
                    <nav class="hidden md:flex ml-10 space-x-8">
                        <a href="{{ route('home') }}" class="nav-link font-medium text-sm uppercase tracking-wider {{ request()->routeIs('home') ? 'text-slate-900 dark:text-white' : '' }}">Trang Chủ</a>
                        <a href="{{ route('shop') }}" class="nav-link font-medium text-sm uppercase tracking-wider {{ request()->routeIs('shop') ? 'text-slate-900 dark:text-white' : '' }}">Cửa Hàng</a>
                        <a href="javascript:void(0)" onclick="alert('Tính năng Khuyến Mãi đang được phát triển!')" class="nav-link font-medium text-sm uppercase tracking-wider">Khuyến Mãi</a>
                        <a href="javascript:void(0)" onclick="alert('Tính năng Hỗ Trợ đang được phát triển!')" class="nav-link font-medium text-sm uppercase tracking-wider">Hỗ Trợ</a>
                    </nav>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center space-x-3 sm:space-x-5">
                    <!-- Cart -->
                    <div class="relative group">
                        <a href="{{ route('cart.index') }}" class="relative w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" aria-label="Giỏ hàng">
                            <i class="fa-solid fa-cart-shopping"></i>
                            @if(session()->has('cart') && count(session('cart')) > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-slate-900">
                                    {{ count(session('cart')) }}
                                </span>
                            @endif
                        </a>
                        
                        <!-- Mini Cart Dropdown -->
                        <div class="absolute right-0 mt-0 pt-2 w-80 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right scale-95 group-hover:scale-100 z-50">
                            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                                    <h4 class="font-bold text-slate-900 dark:text-white">Giỏ hàng của bạn</h4>
                                    @if(session()->has('cart') && count(session('cart')) > 0)
                                        <span class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-1 rounded-md">{{ count(session('cart')) }} sản phẩm</span>
                                    @endif
                                </div>
                                <div class="p-2 max-h-[320px] overflow-y-auto">
                                    @if(session()->has('cart') && count(session('cart')) > 0)
                                        @foreach(array_slice(session('cart'), 0, 3, true) as $id => $item)
                                            <a href="{{ route('cart.index') }}" class="flex items-center gap-3 p-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors mb-1">
                                                <img src="{{ $item['image'] }}" alt="Cover" class="w-12 h-12 object-cover rounded-md shadow-sm">
                                                <div class="flex-1 min-w-0">
                                                    <h5 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item['name'] }}</h5>
                                                    <div class="text-xs text-slate-500 mt-1">Số lượng: {{ $item['quantity'] }}</div>
                                                </div>
                                                <div class="text-sm font-bold text-emerald-500">{{ number_format($item['price']) }}đ</div>
                                            </a>
                                        @endforeach
                                        @if(count(session('cart')) > 3)
                                            <div class="text-center py-2 text-xs text-slate-500 dark:text-slate-400 font-medium border-t border-slate-100 dark:border-slate-800 mt-2">
                                                <i class="fa-solid fa-plus text-[10px]"></i> {{ count(session('cart')) - 3 }} sản phẩm khác...
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-8">
                                            <i class="fa-solid fa-cart-arrow-down text-4xl text-slate-200 dark:text-slate-700 mb-3"></i>
                                            <p class="text-sm font-medium text-slate-500">Chưa có sản phẩm nào</p>
                                        </div>
                                    @endif
                                </div>
                                @if(session()->has('cart') && count(session('cart')) > 0)
                                    <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                        <a href="{{ route('cart.index') }}" class="flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-600/20">
                                            Xem Tất Cả <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" aria-label="Toggle Theme">
                        <i class="fa-solid fa-moon dark:hidden"></i>
                        <i class="fa-solid fa-sun hidden dark:block"></i>
                    </button>
                    @auth
                        <div class="relative group">
                            <div class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-slate-50 dark:bg-slate-50/50 dark:bg-slate-800/50 transition-colors">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-blue-400 font-semibold"><i class="fa-solid fa-wallet mr-1"></i> {{ number_format(Auth::user()->balance) }}đ</div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-700 to-slate-600 flex items-center justify-center text-slate-900 dark:text-white font-bold shadow-inner border border-slate-500 overflow-hidden">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:text-white transition-colors"></i>
                            </div>
                            
                            <!-- Dropdown -->
                            <div class="absolute right-0 mt-2 w-64 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right scale-95 group-hover:scale-100 z-50">
                                <div class="p-3 border-b border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 sm:hidden">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-blue-400 font-semibold mt-1"><i class="fa-solid fa-wallet mr-1"></i> {{ number_format(Auth::user()->balance) }}đ</div>
                                </div>
                                <div class="p-2 border-b border-slate-200 dark:border-slate-700/50">
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-blue-500/10 hover:border-blue-500/20 border border-transparent transition-all">
                                        <i class="fa-solid fa-gamepad w-5 text-center text-blue-500"></i> Kho Game Của Tôi
                                    </a>
                                    <a href="{{ route('wallet.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-emerald-500/10 hover:border-emerald-500/20 border border-transparent transition-all mt-1">
                                        <i class="fa-solid fa-wallet w-5 text-center text-emerald-500"></i> Nạp Tiền
                                    </a>
                                </div>
                                <div class="p-2 border-b border-slate-200 dark:border-slate-700/50">
                                    <a href="{{ route('profile.transactions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-amber-500/10 hover:border-amber-500/20 border border-transparent transition-all">
                                        <i class="fa-solid fa-clock-rotate-left w-5 text-center text-amber-500"></i> Lịch Sử Giao Dịch
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white hover:bg-slate-500/10 hover:border-slate-500/20 border border-transparent transition-all mt-1">
                                        <i class="fa-solid fa-user-gear w-5 text-center text-slate-500"></i> Cài Đặt Tài Khoản
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-rose-400 hover:text-rose-500 hover:bg-rose-500/10 hover:border-rose-500/20 border border-transparent transition-all font-semibold">
                                            <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:block text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white font-medium text-sm transition-colors">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn-primary-glow px-6 py-2.5 rounded-lg font-semibold text-sm">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-white/50 dark:bg-slate-900/50 pt-16 pb-8 mt-20 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(circle at 50% 0%, rgba(59, 130, 246, 0.05) 0%, transparent 70%);"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 mb-6">
                        @if($siteLogo)
                            <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" class="h-10 object-contain hover:scale-105 transition-transform">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-slate-900 dark:text-white">
                                <i class="fa-solid fa-gamepad"></i>
                            </div>
                            <span class="font-display font-bold text-xl text-slate-900 dark:text-white">{{ mb_strtoupper($siteName) }}</span>
                        @endif
                    </a>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        Nền tảng cung cấp bản quyền game chính hãng giá tốt nhất thị trường. Hệ thống giao nhận tự động 24/7.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <a href="/" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-600 hover:text-slate-900 dark:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="/" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-400 hover:text-slate-900 dark:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-twitter"></i></a>
                        <a href="/" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-indigo-600 hover:text-slate-900 dark:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-discord"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-display font-semibold text-slate-900 dark:text-white text-lg mb-6">Sản phẩm</h3>
                    <ul class="space-y-3">
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Game Steam</a></li>
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Origin & EA</a></li>
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Battle.net</a></li>
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Epic Games</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-display font-semibold text-slate-900 dark:text-white text-lg mb-6">Hỗ trợ khách hàng</h3>
                    <ul class="space-y-3">
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Hướng dẫn mua hàng</a></li>
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Hướng dẫn kích hoạt</a></li>
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Chính sách bảo hành</a></li>
                        <li><a href="/" class="text-slate-500 dark:text-slate-400 hover:text-blue-400 text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-display font-semibold text-slate-900 dark:text-white text-lg mb-6">Thanh toán</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Chúng tôi hỗ trợ các hình thức thanh toán an toàn và tiện lợi nhất.</p>
                    <div class="flex flex-wrap gap-2">
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">MoMo</div>
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">VNPay</div>
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">Visa / Master</div>
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">Bank Transfer</div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-200 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} {{ $siteName }}. Tất cả các quyền được bảo lưu.
                </div>
                <div class="flex space-x-6 text-sm">
                    <a href="/" class="text-slate-500 hover:text-slate-700 dark:text-slate-300 transition-colors">Điều khoản dịch vụ</a>
                    <a href="/" class="text-slate-500 hover:text-slate-700 dark:text-slate-300 transition-colors">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
