@extends('core::layouts.admin')

@section('title', 'Danh mục Sound Meme')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><a href="{{ route('admin.soundmeme.sounds') }}">Sound Meme</a><span class="separator">/</span><span>Danh mục</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Danh mục Sound Meme <span class="page-badge">CATEGORIES</span></h1></div>
    </div>

    @if(session('success'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #ecfdf5; border-color: #a7f3d0; color: #047857;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #fef2f2; border-color: #fecaca; color: #b91c1c;">{{ session('error') }}</div>
    @endif

    <div class="card" style="margin-bottom: 24px; padding: 20px;">
        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 700;">Thêm danh mục mới</h3>
        <form action="{{ route('admin.soundmeme.categories.store') }}" method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 180px;">
                <label style="display:block; font-size:12px; margin-bottom:6px;">Tên danh mục</label>
                <input type="text" name="name" required class="form-control" style="width:100%;">
            </div>
            <div style="flex: 2; min-width: 220px;">
                <label style="display:block; font-size:12px; margin-bottom:6px;">Mô tả</label>
                <input type="text" name="description" class="form-control" style="width:100%;">
            </div>
            <div style="width: 90px;">
                <label style="display:block; font-size:12px; margin-bottom:6px;">Thứ tự</label>
                <input type="number" name="order" value="0" class="form-control" style="width:100%;">
            </div>
            <button type="submit" class="btn btn-primary">Thêm</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Số sound</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td style="font-weight:600;">{{ $cat->name }}</td>
                        <td style="font-size:13px; color:var(--text-muted);">{{ $cat->description }}</td>
                        <td>{{ $cat->sounds_count }}</td>
                        <td>
                            @if($cat->status)
                                <span class="badge badge-success">Bật</span>
                            @else
                                <span class="badge badge-muted">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.soundmeme.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Xoá danh mục này?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color:#b91c1c;">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có danh mục nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
