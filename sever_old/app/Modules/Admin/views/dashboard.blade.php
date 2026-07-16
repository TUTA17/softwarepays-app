@extends('core::layouts.admin')

@section('title', 'Tổng quan hệ thống')

@section('breadcrumb')
    <span>Dashboard</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Tổng quan hệ thống Game Shop</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="stat-label">Tổng Khách hàng</div>
                <div class="stat-value" style="color: #2563eb;">{{ number_format($stats['total_users']) }}</div>
            </div>
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb;">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Doanh thu bán Game</div>
                <div class="stat-value" style="color: #16a34a; font-size: 22px;">{{ number_format($stats['total_revenue']) }}đ</div>
            </div>
            <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Key đã bán ra</div>
                <div class="stat-value" style="color: #d97706;">{{ number_format($stats['total_keys_sold']) }}</div>
            </div>
            <div class="stat-icon" style="background:#fefce8;color:#d97706;">
                <span class="material-symbols-outlined">vpn_key</span>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Số dư User (Nợ)</div>
                <div class="stat-value" style="color: #dc2626; font-size: 22px;">{{ number_format($stats['total_balance']) }}đ</div>
            </div>
            <div class="stat-icon" style="background:#fef2f2;color:#dc2626;">
                <span class="material-symbols-outlined">account_balance_wallet</span>
            </div>
        </div>
    </div>

    <!-- Bảng Giao dịch gần nhất -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <span class="card-title"><span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 5px;">history</span> Giao dịch gần nhất</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Loại Giao dịch</th>
                        <th>Số tiền</th>
                        <th>Mô tả</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_transactions as $tx)
                    <tr>
                        <td style="font-weight: 600;">{{ $tx->user->name }}</td>
                        <td>
                            @if($tx->type == 'deposit')
                                <span class="badge badge-info">NẠP TIỀN</span>
                            @elseif($tx->type == 'purchase')
                                <span class="badge badge-danger">MUA HÀNG</span>
                            @else
                                <span class="badge badge-success">HOA HỒNG</span>
                            @endif
                        </td>
                        <td style="font-weight: 600; color: {{ $tx->amount > 0 ? '#16a34a' : '#dc2626' }};">
                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}đ
                        </td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $tx->description }}</td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $tx->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có giao dịch nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
