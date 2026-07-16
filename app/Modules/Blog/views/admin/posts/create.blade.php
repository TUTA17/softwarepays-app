@extends('core::layouts.admin')
@section('title', 'Thêm Bài viết Blog')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><a href="{{ route('admin.blog.posts') }}">Blog</a><span class="separator">/</span><span>Thêm bài</span>
@endsection
@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Viết bài mới</h1></div>
    </div>

    <div class="card" style="padding:24px;">
        <form action="{{ route('admin.blog.posts.store') }}" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Tiêu đề bài viết</label>
                        <input type="text" name="title" required class="form-control" style="width:100%;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Mô tả ngắn</label>
                        <textarea name="summary" class="form-control" style="width:100%; height:80px;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Nội dung chi tiết</label>
                        <textarea name="content" class="form-control" required style="width:100%; height:300px;"></textarea>
                    </div>
                </div>

                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Danh mục</label>
                        <select name="category_id" class="form-control" style="width:100%;">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Link ảnh Thumbnail</label>
                        <input type="text" name="image" class="form-control" style="width:100%;" placeholder="https://...">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:24px; padding:12px; font-size:16px;">🚀 Đăng bài</button>
                </div>
            </div>
        </form>
    </div>
@endsection