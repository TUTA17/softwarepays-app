@extends('core::layouts.admin')

@section('title', 'Cấu hình Tiếp thị liên kết')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Cài đặt</span><span class="separator">/</span><span>Tiếp thị liên kết</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cấu hình Tiếp thị liên kết (Affiliate)</h1>
            <p class="page-subtitle">Thiết lập hoa hồng và phần thưởng cho người dùng giới thiệu</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding: 16px 24px; background: #f8fafc;">
            <span class="card-title" style="font-size: 18px; font-weight: 700; color: #0f172a;"><i class="fa-solid fa-users" style="margin-right: 8px; color: #2563eb;"></i> Cấu hình Thưởng & Hoa hồng</span>
        </div>
        
        <form action="{{ route('admin.settings.affiliate') }}" method="POST" style="padding: 24px;">
            @csrf
            
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; height: 100%;">
                        <h4 style="margin-bottom: 20px; font-weight: 600; color: #334155; border-bottom: 2px solid #2563eb; display: inline-block; padding-bottom: 8px;">1. Thưởng Đăng Ký</h4>
            
                        <div class="form-group mb-4">
                            <label for="referral_signup_bonus" style="display:block; margin-bottom: 8px; font-weight: 600;">Tiền thưởng khi có người tạo tài khoản thành công (VNĐ)</label>
                            <input type="number" id="referral_signup_bonus" name="referral_signup_bonus" class="form-control" value="{{ $affiliateConfig['referral_signup_bonus'] ?? '500' }}" required min="0" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                            <small style="color: var(--text-muted); display: block; margin-top: 4px;">Ví dụ: 500 (Tức là thưởng 500đ cho người giới thiệu mỗi khi có 1 người đăng ký qua link của họ).</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; height: 100%;">
                        <h4 style="margin-bottom: 20px; font-weight: 600; color: #334155; border-bottom: 2px solid #10b981; display: inline-block; padding-bottom: 8px;">2. Hoa Hồng Mua Hàng</h4>
            
                        <div class="form-group mb-4">
                            <label for="affiliate_commission" style="display:block; margin-bottom: 8px; font-weight: 600;">Phần trăm (%) hoa hồng chia cho người giới thiệu</label>
                            <input type="number" id="affiliate_commission" name="affiliate_commission" class="form-control" value="{{ $affiliateConfig['affiliate_commission'] ?? '5' }}" required min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                            <small style="color: var(--text-muted); display: block; margin-top: 4px;">Ví dụ: 5 (Tức là chia 5% tổng giá trị đơn hàng khi người được giới thiệu mua game).</small>
                        </div>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 15px; font-weight: bold; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 0.2s;">
                    LƯU CẤU HÌNH
                </button>
            </div>
        </form>
    </div>
@endsection
