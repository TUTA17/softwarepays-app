@extends('client::layouts.app')

@section('title', 'KeyGame - Cửa Hàng Game Bản Quyền')

@section('content')
    <!-- Main Hero Banner -->
    <div class="relative overflow-hidden mb-16 pt-8">
        <!-- Glowing Orbs Background -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"></div>
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full mix-blend-screen filter blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="glass-card rounded-3xl p-8 md:p-16 flex flex-col md:flex-row items-center justify-between border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 bg-gradient-to-br from-white/90 to-slate-100/80 dark:from-slate-900/90 dark:to-slate-800/80">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <div class="inline-block px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold text-sm mb-6 flex items-center gap-2 w-max">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                        </span>
                        Kho Game Bản Quyền Lớn Nhất
                    </div>
                    <h1 class="text-4xl md:text-6xl font-display font-bold text-slate-900 dark:text-white mb-6 leading-tight">
                        Trải Nghiệm Game <br /> <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Đỉnh Cao</span>
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-lg mb-8 max-w-xl leading-relaxed">
                        Mua game an toàn, nhận key tự động 24/7. Tích điểm và nhận hoa hồng lên tới 5% khi giới thiệu bạn bè tham gia cộng đồng KeyGame.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#store" class="btn-primary-glow px-8 py-3.5 rounded-xl font-bold text-lg flex items-center gap-3">
                            <i class="fa-solid fa-cart-shopping"></i> Khám Phá Ngay
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center relative">
                    <img src="https://media.rawg.io/media/games/20a/20aa03a10cda45239fe22d035c0ebe64.jpg" alt="Featured Game" class="rounded-2xl shadow-2xl shadow-blue-900/50 w-full max-w-md transform rotate-3 hover:rotate-0 transition-transform duration-500 border border-slate-200 dark:border-slate-200/50 dark:border-slate-700/50 animate-float" style="object-fit: cover; aspect-ratio: 4/5;">
                </div>
            </div>
        </div>
    </div>

    <!-- Cửa Hàng -->
    <div id="store" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-24">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-10 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-fire text-orange-500"></i> Game Đang Bán
                </h2>
                <p class="text-slate-500 dark:text-slate-400">Những tựa game hot nhất hiện nay đang có sẵn key</p>
            </div>
            
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all shadow-sm hover:shadow-lg hover:shadow-blue-500/25">
                    Xem Tất Cả Game <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
                @php 
                    $steam = $product->steam_data;
                    $slug = \Illuminate\Support\Str::slug($product->name);
                @endphp
                <a href="{{ route('product.show', ['id' => $product->id, 'slug' => $slug]) }}" class="glass-card rounded-2xl overflow-hidden group flex flex-col h-full cursor-pointer relative">
                    
                    @if($product->original_price && $product->original_price > $product->price)
                        <div class="absolute top-3 left-3 z-20 bg-rose-500 text-slate-900 dark:text-white text-xs font-bold px-2 py-1 rounded shadow-lg">
                            -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
                        </div>
                    @endif

                    <div class="relative overflow-hidden aspect-[16/9]">
                        @if($product->header_image)
                            <img src="{{ $product->header_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                                <span class="text-slate-600"><i class="fa-solid fa-image fa-3x"></i></span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent opacity-80"></div>
                        
                        <!-- Quick Actions on Hover -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-sm z-10">
                            <span class="btn-primary-glow px-4 py-2 rounded-lg font-semibold text-sm">Xem chi tiết</span>
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow relative z-20 bg-white dark:bg-white/50 dark:bg-slate-900/50 group-hover:bg-white dark:bg-slate-900/80 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-display font-semibold text-lg text-slate-900 dark:text-white leading-tight group-hover:text-blue-400 transition-colors line-clamp-2" title="{{ $product->name }}">{{ $product->name }}</h3>
                        </div>
                        
                        <div class="flex items-center gap-2 mb-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700"><i class="fa-brands fa-steam"></i> STEAM</span>
                            @if($product->available_keys > 0)
                                <span class="text-[10px] text-emerald-400 font-semibold"><i class="fa-solid fa-check-circle"></i> Có sẵn</span>
                            @else
                                <span class="text-[10px] text-rose-400 font-semibold"><i class="fa-solid fa-xmark-circle"></i> Hết hàng</span>
                            @endif
                        </div>
                        
                        <div class="mt-auto pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-between items-end">
                            <div>
                                @if($product->original_price && $product->original_price > $product->price)
                                    <div class="text-xs text-slate-500 line-through mb-0.5">{{ number_format($product->original_price) }}đ</div>
                                @endif
                                <div class="text-xl font-bold text-emerald-400">{{ number_format($product->price) }}đ</div>
                            </div>
                            
                            <button class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 group-hover:bg-blue-600 group-hover:text-slate-900 dark:text-white group-hover:border-blue-500 transition-all flex items-center justify-center shadow-lg transform group-hover:-translate-y-1">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($products->isEmpty())
            <div class="glass-card rounded-2xl p-16 text-center text-slate-500 mt-8">
                <i class="fa-solid fa-ghost text-6xl mb-6 opacity-50"></i>
                <h3 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-2">Chưa có Game nào</h3>
                <p>Hiện chưa có game nào được mở bán trên hệ thống. Quý khách vui lòng quay lại sau.</p>
            </div>
        @endif
    </div>
@endsection
