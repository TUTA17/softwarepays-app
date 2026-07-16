@extends('core::layouts.admin')

@section('title', 'Quản lý Cache')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>Quản lý Cache</h1>
            <div class="page-breadcrumb">
                <span class="text-gray-500">Dashboard / Hệ thống / Cache</span>
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
        <div class="data-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--bg-body); display: flex; align-items: center; gap: 8px;">
            <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px;">autorenew</span>
            <h3 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0;">Các lệnh bộ nhớ đệm cơ bản</h3>
        </div>

        <div style="padding: 0;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                
                <!-- Xóa tất cả bộ nhớ đệm -->
                <li style="padding: 16px 24px; border-bottom: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: var(--text-secondary); font-size: 14px;">
                        Xóa các bộ nhớ đệm của ứng dụng, cơ sở dữ liệu, nội dung tĩnh... Chạy lệnh này khi bạn cập nhật dữ liệu nhưng giao diện không thay đổi
                    </div>
                    <a href="{{ route('admin.system.cache.clear', 'all') }}" class="btn" style="background: #e8f5e9; color: #16a34a; border: none; font-size: 13px; font-weight: 500; padding: 8px 16px; min-width: 250px; text-align: center; justify-content: center;">
                        Xóa tất cả các bộ nhớ đệm hiện có ứng dụng
                    </a>
                </li>

                <!-- Làm mới bộ đệm giao diện -->
                <li style="padding: 16px 24px; border-bottom: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: var(--text-secondary); font-size: 14px;">
                        Làm mới bộ đệm giao diện giúp phần giao diện luôn mới nhất
                    </div>
                    <a href="{{ route('admin.system.cache.clear', 'view') }}" class="btn" style="background: #e8f5e9; color: #16a34a; border: none; font-size: 13px; font-weight: 500; padding: 8px 16px; min-width: 250px; text-align: center; justify-content: center;">
                        Làm mới bộ đệm giao diện
                    </a>
                </li>

                <!-- Xóa bộ nhớ đệm cấu hình -->
                <li style="padding: 16px 24px; border-bottom: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: var(--text-secondary); font-size: 14px;">
                        Bạn cần làm mới bộ đệm cấu hình khi tạo ra sự thay đổi nào đó ở môi trường thành phẩm
                    </div>
                    <a href="{{ route('admin.system.cache.clear', 'setting') }}" class="btn" style="background: #e8f5e9; color: #16a34a; border: none; font-size: 13px; font-weight: 500; padding: 8px 16px; min-width: 250px; text-align: center; justify-content: center;">
                        Xóa bộ nhớ đệm của phần cấu hình
                    </a>
                </li>

                <!-- Xóa cache đường dẫn -->
                <li style="padding: 16px 24px; border-bottom: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: var(--text-secondary); font-size: 14px;">
                        Cần thực hiện thao tác này khi không xuất hiện đường dẫn mới
                    </div>
                    <a href="{{ route('admin.system.cache.clear', 'route') }}" class="btn" style="background: #e8f5e9; color: #16a34a; border: none; font-size: 13px; font-weight: 500; padding: 8px 16px; min-width: 250px; text-align: center; justify-content: center;">
                        Xóa cache đường dẫn
                    </a>
                </li>

                <!-- Xóa lịch sử lỗi -->
                <li style="padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: var(--text-secondary); font-size: 14px;">
                        Xóa dữ liệu lịch sử lỗi được ghi nhận trong bảng hệ thống
                    </div>
                    <a href="{{ route('admin.system.cache.clear', 'error') }}" class="btn" style="background: #e8f5e9; color: #16a34a; border: none; font-size: 13px; font-weight: 500; padding: 8px 16px; min-width: 250px; text-align: center; justify-content: center;">
                        Xóa lịch sử lỗi
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
@endsection
