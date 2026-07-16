@extends('core::layouts.admin')

@section('title', 'Quản lý Mã Giảm Giá')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Mã Giảm Giá</span>
@endsection

@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div><h1 class="page-title">Quản lý Mã Giảm Giá <span class="page-badge">COUPONS</span></h1></div>
    </div>

    @if(session('success'))
        <div style="padding: 12px 20px; background: #ecfdf5; border-left: 4px solid #10b981; color: #047857; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div style="padding: 12px 20px; background: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; border-radius: 4px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;">Thêm Mã Giảm Giá Mới</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Mã (Code):</label>
                        <input type="text" name="code" required class="form-control" placeholder="VD: SUMMER20" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px; text-transform: uppercase;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Loại Giảm Giá:</label>
                        <select name="discount_type" class="form-control" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                            <option value="fixed">Giảm số tiền cố định (VNĐ)</option>
                            <option value="percent">Giảm theo phần trăm (%)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Mức giảm (VNĐ hoặc %):</label>
                        <input type="number" name="discount_value" required min="0" class="form-control" placeholder="VD: 50000 hoặc 10" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Đơn tối thiểu (VNĐ):</label>
                        <input type="number" name="min_order_amount" value="0" min="0" class="form-control" placeholder="Để 0 nếu không giới hạn" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Giảm tối đa (VNĐ):</label>
                        <input type="number" name="max_discount_amount" class="form-control" placeholder="Dùng cho loại %, để trống nếu không giới hạn" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Giới hạn số lượt dùng chung:</label>
                        <input type="number" name="usage_limit" class="form-control" placeholder="Để trống nếu không giới hạn" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Hiệu lực từ:</label>
                        <input type="datetime-local" name="valid_from" class="form-control" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Hiệu lực đến:</label>
                        <input type="datetime-local" name="valid_until" class="form-control" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Mô tả mã (Hiển thị cho User):</label>
                    <input type="text" name="description" class="form-control" placeholder="VD: Giảm 50K cho đơn hàng từ 200K" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>

                <div style="margin-top: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_public" value="1" checked style="width: 16px; height: 16px;">
                        <span style="font-size: 13px; font-weight: 500;">Hiển thị công khai trang Khuyến mãi</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="width: 16px; height: 16px;">
                        <span style="font-size: 13px; font-weight: 500;">Kích hoạt ngay</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #0284c7;">
                        <input type="checkbox" name="send_email" value="1" style="width: 16px; height: 16px;">
                        <span style="font-size: 13px; font-weight: bold;">Gửi Email thông báo mã mới cho Toàn bộ User (Chạy nền chống lag)</span>
                    </label>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">THÊM MÃ GIẢM GIÁ</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;">Danh sách Mã Giảm Giá</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Mã (Code)</th>
                        <th>Loại - Mức giảm</th>
                        <th>Đơn tối thiểu</th>
                        <th>Giới hạn/Đã dùng</th>
                        <th>Công khai</th>
                        <th>Trạng thái</th>
                        <th>Thời hạn</th>
                        <th style="width: 100px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="text-muted">#{{ $coupon->id }}</td>
                            <td><strong style="color: #0284c7; background: #f0f9ff; padding: 4px 8px; border-radius: 4px; border: 1px dashed #38bdf8;">{{ $coupon->code }}</strong></td>
                            <td>
                                @if($coupon->discount_type == 'fixed')
                                    <span style="color: #10b981; font-weight: bold;">-{{ number_format($coupon->discount_value) }}đ</span>
                                @else
                                    <span style="color: #8b5cf6; font-weight: bold;">-{{ $coupon->discount_value }}%</span>
                                    @if($coupon->max_discount_amount)
                                        <div style="font-size: 11px; color: #64748b;">(Tối đa: {{ number_format($coupon->max_discount_amount) }}đ)</div>
                                    @endif
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ number_format($coupon->min_order_amount) }}đ</td>
                            <td>
                                {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td>
                                @if($coupon->is_public)
                                    <span class="badge" style="background-color: #dbeafe; color: #1d4ed8;">Công khai</span>
                                @else
                                    <span class="badge" style="background-color: #f1f5f9; color: #475569;">Ẩn (Code riêng)</span>
                                @endif
                            </td>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge" style="background-color: #dcfce7; color: #15803d;">Hoạt động</span>
                                @else
                                    <span class="badge" style="background-color: #fee2e2; color: #b91c1c;">Tạm khóa</span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">
                                @if($coupon->valid_from)
                                    <div>Từ: {{ $coupon->valid_from->format('d/m/Y H:i') }}</div>
                                @endif
                                @if($coupon->valid_until)
                                    <div style="color: {{ $coupon->valid_until < now() ? '#ef4444' : 'inherit' }}">Đến: {{ $coupon->valid_until->format('d/m/Y H:i') }}</div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5;" title="Xóa">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px; color: var(--text-muted);">Không có mã giảm giá nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 15px; border-top: 1px solid var(--border-color);">
            {{ $coupons->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
