@extends('core::layouts.admin')

@section('title', 'Tải Sound Lên')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><a href="{{ route('admin.soundmeme.sounds') }}">Sound Meme</a><span class="separator">/</span><span>Tải lên</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Tải Sound Lên</h1></div>
    </div>

    @if ($errors->any())
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #fef2f2; border-color: #fecaca; color: #b91c1c;">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="padding:24px;">
        <form action="{{ route('admin.soundmeme.sounds.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Tiêu đề</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="form-control" style="width:100%;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control" style="width:100%;">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Tags (cách nhau bởi dấu phẩy)</label>
                        <input type="text" name="tags" value="{{ old('tags') }}" placeholder="hài, troll, meme..." class="form-control" style="width:100%;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">File âm thanh (mp3, ogg, wav, webm, m4a — tối đa {{ config('sound.max_upload_mb') }}MB)</label>
                        <input type="file" name="audio" accept="audio/*" required class="form-control" style="width:100%;">
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
                        <label style="display:block; margin-bottom:8px;">Trạng thái</label>
                        <select name="status" class="form-control" style="width:100%;">
                            <option value="draft">Nháp</option>
                            <option value="published">Đăng ngay</option>
                            <option value="hidden">Ẩn</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:8px;">Ảnh thumbnail (không bắt buộc)</label>
                        <input type="file" name="thumbnail" accept="image/*" class="form-control" style="width:100%;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1">
                        <label for="is_featured" style="margin:0;">Đánh dấu nổi bật</label>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">upload</span> Tải lên
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
