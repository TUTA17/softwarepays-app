@extends('core::layouts.admin')

@section('title', 'Quản lý Giao dịch')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Giao dịch</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Lịch sử Giao dịch <span class="page-badge">TRANSACTIONS</span></h1></div>
    </div>

    <!-- Bảng Danh sách Giao dịch -->
    <div class="card">
        <div style="padding: 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <form action="{{ route('admin.transactions') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
                <div>
                    <label style="font-size: 13px; color: var(--text-muted);">Lọc theo Ngày:</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}" style="padding: 6px 12px; border-radius: 6px;">
                </div>
                <div>
                    <label style="font-size: 13px; color: var(--text-muted);">Lọc theo Tháng:</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}" style="padding: 6px 12px; border-radius: 6px;">
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 18px; padding: 7px 16px;">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">filter_list</span> Lọc
                </button>
                @if(request('date') || request('month'))
                    <a href="{{ route('admin.transactions') }}" class="btn btn-light" style="margin-top: 18px; padding: 7px 16px;">Xóa lọc</a>
                @endif
            </form>

            <a href="{{ route('admin.transactions.export', request()->all()) }}" class="btn btn-success" style="background: #16a34a; color: white; display: flex; align-items: center; gap: 5px;">
                <span class="material-symbols-outlined">download</span> Xuất CSV Nạp Tiền
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Thời gian</th>
                        <th>Khách hàng</th>
                        <th>Loại Giao dịch</th>
                        <th>Số tiền (VNĐ)</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Mã tham chiếu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td style="font-weight: 600;">{{ $tx->id }}</td>
                        <td style="font-size: 13px; color: var(--text-muted);">{{ $tx->created_at->format('d/m/Y H:i:s') }}</td>
                        <td style="font-weight: 600; color: var(--primary);">
                            {{ $tx->user ? $tx->user->name : 'N/A' }}
                        </td>
                        <td>
                            @if($tx->type == 'deposit')
                                <span class="badge badge-info">NẠP TIỀN</span>
                            @elseif($tx->type == 'purchase')
                                <span class="badge badge-danger">MUA HÀNG</span>
                            @else
                                <span class="badge badge-muted">{{ strtoupper($tx->type) }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 600; color: {{ $tx->amount > 0 ? '#16a34a' : '#dc2626' }};">
                            @if(($tx->currency ?? 'VND') === 'USD')
                                {{ $tx->amount > 0 ? '+' : '' }}${{ number_format($tx->amount, 2) }}
                            @else
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}đ
                            @endif
                        </td>
                        <td style="font-size: 13px;">{{ $tx->description }}</td>
                        <td>
                            @if($tx->status == 'completed')
                                <span class="badge badge-success">Thành công</span>
                            @elseif($tx->status == 'pending')
                                <span class="badge badge-warning">Chờ xử lý</span>
                            @else
                                <span class="badge badge-danger">Thất bại</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; font-size: 13px;">{{ $tx->reference_id }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có Giao dịch nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
@endsection
