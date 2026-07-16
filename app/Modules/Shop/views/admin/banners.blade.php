@extends('core::layouts.admin')

@section('title', 'Quản lý Banner')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Banner Trang Chủ</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Quản lý Banner Trang Chủ <span class="page-badge">SLIDE</span></h1></div>
    </div>

    @if(session('success'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #ecfdf5; border-color: #a7f3d0; color: #047857;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #fef2f2; border-color: #fecaca; color: #b91c1c;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Thêm banner mới -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px;">add_photo_alternate</span>Thêm banner mới</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Ảnh cho Ngôn ngữ khác (mặc định, dùng chung mọi ngôn ngữ trừ Tiếng Việt nếu chưa bật ảnh riêng)</label>
                        <input type="file" name="image_intl" accept="image/png,image/jpeg,image/webp" required
                               style="font-size: 12px; border: 1px solid var(--border-color); border-radius: 6px; padding: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Ảnh riêng cho Tiếng Việt (tùy chọn)</label>
                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp"
                               style="font-size: 12px; border: 1px solid var(--border-color); border-radius: 6px; padding: 6px;">
                    </div>
                </div>
                <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Link khi bấm vào</label>
                        <input type="url" name="link_url" placeholder="https://softwarepays.com/..." required
                               class="form-control" style="width: 320px; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-surface);">
                    </div>
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; padding-bottom: 8px;">
                        <input type="checkbox" name="show_vi_image" value="1"> Dùng ảnh riêng cho Tiếng Việt
                    </label>
                    <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 20px;">Thêm</button>
                </div>
                <p style="margin: 8px 0 0; font-size: 12px; color: var(--text-muted);">Ảnh nên có tỉ lệ ngang rộng (khoảng 21:7) để hiển thị đẹp trên banner trang chủ. Nhiều banner sẽ tự động chạy dạng slide, chuyển ảnh mỗi 5 giây. Nếu không bật "Dùng ảnh riêng cho Tiếng Việt", khách Tiếng Việt cũng thấy ảnh "Ngôn ngữ khác".</p>
            </form>
        </div>
    </div>

    <!-- Danh sách banner -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ảnh Ngôn ngữ khác</th>
                        <th>Ảnh Tiếng Việt</th>
                        <th>Link</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr>
                        <td>
                            <img src="{{ $banner->image_intl ?: $banner->image }}" alt="Banner intl {{ $banner->id }}" style="width: 120px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                        </td>
                        <td>
                            @if($banner->image)
                                <img src="{{ $banner->image }}" alt="Banner vi {{ $banner->id }}" style="width: 120px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); {{ $banner->show_vi_image ? '' : 'opacity: 0.4;' }}">
                            @else
                                <span style="font-size: 11px; color: var(--text-muted);">Chưa có</span>
                            @endif
                        </td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $banner->link_url }}">{{ $banner->link_url }}</td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            <form action="{{ route('admin.banners.toggle_active', $banner->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn" style="padding: 4px 12px; font-size: 12px; border-radius: 20px; border: none; cursor: pointer; {{ $banner->is_active ? 'background:#ecfdf5;color:#047857;' : 'background:#fef2f2;color:#b91c1c;' }}">
                                    {{ $banner->is_active ? 'Đang bật' : 'Đang tắt' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <details>
                                <summary class="btn btn-secondary" style="display: inline-block; padding: 5px 10px; font-size: 11px; cursor: pointer;">Sửa</summary>
                                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 10px; padding: 12px; background: var(--bg-surface); border-radius: 8px; display: flex; flex-direction: column; gap: 8px; min-width: 260px;">
                                    @csrf
                                    @method('PUT')
                                    <label style="font-size: 11px; font-weight: 500;">Link
                                        <input type="url" name="link_url" value="{{ $banner->link_url }}" required
                                               style="width: 100%; font-size: 11px; padding: 5px 8px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-body, #fff);">
                                    </label>
                                    <label style="font-size: 11px; font-weight: 500;">Thứ tự
                                        <input type="number" name="sort_order" value="{{ $banner->sort_order }}" min="0"
                                               style="width: 100%; font-size: 11px; padding: 5px 8px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-body, #fff);">
                                    </label>
                                    <label style="font-size: 11px; font-weight: 500;">Ảnh Ngôn ngữ khác (để trống nếu không đổi)
                                        <input type="file" name="image_intl" accept="image/png,image/jpeg,image/webp" style="width: 100%; font-size: 10px;">
                                    </label>
                                    <label style="font-size: 11px; font-weight: 500;">Ảnh Tiếng Việt (để trống nếu không đổi)
                                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" style="width: 100%; font-size: 10px;">
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; font-size: 11px;">
                                        <input type="checkbox" name="show_vi_image" value="1" {{ $banner->show_vi_image ? 'checked' : '' }}> Dùng ảnh riêng cho Tiếng Việt
                                    </label>
                                    <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 11px;">Lưu</button>
                                </form>
                            </details>
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Xóa banner này?');" style="margin-top: 6px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 11px; background: #fef2f2; color: #b91c1c; border: none; border-radius: 6px; cursor: pointer;">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có banner nào — trang chủ sẽ hiện banner mặc định.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
