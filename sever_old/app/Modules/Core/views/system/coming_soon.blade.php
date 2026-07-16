@extends('core::layouts.admin')

@section('title', $title ?? 'Tính năng đang phát triển')

@section('content')
<style>
    /* Tổng quan giao diện nền sáng hài hoà với Admin */
    .cs-container {
        position: relative;
        min-height: calc(100vh - 120px);
        background: transparent; /* Hoà nhập hoàn toàn với nền #f8fafc của admin */
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: -10px;
        z-index: 1;
    }

    /* Hiệu ứng màu Gradient mềm mại trôi bồng bềnh (Soft Light) */
    .cs-shape {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        z-index: -1;
        animation: cs-float 15s infinite alternate ease-in-out;
    }
    .cs-shape-1 { width: 400px; height: 400px; background: rgba(37, 99, 235, 0.15); top: -100px; left: -100px; }
    .cs-shape-2 { width: 350px; height: 350px; background: rgba(56, 189, 248, 0.15); bottom: -50px; right: 8%; animation-delay: -5s; }
    .cs-shape-3 { width: 250px; height: 250px; background: rgba(139, 92, 246, 0.1); top: 30%; left: 50%; animation-duration: 20s; }

    @keyframes cs-float {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(40px, -40px) scale(1.1); }
    }

    /* Khung Card Glassmorphism Siêu Sạch */
    .cs-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255,255,255,1);
        border-radius: 36px;
        padding: 56px 50px;
        text-align: center;
        max-width: 540px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04), 0 2px 5px rgba(0,0,0,0.02);
        position: relative;
        animation: cs-slide-up 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transform: translateY(30px); opacity: 0;
    }

    @keyframes cs-slide-up {
        to { transform: translateY(0); opacity: 1; }
    }

    /* Hộp Icon 3D Góc Xéo */
    .cs-icon-wrapper {
        width: 104px; height: 104px;
        margin: 0 auto 32px;
        background: linear-gradient(135deg, var(--primary), #38bdf8);
        border-radius: 30px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 15px 30px rgba(37, 99, 235, 0.25), inset 0 2px 2px rgba(255,255,255,0.4);
        position: relative;
        transform: rotate(-8deg);
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .cs-card:hover .cs-icon-wrapper {
        transform: rotate(5deg) scale(1.05);
    }
    
    .cs-icon-wrapper .material-symbols-outlined {
        color: white; font-size: 50px; font-variation-settings: 'FILL' 1;
        transform: rotate(8deg);
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }
    .cs-card:hover .cs-icon-wrapper .material-symbols-outlined {
        transform: rotate(-5deg);
    }

    /* Các Icon lơ lửng xung quanh */
    .cs-floating-badge {
        position: absolute;
        display: flex; align-items: center; justify-content: center;
        background: var(--bg-card); border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        padding: 6px; z-index: -1;
        animation: cs-bounce 3s infinite alternate ease-in-out;
    }
    .cs-badge-1 { top: -15px; right: -20px; animation-delay: 0s; }
    .cs-badge-1 .material-symbols-outlined { color: #f59e0b; font-size: 22px; font-variation-settings: 'FILL' 1; }
    .cs-badge-2 { bottom: -12px; left: -16px; animation-delay: -1.5s; padding: 4px; border-radius: 10px; }
    .cs-badge-2 .material-symbols-outlined { color: #10b981; font-size: 18px; font-variation-settings: 'FILL' 1; }

    @keyframes cs-bounce {
        0% { transform: translateY(0) scale(1); }
        100% { transform: translateY(-12px) scale(1.05); }
    }

    /* Typography Gọn Gàng Sắc Nét */
    .cs-title {
        color: var(--text-primary); font-size: 26px; font-weight: 800; margin-bottom: 14px;
        letter-spacing: -0.5px; line-height: 1.35;
    }
    .cs-feature-name {
        color: var(--primary); background: linear-gradient(135deg, var(--primary), #38bdf8);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        display: inline-block; font-size: 30px; margin-bottom: 4px; line-height: 1.2;
    }
    .cs-desc {
        color: var(--text-muted); font-size: 15px; line-height: 1.65; margin-bottom: 36px;
        max-width: 95%; margin-left: auto; margin-right: auto;
    }

    /* Nút Bấm Hiện Đại Premium */
    .cs-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .cs-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 26px; border-radius: 14px; font-weight: 600; font-size: 14px;
        text-decoration: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid transparent; letter-spacing: 0.2px; font-family: inherit;
    }
    
    .cs-btn-primary {
        background: var(--primary); color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .cs-btn-primary:hover {
        background: var(--primary-hover); color: white;
        transform: translateY(-3px); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }
    
    .cs-btn-outline {
        background: var(--bg-card); color: var(--text-secondary);
        border-color: var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .cs-btn-outline:hover {
        background: var(--bg-body); color: var(--text-primary); border-color: #cbd5e1;
        transform: translateY(-3px); box-shadow: 0 6px 14px rgba(0,0,0,0.06);
    }

    .cs-btn .material-symbols-outlined { font-size: 18px; transition: transform 0.3s; }
    .cs-btn-primary:hover .material-symbols-outlined { transform: translateX(3px); }
    .cs-btn-outline:hover .material-symbols-outlined { transform: translateX(-3px); }
</style>

<div class="cs-container">
    <!-- Mây màu sáng ẩn hiện -->
    <div class="cs-shape cs-shape-1"></div>
    <div class="cs-shape cs-shape-2"></div>
    <div class="cs-shape cs-shape-3"></div>

    <div class="cs-card">
        <!-- Khối Icon Xoay nổi 3D -->
        <div class="cs-icon-wrapper">
            <div class="cs-floating-badge cs-badge-1"><span class="material-symbols-outlined">stars</span></div>
            <div class="cs-floating-badge cs-badge-2"><span class="material-symbols-outlined">verified</span></div>
            <span class="material-symbols-outlined">rocket_launch</span>
        </div>

        <!-- Thông Tin Tính Năng -->
        <div class="cs-title">
            Chức năng <span class="cs-feature-name">"{{ $title ?? 'Hệ thống' }}"</span><br>
            Sắp ra mắt
        </div>

        <p class="cs-desc">
            Quy trình nghiên cứu và tối ưu hóa đang được tiến hành để mang đến công cụ hoạt động hiệu quả nhất, khớp nối toàn vẹn với hệ thống CRM của bạn. Vui lòng quay lại trong các bản nâng cấp tới!
        </p>

        <!-- Nút Trở Về Trực Quan -->
        <div class="cs-actions">
            <button type="button" onclick="window.history.back()" class="cs-btn cs-btn-outline">
                <span class="material-symbols-outlined">arrow_back</span>
                Trang trước
            </button>
            <a href="{{ route('admin.dashboard') }}" class="cs-btn cs-btn-primary">
                Trang chủ Hệ thống
                <span class="material-symbols-outlined">home</span>
            </a>
        </div>
    </div>
</div>
@endsection
