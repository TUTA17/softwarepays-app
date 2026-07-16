@extends('core::layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb')<span>Dashboard</span>@endsection
@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Tổng quan hệ thống CRM</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="stat-label">Khách tiềm năng</div>
                <div class="stat-value">{{ number_format($totalLeads) }}</div>
            </div>
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb;">
                <span class="material-symbols-outlined">person_add</span>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Khách hàng</div>
                <div class="stat-value">{{ number_format($totalCustomers) }}</div>
            </div>
            <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Hợp đồng</div>
                <div class="stat-value">{{ number_format($totalBills) }}</div>
            </div>
            <div class="stat-icon" style="background:#fefce8;color:#d97706;">
                <span class="material-symbols-outlined">description</span>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Doanh số tháng</div>
                <div class="stat-value" style="font-size:22px;">{{ number_format($monthlyRevenue) }}đ</div>
            </div>
            <div class="stat-icon" style="background:#faf5ff;color:#9333ea;">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Chào mừng</span>
        </div>
        <div class="card-body">
            <p style="color:var(--text-secondary);font-size:14px;">Hệ thống CRM đã sẵn sàng. Sử dụng menu bên trái để truy cập các chức năng.</p>
        </div>
    </div>
@endsection
