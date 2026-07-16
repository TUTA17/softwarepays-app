@extends('core::layouts.admin')

@section('title', 'Cấu hình SMM Panel')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>SMM Panel</span><span class="separator">/</span><span>Cấu hình</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cấu hình SMM Panel (Like.vn)</h1>
            <p class="page-subtitle">Tích hợp dịch vụ mạng xã hội vào website</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #d1fae5; color: #065f46; border: 1px solid #10b981;">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <span class="card-title">Kết nối API SMM</span>
        </div>
        
        <form action="{{ route('admin.settings.smm.store') }}" method="POST" style="padding: 24px;">
            @csrf
            
            <div class="form-group mb-4">
                <label for="smm_api_url" style="display:block; margin-bottom: 8px; font-weight: 600;">API URL</label>
                <input type="text" id="smm_api_url" name="smm_api_url" class="form-control" value="{{ old('smm_api_url', \App\Modules\Core\Models\Setting::getValue('smm_api_tab_api_url', 'https://like.vn/api/v2')) }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 6px;">
                <small style="color: #64748b; margin-top: 5px; display: block;">Mặc định: https://like.vn/api/v2</small>
            </div>

            <div class="form-group mb-4">
                <label for="smm_api_token" style="display:block; margin-bottom: 8px; font-weight: 600;">API Token</label>
                <input type="text" id="smm_api_token" name="smm_api_token" class="form-control" value="{{ old('smm_api_token', \App\Modules\Core\Models\Setting::getValue('smm_api_tab_api_token')) }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 6px;">
                <small style="color: #64748b; margin-top: 5px; display: block;">Chuỗi Token bí mật lấy từ trang quản lý của Like.vn</small>
            </div>

            <div class="form-group mb-4">
                <label for="smm_profit_margin" style="display:block; margin-bottom: 8px; font-weight: 600;">Tỉ lệ lợi nhuận (%)</label>
                <div style="display: flex; align-items: center;">
                    <input type="number" step="0.1" id="smm_profit_margin" name="smm_profit_margin" class="form-control" value="{{ old('smm_profit_margin', \App\Modules\Core\Models\Setting::getValue('smm_api_tab_profit_margin', '50')) }}" required style="width: 200px; padding: 10px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 6px 0 0 6px;">
                    <span style="background-color: #f1f5f9; padding: 10px 15px; border: 1px solid var(--border-color, #e2e8f0); border-left: none; border-radius: 0 6px 6px 0; color: #475569; font-weight: 600;">%</span>
                </div>
                <small style="color: #64748b; margin-top: 5px; display: block;">Ví dụ: Nhập 50. Nếu giá API gốc là 10.000đ -> Web sẽ bán cho khách với giá 15.000đ.</small>
            </div>

            <div style="margin-top: 30px; border-top: 1px solid var(--border-color, #e2e8f0); padding-top: 20px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">
                    <i class="flaticon2-check-mark"></i> Lưu Cấu Hình
                </button>
            </div>
        </form>
    </div>
@endsection
