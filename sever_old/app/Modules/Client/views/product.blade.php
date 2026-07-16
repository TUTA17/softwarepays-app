@extends('client::layouts.app')

@section('title', ($product->seo_title ?? $product->name) . ' - Mua Key Bản Quyền Tại KeyGame')
@section('meta_description', $product->seo_description ?? 'Mua key bản quyền game ' . $product->name . ' giá rẻ, tự động giao hàng.')

@push('styles')
<style>
    /* Steam Description Styles */
    .steam-description img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 16px 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .steam-description h2 {
        color: white;
        font-family: 'Outfit', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .steam-description br {
        margin-bottom: 8px;
        display: block;
        content: "";
    }
    .steam-description a {
        color: #60a5fa;
        text-decoration: none;
    }
    .steam-description a:hover {
        text-decoration: underline;
    }
    
    .buy-box {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .dark .buy-box {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
</style>
@endpush

@section('content')
    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
        <!-- Breadcrumb -->
        <div class="text-sm text-slate-500 dark:text-slate-400 mb-6 flex items-center space-x-3 font-medium">
            <a href="/" class="hover:text-blue-500 transition flex items-center gap-2"><i class="fa-solid fa-house text-xs"></i> Trang chủ</a>
            <span><i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i></span>
            <a href="{{ route('shop') }}" class="hover:text-blue-500 transition">Cửa hàng Game</a>
            <span><i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i></span>
            <span class="text-slate-900 dark:text-white">{{ $product->name }}</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 xl:gap-12">
            <!-- Cột trái: Hình ảnh, Giới thiệu -->
            <div class="xl:col-span-2 space-y-8">
                
                <!-- Main Game Image (Cinematic Hero) -->
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 bg-slate-900 group">
                    @if($product->header_image)
                        <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full aspect-[2/1] md:aspect-[21/9] object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full aspect-[2/1] md:aspect-[21/9] flex items-center justify-center text-slate-700">
                            <i class="fa-solid fa-image text-7xl opacity-50"></i>
                        </div>
                    @endif
                    
                    <!-- Gradient Overlay for Contrast -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent opacity-90"></div>
                    
                    <!-- Content inside Hero -->
                    <div class="absolute bottom-0 left-0 w-full p-6 md:p-10">
                        @if($product->genres)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(json_decode($product->genres, true) ?? [] as $genre)
                                    <span class="bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm tracking-wide">{{ mb_strtoupper($genre) }}</span>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Title is strictly white for contrast -->
                        <h1 class="text-3xl md:text-5xl lg:text-6xl font-display font-bold text-white mb-2 drop-shadow-2xl leading-tight">{{ $product->name }}</h1>
                    </div>
                </div>
                
                <!-- About this game -->
                <div class="glass-card p-6 md:p-10 rounded-3xl">
                    <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-gamepad text-blue-500"></i> GIỚI THIỆU TRÒ CHƠI
                    </h2>
                    <div class="text-slate-700 dark:text-slate-300 leading-relaxed text-[15px] steam-description">
                        {!! $product->description ?? '<p class="italic text-slate-500">Chưa có thông tin chi tiết.</p>' !!}
                    </div>
                </div>
            </div>

            <!-- Cột phải: Giá và Mua hàng -->
            <div>
                <!-- Sticky Buy Box -->
                <div class="buy-box p-6 md:p-8 rounded-3xl sticky top-28">
                    <!-- Platform -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-700/50">
                        <h2 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Nền tảng</h2>
                        <span class="px-3 py-1.5 bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 rounded-lg text-xs font-bold shadow-md flex items-center gap-2">
                            <i class="fa-brands fa-steam text-base"></i> STEAM
                        </span>
                    </div>
                    
                    <!-- Price Section -->
                    <div class="mb-8">
                        @if($product->original_price && $product->original_price > $product->price)
                            @php
                                $discount = round((($product->original_price - $product->price) / $product->original_price) * 100);
                            @endphp
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-rose-500 text-white text-sm font-bold px-2 py-1 rounded-md shadow-sm">Giảm -{{ $discount }}%</span>
                                <span class="text-slate-400 line-through font-medium text-lg">{{ number_format($product->original_price) }}đ</span>
                            </div>
                        @endif
                        
                        <div class="text-right">
                            <div class="text-5xl font-display font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-indigo-500 py-2">{{ number_format($product->price) }}<span class="text-2xl ml-1">đ</span></div>
                        </div>
                    </div>
                    
                    <!-- Call to Actions -->
                    <div class="mb-8 space-y-4">
                        @if($availableKeysCount > 0)
                            <div class="flex items-center gap-2 text-emerald-500 text-sm mb-6 font-bold px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl">
                                <i class="fa-solid fa-bolt text-lg"></i> Sản phẩm đang còn hàng (Giao mã tự động)
                            </div>
                            
                            @auth
                                <form action="{{ route('product.buy', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full btn-primary-glow text-white py-4 rounded-xl text-lg font-bold flex items-center justify-center gap-3 transition-all shadow-xl shadow-blue-500/30 group mb-3">
                                        <i class="fa-solid fa-credit-card group-hover:scale-110 transition-transform"></i> MUA NGAY
                                    </button>
                                </form>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 py-3.5 rounded-xl text-base font-bold flex items-center justify-center gap-2 transition-colors border-2 border-slate-200 dark:border-slate-700">
                                        <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center justify-center gap-3 w-full btn-primary-glow text-white py-4 rounded-xl text-lg font-bold transition-all shadow-xl shadow-blue-500/30 group">
                                    <i class="fa-solid fa-right-to-bracket group-hover:-translate-x-1 transition-transform"></i> ĐĂNG NHẬP ĐỂ MUA
                                </a>
                            @endauth
                        @else
                            <div class="flex items-center gap-2 text-rose-500 text-sm mb-6 font-bold px-4 py-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
                                <i class="fa-solid fa-hourglass-half text-lg"></i> Sản phẩm đang tạm hết hàng
                            </div>
                            <button disabled class="w-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 py-4 rounded-xl text-lg font-bold cursor-not-allowed flex items-center justify-center gap-3 border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-ban"></i> ĐÃ BÁN HẾT
                            </button>
                        @endif
                    </div>
                    
                    <!-- Guarantees -->
                    <div class="space-y-4 pt-6 border-t border-slate-200 dark:border-slate-700/50">
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group cursor-default">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 text-blue-500 group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300"><i class="fa-solid fa-shield-check text-lg"></i></div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block text-sm mb-0.5">100% Chính hãng</strong>
                                <span class="text-slate-500 dark:text-slate-400 text-xs">Bảo hành trọn đời từ KeyGame</span>
                            </div>
                        </div>
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group cursor-default">
                            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0 text-amber-500 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300"><i class="fa-solid fa-bolt text-lg"></i></div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block text-sm mb-0.5">Giao Key siêu tốc</strong>
                                <span class="text-slate-500 dark:text-slate-400 text-xs">Mã xuất hiện ngay trên màn hình</span>
                            </div>
                        </div>
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group cursor-default">
                            <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0 text-slate-700 dark:text-slate-300 group-hover:scale-110 group-hover:bg-slate-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-slate-900 transition-all duration-300"><i class="fa-brands fa-steam text-lg"></i></div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block text-sm mb-0.5">Kích hoạt dễ dàng</strong>
                                <span class="text-slate-500 dark:text-slate-400 text-xs">Dùng trực tiếp trên phần mềm Steam</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
