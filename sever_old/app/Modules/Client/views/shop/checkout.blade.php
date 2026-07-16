@extends('client::layouts.app')

@section('title', 'Xác nhận thanh toán')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-4">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Thanh Toán Đơn Hàng</h1>
        <p class="text-slate-500 mt-1">Vui lòng kiểm tra lại thông tin đơn hàng trước khi chốt hạ</p>
    </div>

    <!-- Error/Success Messages -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cột Trái: Thông tin & Thanh toán -->
        <div class="w-full lg:w-3/5 space-y-6">
            
            <!-- Box 1: Thông tin người mua -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-500"></i> Thông tin mua hàng
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tên tài khoản</label>
                        <input type="text" readonly value="{{ auth()->user()->name }}" class="w-full bg-slate-100 dark:bg-slate-700 border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-600 dark:text-slate-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email nhận Key (Tự động)</label>
                        <input type="text" readonly value="{{ auth()->user()->email }}" class="w-full bg-slate-100 dark:bg-slate-700 border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-600 dark:text-slate-400 cursor-not-allowed">
                        <p class="text-xs text-slate-500 mt-1">Hệ thống sẽ gửi thông báo và mã Key về email này.</p>
                    </div>
                </div>
            </div>

            <!-- Box 2: Phương thức thanh toán -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-blue-500"></i> Phương thức thanh toán
                </h2>
                
                <div class="space-y-3">
                    <!-- Option 1: Ví KeyGame (Mặc định) -->
                    <label class="relative flex cursor-pointer rounded-lg border border-blue-500 bg-blue-50/50 dark:bg-blue-500/10 p-4 shadow-sm focus:outline-none">
                        <input type="radio" name="payment_method" value="wallet" class="sr-only" checked>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-slate-900 dark:text-white">Thanh toán qua Ví KeyGame</span>
                                <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">
                                    Số dư hiện tại: <strong class="ml-1 text-green-600 dark:text-green-400">{{ number_format(auth()->user()->balance) }}đ</strong>
                                </span>
                            </span>
                        </span>
                        <i class="fa-solid fa-circle-check text-blue-600 dark:text-blue-400 text-xl"></i>
                    </label>
                </div>
            </div>

        </div>

        <!-- Cột Phải: Hóa đơn & Đặt hàng -->
        <div class="w-full lg:w-2/5">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 sticky top-24">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Chi tiết đơn hàng</h2>
                
                <!-- Danh sách sản phẩm -->
                <div class="flow-root mb-6">
                    <ul role="list" class="-my-4 divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($cart as $id => $details)
                        <li class="flex items-center py-4">
                            <div class="h-16 w-24 flex-shrink-0 overflow-hidden rounded-md border border-slate-200 dark:border-slate-700">
                                <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" class="h-full w-full object-cover object-center">
                            </div>
                            <div class="ml-4 flex flex-1 flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium text-slate-900 dark:text-white">
                                        <h3 class="line-clamp-2 text-sm">{{ $details['name'] }}</h3>
                                        <p class="ml-4 text-sm whitespace-nowrap text-green-600">{{ number_format($details['price']) }}đ</p>
                                    </div>
                                </div>
                                <div class="flex flex-1 items-end justify-between text-sm">
                                    <p class="text-slate-500 dark:text-slate-400">SL: {{ $details['quantity'] }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Box Khuyến Mãi -->
                <div class="border-t border-slate-200 dark:border-slate-700 py-4 mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mã giảm giá</label>
                    <div class="flex gap-2">
                        <input type="text" placeholder="Nhập mã (nếu có)" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <button type="button" class="bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">Áp dụng</button>
                    </div>
                </div>

                <!-- Tổng tiền -->
                <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600 dark:text-slate-400">Tạm tính</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ number_format($total) }}đ</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600 dark:text-slate-400">Giảm giá</p>
                        <p class="text-sm font-medium text-red-500">0đ</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600 dark:text-slate-400">Phí VAT (5%)</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ number_format($vat) }}đ</p>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700">
                        <p class="text-lg font-bold text-slate-900 dark:text-white">Tổng cộng</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($final_total) }}đ</p>
                    </div>
                </div>

                <!-- Nút Đặt Hàng -->
                <form action="{{ route('cart.checkout.process') }}" method="POST" class="mt-6">
                    @csrf
                    @if(auth()->user()->balance >= $final_total)
                        <button type="submit" class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-xl text-lg font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1">
                            <i class="fa-solid fa-credit-card"></i>
                            Thanh Toán Bằng Ví
                        </button>
                    @else
                        <div class="p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg mb-3">
                            <p class="text-sm text-red-600 dark:text-red-400 text-center">Số dư ví không đủ để thanh toán.</p>
                        </div>
                        <a href="{{ route('wallet.show') }}" class="w-full flex justify-center items-center gap-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-6 py-4 rounded-xl text-lg font-bold transition">
                            <i class="fa-solid fa-wallet"></i>
                            Nạp Thêm Tiền
                        </a>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
