@extends('core::layouts.admin')
@section('title', 'Sửa nhân viên')
@section('content')
    <div class="page-header"><h1 class="page-title">Sửa Nhân viên #{{ $admin->id }}</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group"><label class="form-label">Tên *</label><input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required></div>
                <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required></div>
                <div class="form-group"><label class="form-label">Mật khẩu (để trống nếu không đổi)</label><input type="password" name="password" class="form-control"></div>
                <div class="form-group"><label class="form-label">SĐT</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $admin->phone) }}"></div>
                <div class="form-group"><label class="form-label">Vai trò</label>
                    <select name="role_id" class="form-control">
                        <option value="">-- Chọn --</option>
                        @foreach($roles as $r)<option value="{{ $r->id }}" {{ $admin->role_id == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $admin->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ $admin->status == 0 ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </form>
    </div></div>
@endsection
