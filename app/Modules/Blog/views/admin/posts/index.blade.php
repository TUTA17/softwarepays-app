@extends('core::layouts.admin')
@section('title', 'Bài viết Blog')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Blog</span><span class="separator">/</span><span>Bài viết</span>
@endsection
@section('content')
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div><h1 class="page-title">Quản lý Bài viết <span class="page-badge">BLOG</span></h1></div>
        <div style="display:flex; gap:8px;">
            <form action="{{ route('admin.blog.posts.sync') }}" method="POST" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<span class=\'material-symbols-outlined\' style=\'font-size: 18px; animation: spin 2s linear infinite;\'>sync</span> Đang đồng bộ...'; return confirm('Lấy tin tức mới nhất từ GameHub (tối đa 4 bài/lần)?');">
                @csrf
                <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 5px; background-color: #0284c7; border-color: #0284c7;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">sync</span> Đồng bộ tin tức
                </button>
            </form>
            <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">+ Viết bài mới</a>
        </div>
    </div>
    <p style="margin: 0 0 16px; font-size: 12px; color: var(--text-muted);">Tin tức được cấu hình tự động đồng bộ mỗi 3 tiếng qua cron ngoài. Nút "Đồng bộ tin tức" ở trên dùng để chạy tay ngay lập tức, không cần đợi.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="margin-bottom:16px; padding:16px;">
        <form method="GET" style="display:flex; gap:16px;">
            <select name="is_auto" class="form-control" style="width:200px;">
                <option value="">Tất cả nguồn</option>
                <option value="1" {{ request('is_auto') == '1' ? 'selected' : '' }}>Bài tự động (Bot)</option>
                <option value="0" {{ request('is_auto') == '0' ? 'selected' : '' }}>Bài viết tay</option>
            </select>
            <button class="btn btn-primary">Lọc</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Ngày đăng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td style="font-weight: 600;">{{ $post->id }}</td>
                        <td>
                            <div style="font-weight: 600; color: var(--primary);">{{ $post->title }}</div>
                            @if($post->is_auto)
                                <span style="font-size:11px; padding:2px 6px; background:#475569; border-radius:4px;">BOT CÀO</span>
                            @endif
                        </td>
                        <td>{{ $post->category ? $post->category->name : 'Không có' }}</td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $post->author }}</td>
                        <td style="font-size: 13px;">{{ $post->pub_date->format('d/m/Y H:i') }}</td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <a href="{{ route('admin.blog.posts.edit', $post->id) }}" class="btn" style="padding:4px 8px; font-size:12px; background:#2563eb; color:#fff;">Sửa</a>
                                <form action="{{ route('admin.blog.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Chắc chắn xóa?')">
                                    @csrf @method('DELETE')
                                    <button class="btn" style="padding:4px 8px; font-size:12px; background:#dc2626; color:#fff; border:none; cursor:pointer;">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có bài viết</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
@endsection