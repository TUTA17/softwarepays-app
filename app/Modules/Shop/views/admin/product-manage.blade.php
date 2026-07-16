@extends('core::layouts.admin')

@section('title', $pageTitle)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>{{ $breadcrumbLabel }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">{{ $pageTitle }} <span class="page-badge">{{ strtoupper($breadcrumbLabel) }}</span></h1></div>
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

    @isset($marginRouteName)
    <!-- Tỉ lệ lợi nhuận -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px;">percent</span>Tỉ lệ lợi nhuận</h3>
        </div>
        <div class="card-body">
            <form action="{{ route($marginRouteName) }}" method="POST" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Tỉ lệ lợi nhuận (%)</label>
                    <input type="number" step="0.1" min="0" max="1000" name="margin" value="{{ old('margin', $profitMargin) }}" placeholder="Mặc định 20%"
                           class="form-control" style="width: 200px; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-surface);">
                </div>
                <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 20px;">Lưu</button>
                <p style="margin: 0; font-size: 12px; color: var(--text-muted); flex-basis: 100%;">Hệ thống lấy giá gốc từ API cộng thêm % này để làm giá bán. Chỉ áp dụng khi chạy lệnh đồng bộ tiếp theo, không đổi ngay giá đang hiển thị.</p>
            </form>
        </div>
    </div>
    @endisset

    @isset($exchangeRateRouteName)
    <!-- Tỷ giá quy đổi -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px;">currency_exchange</span>Tỷ giá quy đổi USD → VNĐ</h3>
        </div>
        <div class="card-body">
            <form action="{{ route($exchangeRateRouteName) }}" method="POST" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">1 USD = ? VNĐ</label>
                    <input type="number" step="1" min="1" name="usd_rate" value="{{ old('usd_rate', $exchangeRate) }}" placeholder="Mặc định 25.000"
                           class="form-control" style="width: 200px; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-surface);">
                </div>
                <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 20px;">Lưu</button>
                <p style="margin: 0; font-size: 12px; color: var(--text-muted); flex-basis: 100%;">Nhà cung cấp VPN trả giá gói bằng USD, hệ thống quy đổi ra VNĐ theo tỷ giá này khi tính giá bán. Chỉ áp dụng khi chạy lệnh đồng bộ tiếp theo.</p>
            </form>
        </div>
    </div>
    @endisset

    <!-- Danh sách sản phẩm & Ảnh -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Upload ảnh mới</th>
                        @isset($packagesRouteName)
                        <th>Mệnh giá / Chiết khấu</th>
                        @endisset
                        @isset($videoRouteName)
                        <th>Video YouTube (link đã xác minh)</th>
                        @endisset
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div style="width: 56px; height: 56px; border-radius: 8px; overflow: hidden; background: var(--bg-surface); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                                @if($product->header_image)
                                    <img src="{{ $product->header_image }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span class="material-symbols-outlined" style="color: var(--text-muted);">image</span>
                                @endif
                            </div>
                        </td>
                        <td style="font-weight: 500;">{{ $product->name }}</td>
                        <td style="min-width: 320px;">
                            <form action="{{ route($uploadRouteName, $product->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px; align-items: center;">
                                @csrf
                                <input type="file" name="header_image" accept="image/png,image/jpeg,image/webp" required
                                       style="flex: 1; font-size: 12px; border: 1px solid var(--border-color); border-radius: 6px; padding: 6px;">
                                <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 12px; white-space: nowrap;">Upload</button>
                            </form>
                        </td>
                        @isset($packagesRouteName)
                        <td>
                            <a href="{{ route($packagesRouteName, $product->id) }}" class="btn btn-secondary" style="padding: 6px 14px; font-size: 12px; white-space: nowrap;">
                                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 14px;">percent</span> Sửa chiết khấu
                            </a>
                        </td>
                        @endisset
                        @isset($videoRouteName)
                        <td style="min-width: 280px;">
                            <form action="{{ route($videoRouteName, $product->id) }}" method="POST" style="display: flex; gap: 8px; align-items: center;">
                                @csrf
                                <input type="url" name="video_url" placeholder="https://youtube.com/watch?v=..."
                                       value="{{ old('video_url', data_get($product->steam_data, 'videos.0.embed_url') ? 'https://youtube.com/watch?v=' . basename(data_get($product->steam_data, 'videos.0.embed_url')) : '') }}"
                                       style="flex: 1; font-size: 12px; border: 1px solid var(--border-color); border-radius: 6px; padding: 6px;">
                                <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 12px; white-space: nowrap;">Lưu</button>
                            </form>
                        </td>
                        @endisset
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có sản phẩm nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $products->links() }}
        </div>
        @endif
    </div>
@endsection
