@extends('core::layouts.admin')

@section('title', 'Sao lưu dữ liệu')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>Sao lưu dữ liệu (Backup)</h1>
            <div class="page-breadcrumb">
                <span class="text-gray-500">Dashboard / Hệ thống / Sao lưu dữ liệu</span>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert bg-success text-white" role="alert" style="padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; background-color: var(--success); color: white;">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert bg-danger text-white" role="alert" style="padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; background-color: var(--danger); color: white;">
        <span class="material-symbols-outlined">error</span>
        {{ session('error') }}
    </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- 1. Danh sách bản sao lưu (Table) -->
        <div class="data-card">
            <div class="data-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--bg-body); display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px;">dns</span>
                    Danh sách Sao lưu cơ sở dữ liệu
                </h3>
                <a href="{{ route('admin.system.backup.run') }}" onclick="return confirm('Tiến trình tạo backup có thể mất một ít thời gian. Tiếp tục?');" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; font-size: 13px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">play_circle</span>
                    Chạy ngay bây giờ
                </a>
            </div>

            <div class="table-container">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--bg-body); border-bottom: 2px solid var(--border); text-align: left; color: var(--text-secondary); font-size: 13px; font-weight: 600;">
                            <th style="padding: 12px 16px;">Tên file</th>
                            <th style="padding: 12px 16px;">Kích thước</th>
                            <th style="padding: 12px 16px;">Ngày tạo</th>
                            <th style="padding: 12px 16px; text-align: right; width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 12px 16px;">
                                <a href="{{ route('admin.system.backup.download', $file['name']) }}" style="font-weight: 600; color: var(--text-secondary); text-decoration: none;">
                                    {{ $file['name'] }}
                                </a>
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted);">
                                <span style="background: var(--bg-card-hover); padding: 2px 6px; border-radius: 4px; font-size: 13px;">{{ $file['size'] }}</span>
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted); font-size: 13px;">
                                {{ $file['time'] }}
                            </td>
                            <td style="padding: 12px 16px; text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 4px;">
                                    <a href="{{ route('admin.system.backup.download', $file['name']) }}" class="btn btn-outline btn-sm" style="padding: 4px; color: var(--primary);" title="Tải xuống">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">download</span>
                                    </a>
                                    <a href="{{ route('admin.system.backup.delete', $file['name']) }}" onclick="return confirm('Xóa bản ghi này?');" class="btn btn-outline btn-sm" style="padding: 4px; color: var(--danger);" title="Xóa bản ghi">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 40px; text-align: center; color: #94a3b8;">
                                Bản sao lưu không tồn tại
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Cấu hình tự động backup -->
        <div class="data-card">
            <div class="data-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--bg-body); display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px;">settings_suggest</span>
                <h3 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0;">
                    Cấu hình lịch chạy tự động Sao lưu cơ sở dữ liệu
                </h3>
            </div>
            
            <div style="padding: 24px;">
                <form action="{{ route('admin.system.backup.store') }}" method="POST">
                    @csrf
                    
                    <div style="margin-bottom: 24px; display: flex; flex-direction: column; gap: 6px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none;">
                            <input type="checkbox" name="status" value="1" {{ isset($settings['status']) && $settings['status']->value == '1' ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                            <span style="font-weight: 600; font-size: 14px; color: var(--text-secondary);">Bật tự động sao lưu</span>
                        </label>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 13px; color: var(--text-secondary);">Số phút (vd: lúc 30 phút)</label>
                            <input type="text" name="minute_backup" class="form-control" style="font-size: 13px;" value="{{ $settings['minute_backup']->value ?? '*' }}" placeholder="*">
                            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">Nhập số phút hoặc dấu * (mỗi phút)</small>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 13px; color: var(--text-secondary);">Giờ (vd: lúc 2 giờ 30 chập 2 vào ô giờ)</label>
                            <input type="text" name="hour_backup" class="form-control" style="font-size: 13px;" value="{{ $settings['hour_backup']->value ?? '*' }}" placeholder="*">
                            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">Nhập số giờ (0-23)</small>
                        </div>
                        
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 13px; color: var(--text-secondary);">Ngày trong tháng</label>
                            <input type="text" name="day_in_month_backup" class="form-control" style="font-size: 13px;" value="{{ $settings['day_in_month_backup']->value ?? '*' }}" placeholder="*">
                            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">Nhập ngày (1-31)</small>
                        </div>
                        
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 13px; color: var(--text-secondary);">Tháng (vd: tháng 2)</label>
                            <input type="text" name="month_backup" class="form-control" style="font-size: 13px;" value="{{ $settings['month_backup']->value ?? '*' }}" placeholder="*">
                            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">Nhập tháng (1-12)</small>
                        </div>
                        
                        <div class="form-group" style="margin: 0; grid-column: span 2;">
                            <label class="form-label" style="font-size: 13px; color: var(--text-secondary);">Ngày trong tuần</label>
                            <input type="text" name="day_in_week_backup" class="form-control" style="font-size: 13px;" value="{{ $settings['day_in_week_backup']->value ?? '*' }}" placeholder="*">
                            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">(0 = Chủ nhật, 1 = Thứ 2... 6 = Thứ 7)</small>
                        </div>
                    </div>

                    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--border);">
                        <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                            Lưu cấu hình
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
