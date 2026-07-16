@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Kho Game Của Tôi</h1>
        <p class="text-slate-500 dark:text-slate-400">Quản lý các tựa game và Key bản quyền bạn đã mua</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @include('partials.user-sidebar')
        </div>

        <!-- Dashboard Content -->
        <div class="lg:col-span-3 space-y-8">
            <!-- Thống kê nhanh -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 text-blue-500/10 group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-layer-group text-9xl"></i>
                    </div>
                    <div class="relative">
                        <p class="text-slate-500 dark:text-slate-400 font-medium mb-1">Tổng Số Game</p>
                        <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($totalGames) }}</div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 text-emerald-500/10 group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-money-bill-wave text-9xl"></i>
                    </div>
                    <div class="relative">
                        <p class="text-slate-500 dark:text-slate-400 font-medium mb-1">Đã Chi Tiêu</p>
                        <div class="text-3xl font-bold text-emerald-500">{{ number_format($totalSpent) }}đ</div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 text-amber-500/10 group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-star text-9xl"></i>
                    </div>
                    <div class="relative">
                        <p class="text-slate-500 dark:text-slate-400 font-medium mb-1">Điểm Tích Lũy</p>
                        <div class="text-3xl font-bold text-amber-500">{{ number_format(Auth::user()->points) }}</div>
                    </div>
                </div>
            </div>

            <!-- Danh sách Key Game -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                        <i class="fa-solid fa-key text-blue-500 mr-2"></i> Key Game Của Bạn
                    </h2>
                    <a href="{{ route('shop') }}" class="text-sm text-blue-500 hover:text-blue-600 font-medium">Mua thêm <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                
                <div class="p-0">
                    @if(count($gameKeys) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm">
                                        <th class="p-4 font-medium">Game</th>
                                        <th class="p-4 font-medium">Mã Key</th>
                                        <th class="p-4 font-medium">Ngày mua</th>
                                        <th class="p-4 font-medium text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @foreach($gameKeys as $item)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                                            <td class="p-4">
                                                <div class="flex items-center gap-3">
                                                    @if($item->product && $item->product->header_image)
                                                        <img src="{{ $item->product->header_image }}" alt="Thumbnail" class="w-16 h-9 object-cover rounded shadow-sm">
                                                    @else
                                                        <div class="w-16 h-9 bg-slate-200 dark:bg-slate-700 rounded flex items-center justify-center text-slate-400"><i class="fa-solid fa-image"></i></div>
                                                    @endif
                                                    <span class="font-bold text-slate-900 dark:text-white">
                                                        {{ $item->product ? $item->product->name : 'Sản phẩm không xác định' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <div class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 font-mono text-sm text-slate-700 dark:text-slate-300">
                                                    <span id="key-{{ $item->id }}">{{ $item->key_code ?? $item->key }}</span>
                                                    <button onclick="copyKey('key-{{ $item->id }}')" class="text-blue-500 hover:text-blue-700 p-1" title="Copy Key">
                                                        <i class="fa-regular fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="p-4 text-slate-500 text-sm">
                                                {{ \Carbon\Carbon::parse($item->sold_at)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="p-4 text-right">
                                                @if($item->product && $item->product->steam_app_id)
                                                <a href="steam://install/{{ $item->product->steam_app_id }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors text-sm font-medium">
                                                    <i class="fa-brands fa-steam"></i> Cài đặt
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-slate-200 dark:border-slate-800">
                            {{ $gameKeys->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-4xl">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Kho game trống!</h3>
                            <p class="text-slate-500 mb-6 max-w-md mx-auto">Bạn chưa mua tựa game nào. Hãy khám phá hàng ngàn game đỉnh cao đang có giá cực hời tại Cửa Hàng.</p>
                            <a href="{{ route('shop') }}" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">Khám Phá Ngay</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyKey(elementId) {
    var copyText = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(copyText).then(function() {
        alert("Đã copy mã Key: " + copyText);
    }, function(err) {
        console.error('Không thể copy text: ', err);
    });
}
</script>
@endsection
