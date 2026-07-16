@extends('core::layouts.admin')
@section('title', 'Nhân viên')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Nhân viên</span>
@endsection
@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Đăng nhập & Tài khoản <span class="page-badge">ADMIN</span></h1></div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined" style="font-size:18px;">person_add</span> Thêm nhân viên
        </a>
    </div>
    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Tên</th><th>SĐT</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th style="width:80px;"><span class="material-symbols-outlined" style="font-size:18px;">settings</span></th></tr></thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr>
                        <td style="font-weight:600;">{{ $admin->name }}</td>
                        <td style="font-size:13px;color:var(--primary);font-weight:500;">{{ $admin->phone ?? '—' }}</td>
                        <td style="font-size:13px;">{{ $admin->email ?? '—' }}</td>
                        <td>@foreach($admin->roles as $role)<span class="badge badge-info" style="margin-right:4px;">{{ $role->name }}</span>@endforeach</td>
                        <td><span class="badge {{ $admin->status ? 'badge-success' : 'badge-danger' }}">{{ $admin->status ? 'Hoạt động' : 'Khóa' }}</span></td>
                        <td>
                            <div style="display:flex;gap:4px;">
                                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-outline btn-icon btn-sm"><span class="material-symbols-outlined" style="font-size:16px;">edit</span></a>
                                @if($admin->id != session('admin_id'))
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin->id) }}" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')
                                    <button class="btn btn-outline btn-icon btn-sm" style="color:var(--danger);"><span class="material-symbols-outlined" style="font-size:16px;">delete</span></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có nhân viên</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
