@extends('core::layouts.admin')

@section('title', 'Chiết khấu - ' . $product->name)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span>
    <a href="{{ route('admin.card') }}">Thẻ Nạp</a><span class="separator">/</span><span>{{ $product->name }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Chiết khấu cho khách <span class="page-badge">{{ strtoupper($product->name) }}</span></h1></div>
        <a href="{{ route('admin.card') }}" class="btn btn-secondary">
            <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 16px;">arrow_back</span> Quay lại
        </a>
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

    <div class="card">
        <div class="card-body" style="padding: 0 0 8px;">
            <p style="padding: 16px 18px 0; margin: 0; font-size: 13px; color: var(--text-muted);">
                Đặt % giảm giá riêng cho từng mệnh giá — chỉ áp dụng cho khách mua, không ảnh hưởng tới % lợi nhuận chung ở trang danh sách. Để trống hoặc 0 nghĩa là không giảm giá.
            </p>
        </div>
        <form action="{{ route('admin.card.packages.update', $product->id) }}" method="POST">
            @csrf
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mệnh giá (giá gốc)</th>
                            <th>% Giảm giá cho khách</th>
                            <th>Giá bán hiện tại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $pkg)
                        <tr>
                            <td style="font-weight: 500;">{{ number_format($pkg->face_value) }}đ</td>
                            <td>
                                <input type="number" step="0.1" min="0" max="90" name="discount[{{ $pkg->id }}]"
                                       value="{{ old('discount.' . $pkg->id, $pkg->promo_discount_percent) }}" placeholder="0"
                                       class="form-control" style="width: 100px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-surface);">
                            </td>
                            <td style="font-weight: 600; color: var(--primary-color, #2563eb);">{{ number_format($pkg->price) }}đ</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có mệnh giá nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px 18px; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-primary" style="padding: 8px 24px;">Lưu chiết khấu</button>
            </div>
        </form>
    </div>
@endsection
