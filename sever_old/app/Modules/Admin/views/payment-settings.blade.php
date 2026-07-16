@extends('core::layouts.admin')

@section('title', 'Cấu hình Thanh toán')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Thanh toán</span><span class="separator">/</span><span>Cấu hình</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cấu hình Thanh toán (SePay)</h1>
            <p class="page-subtitle">Tích hợp nhận thông báo chuyển khoản tự động</p>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <span class="card-title">Thông tin Tích hợp Webhook</span>
        </div>
        
        <form action="{{ route('admin.settings.payment') }}" method="POST" style="padding: 24px;">
            @csrf
            
            <div class="form-group mb-4">
                <label for="sepay_name" style="display:block; margin-bottom: 8px; font-weight: 600;">Tên Đơn vị (Hiển thị Admin)</label>
                <input type="text" id="sepay_name" name="sepay_name" class="form-control" value="{{ $paymentConfig['sepay_name'] ?? 'Tùng Trân' }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
            </div>

            <div class="form-group mb-4">
                <label for="sepay_client_id" style="display:block; margin-bottom: 8px; font-weight: 600;">Mã Đơn vị (Client ID / API Key)</label>
                <input type="text" id="sepay_client_id" name="sepay_client_id" class="form-control" value="{{ $paymentConfig['sepay_client_id'] ?? 'SP-TEST-TTB75B38' }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">Ví dụ: SP-TEST-TTB75B38</small>
            </div>

            <div class="form-group mb-4">
                <label for="sepay_secret_key" style="display:block; margin-bottom: 8px; font-weight: 600;">Secret Key (Xác thực Webhook)</label>
                <input type="text" id="sepay_secret_key" name="sepay_secret_key" class="form-control" value="{{ $paymentConfig['sepay_secret_key'] ?? 'spsk_test_cCAXsNHwBUjNLrSCoAPh75NFiJ7u3w7T' }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">Sử dụng để đối chiếu Header <code>Authorization: Apikey</code> từ SePay gọi sang hệ thống nhằm tránh fake request.</small>
            </div>

            <div class="form-group mb-5">
                <label style="display:block; margin-bottom: 8px; font-weight: 600;">URL Webhook của bạn (Điền vào SePay)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" class="form-control" value="{{ url('/api/webhook/bank-transfer') }}" readonly style="width: 100%; padding: 10px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 6px; color: #475569;">
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: bold; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    LƯU CẤU HÌNH THANH TOÁN
                </button>
            </div>
        </form>
    </div>
@endsection
