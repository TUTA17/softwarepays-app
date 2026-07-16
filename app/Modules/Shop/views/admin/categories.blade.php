@extends('core::layouts.admin')

@section('title', 'Quản lý Danh mục')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Quản lý Danh mục</span>
@endsection

@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div><h1 class="page-title">Danh sách Thể loại <span class="page-badge">CATEGORIES</span></h1></div>
        <form action="{{ route('admin.categories.sync') }}" method="POST" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<span class=\'material-symbols-outlined\' style=\'font-size: 18px; animation: spin 2s linear infinite;\'>sync</span> Đang đồng bộ...'; return confirm('Quá trình đồng bộ sẽ quét lại tất cả các Thể loại hiện có của các game. Bạn có muốn tiếp tục?');">
            @csrf
            <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 5px; background-color: #0284c7; border-color: #0284c7;">
                <span class="material-symbols-outlined" style="font-size: 18px;">sync</span> Đồng bộ danh mục
            </button>
        </form>
    </div>
    
    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px; padding: 15px; background-color: #d1fae5; color: #065f46; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Thêm Danh Mục -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">Thêm Danh Mục Mới</div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
                @csrf
                <div style="flex: 1;">
                    <label class="form-label">Tên Danh Mục</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên (ví dụ: Hành Động)" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Thêm Mới</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng Danh Mục -->
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Slug</th>
                        <th>Số lượng Game</th>
                        <th>Trạng thái</th>
                        <th width="120" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>#{{ $category->id }}</td>
                        <td>
                            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" style="display: flex; gap: 10px;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" style="padding: 4px 8px;">
                                <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} title="Kích hoạt">
                                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                            </form>
                        </td>
                        <td><span style="font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px;">{{ $category->slug }}</span></td>
                        <td><span class="page-badge">{{ $category->products_count }} game</span></td>
                        <td>
                            @if($category->is_active)
                                <span style="color: #16a34a; font-weight: bold; font-size: 12px;">HIỂN THỊ</span>
                            @else
                                <span style="color: #dc2626; font-weight: bold; font-size: 12px;">ẨN</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Việc xóa sẽ gỡ nó khỏi tất cả các game.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">Chưa có danh mục nào. Hãy bấm "Đồng bộ danh mục" ở góc trên bên phải.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="card-body" style="border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
@endsection
