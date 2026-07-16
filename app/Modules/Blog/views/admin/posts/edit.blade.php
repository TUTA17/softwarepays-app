@extends('core::layouts.admin')
@section('title', 'Sửa Bài viết Blog')
@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Sửa bài: {{ $post->title }}</h1></div>
    </div>

    <div class="card" style="padding:24px;">
        <form action="{{ route('admin.blog.posts.update', $post->id) }}" method="POST">
            @csrf @method('PUT')
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Tiêu đề bài viết</label>
                        <input type="text" name="title" value="{{ $post->title }}" required class="form-control" style="width:100%;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Mô tả ngắn</label>
                        <textarea name="summary" class="form-control" style="width:100%; height:80px;">{{ $post->summary }}</textarea>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Nội dung chi tiết</label>
                        <textarea name="content" class="form-control" required style="width:100%; height:300px;">{{ $post->content }}</textarea>
                    </div>
                </div>

                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Danh mục</label>
                        <select name="category_id" class="form-control" style="width:100%;">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $post->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Link ảnh Thumbnail</label>
                        <input type="text" name="image" value="{{ $post->image }}" class="form-control" style="width:100%;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:24px; padding:12px; font-size:16px;">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
@endsection