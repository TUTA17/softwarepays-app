@extends('core::layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Người dùng</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Quản lý Khách hàng <span class="page-badge">USERS</span></h1></div>
    </div>

    <!-- Bảng Danh sách Người dùng -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tên Khách hàng</th>
                        <th>Email</th>
                        <th>Số dư (VNĐ)</th>
                        <th>Điểm (Pts)</th>
                        <th>Mã Affiliate</th>
                        <th>Giới thiệu bởi</th>
                        <th>Vai trò</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td style="font-weight: 600;">{{ $user->id }}</td>
                        <td style="font-weight: 600; color: var(--primary);">{{ $user->name }}</td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $user->email }}</td>
                        <td style="font-weight: 600; color: #16a34a;">{{ number_format($user->balance) }}đ</td>
                        <td style="font-weight: 600; color: #2563eb;">{{ number_format($user->points) }}</td>
                        <td style="font-family: monospace; font-size: 13px;">{{ $user->affiliate_code }}</td>
                        <td style="font-size: 13px;">{{ $user->referred_by ? 'ID: ' . $user->referred_by : '-' }}</td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge badge-danger">ADMIN</span>
                            @else
                                <span class="badge badge-muted">USER</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có Người dùng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $users->links() }}
        </div>
        @endif
    </div>
@endsection
