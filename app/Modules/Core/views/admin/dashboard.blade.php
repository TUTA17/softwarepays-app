@extends('core::layouts.admin')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <span>Dashboard Thống Kê</span>
@endsection

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title">Dashboard Tổng Quan</h1>
        <p class="page-subtitle" style="color: #64748b; font-size: 14px; margin-top: 4px;">Xem báo cáo doanh thu và người dùng</p>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <!-- Card Doanh Thu -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 16px; border-left: 4px solid #10b981;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #d1fae5; display: flex; align-items: center; justify-content: center; color: #10b981;">
            <span class="material-symbols-outlined">payments</span>
        </div>
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase;">Tổng Doanh Thu</div>
            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{!! \App\Helpers\CurrencyHelper::formatPrice($stats['total_revenue']) !!}</div>
        </div>
    </div>

    <!-- Card Users -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 16px; border-left: 4px solid #3b82f6;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
            <span class="material-symbols-outlined">group</span>
        </div>
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase;">Khách Hàng</div>
            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ number_format($stats['total_users']) }}</div>
        </div>
    </div>

    <!-- Card Keys Sold -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 16px; border-left: 4px solid #f59e0b;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #f59e0b;">
            <span class="material-symbols-outlined">key</span>
        </div>
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase;">Số Key Đã Bán</div>
            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ number_format($stats['total_keys_sold']) }}</div>
        </div>
    </div>

    <!-- Card Total Balance -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 16px; border-left: 4px solid #8b5cf6;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #ede9fe; display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
            <span class="material-symbols-outlined">account_balance_wallet</span>
        </div>
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase;">Số Dư Khách Hàng</div>
            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{!! \App\Helpers\CurrencyHelper::formatPrice($stats['total_balance']) !!}</div>
        </div>
    </div>
</div>

<!-- Biểu đồ & Top Game -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
    
    <!-- Chart -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">Biểu đồ Doanh Thu (30 Ngày)</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    <!-- Top Games -->
    <div style="background: white; border-radius: 12px; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">🔥 Top 5 Game Bán Chạy</h3>
        </div>
        <div style="padding: 0;">
            @foreach($topGames as $game)
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #f1f5f9;">
                <img src="{{ $game->header_image }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                <div style="flex: 1; overflow: hidden;">
                    <div style="font-size: 14px; font-weight: 600; color: #334155; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ $game->name }}</div>
                    <div style="font-size: 12px; color: #64748b;">Đã bán: <b style="color: #10b981;">{{ $game->sold_count }}</b> key</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Top Khách hàng & Giao dịch mới -->
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; margin-bottom: 24px;">

    <!-- Top Users -->
    <div style="background: white; border-radius: 12px; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">💎 Khách Nạp Nhiều Nhất</h3>
        </div>
        <div>
            @foreach($topUsers as $index => $u)
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #f1f5f9;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $index == 0 ? '#fef08a' : ($index == 1 ? '#e2e8f0' : '#fed7aa') }}; color: #854d0e; font-weight: bold; display: flex; align-items: center; justify-content: center;">
                    #{{ $index + 1 }}
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 14px; font-weight: 600; color: #334155;">{{ $u->name }}</div>
                    <div style="font-size: 12px; color: #64748b;">{{ $u->email }}</div>
                </div>
                <div style="font-weight: 700; color: #3b82f6;">
                    {!! \App\Helpers\CurrencyHelper::formatPrice($u->total_deposit ?? 0) !!}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Giao dịch gần đây -->
    <div style="background: white; border-radius: 12px; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">⏳ Giao Dịch Gần Đây</h3>
        </div>
        <div class="table-responsive" style="padding: 10px;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 10px; font-size: 12px; color: #64748b;">THỜI GIAN</th>
                        <th style="padding: 10px; font-size: 12px; color: #64748b;">KHÁCH HÀNG</th>
                        <th style="padding: 10px; font-size: 12px; color: #64748b;">LOẠI</th>
                        <th style="padding: 10px; font-size: 12px; color: #64748b; text-align: right;">SỐ TIỀN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_transactions as $trans)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px; font-size: 13px; color: #64748b;">{{ $trans->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 10px; font-size: 13px; font-weight: 500; color: #334155;">{{ $trans->user->name ?? 'Unknown' }}</td>
                        <td style="padding: 10px;">
                            @if($trans->type == 'deposit')
                                <span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Nạp tiền</span>
                            @elseif($trans->type == 'purchase')
                                <span style="background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Mua Game</span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Khác</span>
                            @endif
                        </td>
                        <td style="padding: 10px; font-size: 14px; font-weight: 700; text-align: right; color: {{ $trans->amount > 0 ? '#10b981' : '#ef4444' }};">
                            {{ $trans->amount > 0 ? '+' : '' }}{!! \App\Helpers\CurrencyHelper::formatPrice($trans->amount) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');   
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: {!! json_encode($chartData) !!},
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            if(value >= 1000000) return (value / 1000000) + 'Tr';
                            if(value >= 1000) return (value / 1000) + 'K';
                            return value;
                        },
                        font: { size: 11, family: 'Inter' }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { size: 11, family: 'Inter' } }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
</script>
@endsection
