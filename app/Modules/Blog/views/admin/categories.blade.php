@extends('core::layouts.admin')
@section('title', 'Danh mục Blog')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Blog</span><span class="separator">/</span><span>Danh mục</span>
@endsection
@section('content')
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div><h1 class="page-title">Quản lý Danh mục <span class="page-badge">BLOG</span></h1></div>
        <button class="btn btn-primary" onclick="document.getElementById('modal-add').style.display='flex'">+ Thêm Danh mục</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tên Danh mục</th>
                        <th>Slug</th>
                        <th>Số bài viết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td style="font-weight: 600;">{{ $cat->id }}</td>
                        <td style="font-weight: 600; color: var(--primary);">{{ $cat->name }}</td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $cat->slug }}</td>
                        <td>{{ $cat->posts()->count() }}</td>
                        <td>-</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có danh mục</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="modal-add" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center;">
        <div class="modal-content" style="background:#1e1e2d; padding:24px; border-radius:12px; width:400px; border:1px solid #333;">
            <h3 style="margin-bottom:16px; color:#fff;">Thêm Danh mục mới</h3>
            <form action="{{ route('admin.blog.categories.store') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; color:#aaa;">Tên Danh mục</label>
                    <input type="text" name="name" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #444; background:#2a2a3c; color:#fff;">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn" style="background:#444; color:#fff;" onclick="document.getElementById('modal-add').style.display='none'">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
@endsection