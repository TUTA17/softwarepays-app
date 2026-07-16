@extends('core::layouts.admin')

@section('title', 'Import Dữ Liệu')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>Nhập dữ liệu (Import Excel)</h1>
            <div class="page-breadcrumb">
                <span class="text-gray-500">Dashboard / Hệ thống / Import</span>
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

    <div class="data-card">
        <div class="data-card-header" style="padding: 20px; border-bottom: 1px solid var(--border);">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="color: var(--primary);">upload_file</span>
                Tải lên file dữ liệu
            </h3>
        </div>
        <div style="padding: 30px; text-align: center; max-width: 600px; margin: 0 auto;">
            
            <form action="{{ route('admin.system.import.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="margin-bottom: 24px;">
                    <span class="material-symbols-outlined" style="font-size: 64px; color: #94a3b8; display: block; margin-bottom: 16px;">post_add</span>
                    <h3 style="font-size: 18px; margin-bottom: 8px; color: var(--text-secondary); font-weight: 600;">Chọn file Excel để tải lên</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Bạn có thể upload các file mở rộng .xlsx, .xls hoặc .csv (tối đa 5MB)</p>
                    
                    <input type="file" name="file" id="file" required accept=".xlsx,.xls,.csv" style="display: block; margin: 0 auto; margin-bottom: 20px; padding: 10px; border: 1px dashed var(--border); border-radius: 6px; width: 100%; max-width: 400px; background: var(--bg-body);">
                    
                    @error('file')
                        <div style="color: var(--danger); font-size: 13px; margin-bottom: 10px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 500; font-size: 15px; padding: 12px 30px; height: auto;">
                    <span class="material-symbols-outlined">cloud_upload</span>
                    Tiếp tục
                </button>
            </form>
            
        </div>
    </div>
</div>
@endsection
