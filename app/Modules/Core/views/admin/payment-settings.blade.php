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

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding: 16px 24px; background: #f8fafc;">
            <span class="card-title" style="font-size: 18px; font-weight: 700; color: #0f172a;"><i class="fa-solid fa-building-columns" style="margin-right: 8px; color: #2563eb;"></i> Cấu hình Chuyển khoản Thủ công & Tự động</span>
        </div>
        
        <form action="{{ route('admin.settings.payment') }}" method="POST" style="padding: 24px;">
            @csrf
            
            <div class="row">
                <!-- Cột trái: Thông tin VietQR -->
                <div class="col-lg-6 mb-4">
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; height: 100%;">
                        <h4 style="margin-bottom: 20px; font-weight: 600; color: #334155; border-bottom: 2px solid #2563eb; display: inline-block; padding-bottom: 8px;">1. Thông tin tài khoản nhận tiền (VietQR)</h4>
            
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label for="bank_id" style="display:block; margin-bottom: 8px; font-weight: 600;">Ngân hàng</label>
                    <select id="bank_id" name="bank_id" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
                        @php
                            $popularBanks = [
                                'VCB' => 'Vietcombank',
                                'MB' => 'MBBank',
                                'TCB' => 'Techcombank',
                                'TPB' => 'TPBank',
                                'VPB' => 'VPBank',
                                'BIDV' => 'BIDV',
                                'CTG' => 'VietinBank',
                                'ACB' => 'ACB',
                                'VIB' => 'VIB',
                                'HDB' => 'HDBank',
                                'SHB' => 'SHB',
                                'STB' => 'Sacombank',
                                'OJB' => 'OceanBank',
                                'ABB' => 'ABBank',
                                'SCB' => 'SCB'
                            ];
                            $currentBank = $paymentConfig['bank_id'] ?? 'TPB';
                        @endphp
                        @foreach($popularBanks as $code => $name)
                            <option value="{{ $code }}" {{ $currentBank == $code ? 'selected' : '' }}>{{ $name }} ({{ $code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label for="account_no" style="display:block; margin-bottom: 8px; font-weight: 600;">Số Tài Khoản</label>
                    <input type="text" id="account_no" name="account_no" class="form-control" value="{{ $paymentConfig['account_no'] ?? '0123456789' }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="account_name" style="display:block; margin-bottom: 8px; font-weight: 600;">Tên Chủ Tài Khoản (Viết Hoa, Không Dấu)</label>
                <input type="text" id="account_name" name="account_name" class="form-control" value="{{ $paymentConfig['account_name'] ?? 'NGUYEN VAN A' }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
            </div>

                        <div class="form-group mb-0">
                            <label for="payment_notice" style="display:block; margin-bottom: 8px; font-weight: 600;">Lưu ý hiển thị ở trang Nạp Tiền</label>
                            <textarea id="payment_notice" name="payment_notice" class="form-control" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">{{ $paymentConfig['payment_notice'] ?? 'Hệ thống tự động cộng tiền trong vòng 1-3 phút.' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Webhook SePay -->
                <div class="col-lg-6 mb-4">
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; height: 100%;">
                        <h4 style="margin-bottom: 20px; font-weight: 600; color: #334155; border-bottom: 2px solid #10b981; display: inline-block; padding-bottom: 8px;">2. Tích hợp Webhook tự động (SePay)</h4>
            
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

                        <div class="form-group mb-0">
                            <label style="display:block; margin-bottom: 8px; font-weight: 600;">URL Webhook của bạn (Điền vào SePay)</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" class="form-control" value="{{ url('/api/webhook/bank-transfer') }}" readonly style="width: 100%; padding: 10px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 6px; color: #475569;">
                            </div>
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

    @php
        $feeConfig = \App\Modules\Core\Models\Setting::getAllGrouped()['payment_fee_tab'] ?? [];
        $domesticFeeLabels = [
            'wallet' => 'Ví SoftwarePays', 'momo' => 'Ví MoMo', 'zalopay' => 'ZaloPay',
            'vnpay' => 'VNPay', 'vietqr' => 'VietQR', 'napas' => 'Napas 247',
        ];
        $intlMethodLabels = [
            'bitcoin' => 'Bitcoin', 'ethereum' => 'Ethereum',
            'litecoin' => 'Litecoin', 'usdt' => 'Tether (USDT)', 'solana' => 'Solana',
            'paylio_stripe' => 'Paylio - Thẻ (Stripe)', 'paylio_banxa' => 'Paylio - Chuyển khoản (Banxa)',
            'paylio_binance' => 'Paylio - Binance Pay', 'paylio_paypal' => 'Paylio - PayPal',
        ];
    @endphp

    <!-- Phí dịch vụ theo phương thức nội địa -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header" style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <span class="card-title" style="font-size: 18px; font-weight: 700; color: #0f172a;"><i class="fa-solid fa-percent" style="margin-right: 8px; color: #d97706;"></i> Phí dịch vụ theo phương thức</span>
            <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Phụ phí cộng thêm vào mỗi đơn hàng, riêng theo từng phương thức thanh toán (tạm tính = % trên tổng tiền hàng + số tiền cố định chung).</p>
        </div>
        <form action="{{ route('admin.settings.payment.fee') }}" method="POST" style="padding: 24px;">
            @csrf
            <div class="row">
                @foreach($domesticFeeLabels as $key => $label)
                    <div class="col-md-4 form-group mb-4">
                        <label style="display:block; margin-bottom: 8px; font-weight: 600;">% — {{ $label }}</label>
                        <input type="number" step="0.01" name="fee_pct_{{ $key }}" class="form-control" value="{{ $feeConfig['fee_pct_'.$key] ?? 0 }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                @endforeach
                <div class="col-md-4 form-group mb-4">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600;">Phụ phí cố định chung (đ)</label>
                    <input type="number" step="1" name="fee_fixed_vnd" class="form-control" value="{{ $feeConfig['fee_fixed_vnd'] ?? 0 }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
            </div>
            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; text-align: right;">
                <button type="submit" style="padding: 12px 30px; font-size: 15px; font-weight: bold; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer;">LƯU PHÍ DỊCH VỤ</button>
            </div>
        </form>
    </div>

    <!-- Phí phương thức quốc tế -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header" style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <span class="card-title" style="font-size: 18px; font-weight: 700; color: #0f172a;"><i class="fa-solid fa-globe" style="margin-right: 8px; color: #2563eb;"></i> Phương thức quốc tế</span>
            <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Chỉ hiển thị phí ước tính cho khách, chưa xử lý thanh toán thật. Phí = (Phí xử lý cơ bản % + Phí giao dịch quốc tế % + Phí quy đổi ngoại tệ %) × giá trị đơn hàng (USD) + Phí cố định ($).</p>
        </div>
        <form action="{{ route('admin.settings.payment.intl-fee') }}" method="POST" style="padding: 24px;">
            @csrf
            <div class="row">
                @foreach($intlMethodLabels as $key => $label)
                    <div class="col-md-6 form-group mb-4">
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px;">
                            <h5 style="font-weight:700; margin-bottom:12px;">{{ $label }}</h5>
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label style="display:block; margin-bottom: 6px; font-size:13px; color:#64748b;">Phí xử lý cơ bản (%)</label>
                                    <input type="number" step="0.01" name="intl_{{ $key }}_basic_pct" class="form-control" value="{{ $feeConfig['intl_'.$key.'_basic_pct'] ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label style="display:block; margin-bottom: 6px; font-size:13px; color:#64748b;">Phí cố định ($)</label>
                                    <input type="number" step="0.01" name="intl_{{ $key }}_fixed_usd" class="form-control" value="{{ $feeConfig['intl_'.$key.'_fixed_usd'] ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                                </div>
                                <div class="col-md-6 form-group mb-0">
                                    <label style="display:block; margin-bottom: 6px; font-size:13px; color:#64748b;">Phí giao dịch quốc tế (%)</label>
                                    <input type="number" step="0.01" name="intl_{{ $key }}_intl_pct" class="form-control" value="{{ $feeConfig['intl_'.$key.'_intl_pct'] ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                                </div>
                                <div class="col-md-6 form-group mb-0">
                                    <label style="display:block; margin-bottom: 6px; font-size:13px; color:#64748b;">Phí quy đổi ngoại tệ (%)</label>
                                    <input type="number" step="0.01" name="intl_{{ $key }}_fx_pct" class="form-control" value="{{ $feeConfig['intl_'.$key.'_fx_pct'] ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px;">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; text-align: right;">
                <button type="submit" style="padding: 12px 30px; font-size: 15px; font-weight: bold; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer;">LƯU PHÍ PHƯƠNG THỨC QUỐC TẾ</button>
            </div>
        </form>
    </div>
@endsection
