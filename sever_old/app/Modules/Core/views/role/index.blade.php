@extends('core::layouts.admin')
@section('title', 'Phân quyền')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Phân quyền</span>
@endsection
@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Phân quyền <span class="page-badge">ROLE</span></h1></div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined" style="font-size:18px;">add</span> Thêm vai trò
        </a>
    </div>
    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Tên</th><th>Slug</th><th>Quyền</th><th style="width:80px;"><span class="material-symbols-outlined" style="font-size:18px;">settings</span></th></tr></thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td style="font-weight:600;">{{ $role->name }}</td>
                        <td style="font-size:13px;color:var(--text-muted);">{{ $role->slug }}</td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                @foreach($role->permissions->take(5) as $perm)
                                    <span class="badge badge-muted" style="font-size:10px;">{{ $perm->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 5)
                                    <span class="badge badge-info" style="font-size:10px;">+{{ $role->permissions->count() - 5 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-outline btn-icon btn-sm"><span class="material-symbols-outlined" style="font-size:16px;">edit</span></a>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" onsubmit="return confirm('Xóa vai trò?')">@csrf @method('DELETE')
                                    <button class="btn btn-outline btn-icon btn-sm" style="color:var(--danger);"><span class="material-symbols-outlined" style="font-size:16px;">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có vai trò</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
