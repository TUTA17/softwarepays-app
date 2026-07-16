@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white text-xl shadow-lg">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white">Lịch Sử Giao Dịch</h1>
            <p class="text-slate-500 mt-1">Lịch sử nạp tiền, mua game và nhận thưởng</p>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="p-0">
            @if(count($transactions) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm">
                                <th class="p-4 font-medium">Mã GD</th>
                                <th class="p-4 font-medium">Loại</th>
                                <th class="p-4 font-medium">Mô tả</th>
                                <th class="p-4 font-medium text-right">Số tiền</th>
                                <th class="p-4 font-medium">Ngày giờ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                                    <td class="p-4 font-mono text-sm text-slate-500">
                                        {{ $tx->reference_id ?? 'TX' . $tx->id }}
                                    </td>
                                    <td class="p-4">
                                        @if($tx->type == 'deposit')
                                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold uppercase">Nạp tiền</span>
                                        @elseif($tx->type == 'purchase')
                                            <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 text-xs font-bold uppercase">Mua game</span>
                                        @else
                                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-bold uppercase">Thưởng</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-700 dark:text-slate-300">
                                        {{ $tx->description }}
                                    </td>
                                    <td class="p-4 text-right font-bold {{ $tx->amount > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}đ
                                    </td>
                                    <td class="p-4 text-slate-500 text-sm">
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Phân trang -->
                <div class="p-6 border-t border-slate-200 dark:border-slate-800">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-4xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Chưa có giao dịch nào</h3>
                    <p class="text-slate-500 mb-6 max-w-md mx-auto">Bạn chưa phát sinh bất kỳ giao dịch nào trên hệ thống.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
