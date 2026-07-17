@extends('core::layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Người dùng</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Quản lý Khách hàng <span class="page-badge">USERS</span></h1></div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

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
                        <th>Số dư (USD)</th>
                        <th>Điểm (Pts)</th>
                        <th>Mã Affiliate</th>
                        <th>Giới thiệu bởi</th>
                        <th>Vai trò</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td style="font-weight: 600;">{{ $user->id }}</td>
                        <td style="font-weight: 600; color: var(--primary);">{{ $user->name }}</td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $user->email }}</td>
                        <td style="font-weight: 600; color: #16a34a;">{{ number_format($user->balance) }}đ</td>
                        <td style="font-weight: 600; color: #10b981;">${{ number_format($user->balance_usd, 2) }}</td>
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
                        <td>
                            <button type="button" class="btn btn-primary" style="padding:6px 12px; font-size:13px;" onclick="document.getElementById('modal-addbalance-{{ $user->id }}').style.display='flex'">+ Cộng tiền</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có Người dùng nào</td></tr>
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

    @foreach($users as $user)
    <div class="modal-overlay" id="modal-addbalance-{{ $user->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center;">
        <div class="modal-content" style="background:#1e1e2d; padding:24px; border-radius:12px; width:400px; border:1px solid #333;">
            <h3 style="margin-bottom:6px; color:#fff;">Cộng tiền thủ công</h3>
            <p style="margin-bottom:16px; color:#aaa; font-size:13px;">Khách hàng: <strong style="color:#fff;">{{ $user->name }}</strong> ({{ $user->email }})<br>Chỉ dùng khi thanh toán tự động bị lỗi và cần bù tiền cho khách.</p>
            <form action="{{ route('admin.users.add_balance', $user->id) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; color:#aaa;">Ví nhận tiền</label>
                    <div style="display:flex; gap:16px;">
                        <label style="display:flex; align-items:center; gap:6px; color:#fff; font-weight:normal;">
                            <input type="radio" name="currency" value="VND" checked onchange="document.getElementById('unit-label-{{ $user->id }}').textContent='VNĐ'; document.getElementById('amount-input-{{ $user->id }}').min='1000'; document.getElementById('amount-input-{{ $user->id }}').step='1000'; document.getElementById('amount-input-{{ $user->id }}').value=''; document.getElementById('amount-input-{{ $user->id }}').placeholder='VD: 100000';"> Ví VNĐ
                        </label>
                        <label style="display:flex; align-items:center; gap:6px; color:#fff; font-weight:normal;">
                            <input type="radio" name="currency" value="USD" onchange="document.getElementById('unit-label-{{ $user->id }}').textContent='USD'; document.getElementById('amount-input-{{ $user->id }}').min='0.01'; document.getElementById('amount-input-{{ $user->id }}').step='0.01'; document.getElementById('amount-input-{{ $user->id }}').value=''; document.getElementById('amount-input-{{ $user->id }}').placeholder='VD: 10.00';"> Ví USD
                        </label>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; color:#aaa;">Số tiền (<span id="unit-label-{{ $user->id }}">VNĐ</span>)</label>
                    <input type="number" id="amount-input-{{ $user->id }}" name="amount" min="1000" step="1000" required placeholder="VD: 100000" style="width:100%; padding:10px; border-radius:6px; border:1px solid #444; background:#2a2a3c; color:#fff;">
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; color:#aaa;">Ghi chú (không bắt buộc)</label>
                    <input type="text" name="note" maxlength="255" placeholder="VD: Bù giao dịch PayPal/SePay lỗi mã GD #12345" style="width:100%; padding:10px; border-radius:6px; border:1px solid #444; background:#2a2a3c; color:#fff;">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn" style="background:#444; color:#fff;" onclick="document.getElementById('modal-addbalance-{{ $user->id }}').style.display='none'">Hủy</button>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Xác nhận cộng tiền vào tài khoản của {{ $user->name }}?')">Xác nhận cộng tiền</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
