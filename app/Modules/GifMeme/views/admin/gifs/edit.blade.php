@extends('core::layouts.admin')

@section('title', 'Sửa Gif')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><a href="{{ route('admin.gifmeme.gifs') }}">GIF World</a><span class="separator">/</span><span>Sửa</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Sửa Gif</h1></div>
    </div>

    @if ($errors->any())
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #fef2f2; border-color: #fecaca; color: #b91c1c;">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="margin-bottom:20px; padding:20px;">
        <label style="display:block; margin-bottom:8px; font-size:13px; color:var(--text-muted);">Nghe thử ({{ $Gif->original_filename }} &middot; {{ number_format($Gif->file_size / 1024, 0) }} KB)</label>
        <image controls src="{{ $playUrl }}" style="width:100%;"></image>
    </div>

    <div class="card" style="padding:24px;">
        <form action="{{ route('admin.gifmeme.gifs.update', $Gif->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Tiêu đề</label>
                        <input type="text" name="title" value="{{ old('title', $Gif->title) }}" required class="form-control" style="width:100%;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control" style="width:100%;">{{ old('description', $Gif->description) }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Tags (cách nhau bởi dấu phẩy)</label>
                        <input type="text" name="tags" value="{{ old('tags', $Gif->tags) }}" class="form-control" style="width:100%;">
                    </div>
                </div>
                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Danh mục</label>
                        <select name="category_id" class="form-control" style="width:100%;">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $Gif->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Trạng thái</label>
                        <select name="status" class="form-control" style="width:100%;">
                            <option value="draft" {{ $Gif->status === 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ $Gif->status === 'published' ? 'selected' : '' }}>Đã đăng</option>
                            <option value="hidden" {{ $Gif->status === 'hidden' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Thay ảnh thumbnail (không bắt buộc)</label>
                        <input type="file" name="thumbnail" accept="image/*" class="form-control" style="width:100%;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ $Gif->is_featured ? 'checked' : '' }}>
                        <label for="is_featured" style="margin:0;">Đánh dấu nổi bật</label>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
@endsection


