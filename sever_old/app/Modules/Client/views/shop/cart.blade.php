@extends('client::layouts.app')

@section('title', 'Giỏ hàng của bạn')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Giỏ Hàng Của Bạn</h1>
        <p class="text-slate-500 dark:text-slate-400">Xem lại các tựa game bạn đã chọn và tiến hành thanh toán</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @include('client::partials.user-sidebar')
        </div>

        <!-- Cart Content -->
        <div class="lg:col-span-3">
            @if(count($cart) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cart as $id => $item)
                            <div class="glass-card p-4 rounded-xl flex items-center gap-6 relative group">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-32 h-20 object-cover rounded-lg shadow-md">
                                
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ $item['name'] }}</h3>
                                    <div class="text-emerald-400 font-bold">{{ number_format($item['price']) }}đ</div>
                                </div>

                                <div class="text-slate-500 dark:text-slate-400 text-sm">
                                    Số lượng: <span class="font-bold text-slate-900 dark:text-white">{{ $item['quantity'] }}</span>
                                </div>

                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="ml-4">
                                    @csrf
                                    <button type="submit" class="w-10 h-10 rounded-full bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 flex items-center justify-center transition" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <div class="glass-card p-6 rounded-2xl sticky top-28 shadow-2xl">
                            <h2 class="text-xl font-display font-bold text-slate-900 dark:text-white mb-6 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 pb-4">Tổng Đơn Hàng</h2>
                            
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                                    <span>Tạm tính ({{ count($cart) }} sản phẩm)</span>
                                    <span class="font-bold">{{ number_format($total) }}đ</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                                    <span>Giảm giá</span>
                                    <span>0đ</span>
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <span class="text-lg font-bold text-slate-900 dark:text-white">Tổng tiền</span>
                                    <span class="text-2xl font-bold text-emerald-400">{{ number_format($total) }}đ</span>
                                </div>
                            </div>

                            <a href="{{ route('cart.checkout') }}" class="w-full btn-primary-glow text-slate-900 dark:text-white py-4 rounded-xl text-lg font-bold flex items-center justify-center gap-3">
                                <i class="fa-solid fa-credit-card"></i> TIẾN HÀNH THANH TOÁN
                            </a>
                            
                            <div class="mt-4 text-center">
                                <a href="/" class="text-sm text-blue-500 hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="glass-card p-12 rounded-2xl text-center shadow-xl h-full flex flex-col items-center justify-center min-h-[400px]">
                    <div class="text-slate-300 dark:text-slate-700 mb-6"><i class="fa-solid fa-cart-shopping text-6xl"></i></div>
                    <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-2">Giỏ hàng của bạn đang trống</h2>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Có vẻ như bạn chưa chọn mua bất kỳ tựa game nào!</p>
                    <a href="/" class="inline-flex btn-primary-glow text-slate-900 dark:text-white px-8 py-3 rounded-xl font-bold items-center gap-2">
                        <i class="fa-solid fa-gamepad"></i> VỀ CỬA HÀNG GAME
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
