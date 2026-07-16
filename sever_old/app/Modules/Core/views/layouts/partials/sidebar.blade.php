<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">
            <div class="sidebar-logo"><img src="{{ $siteLogo ? asset($siteLogo) : asset('auth_assets/images/logo_large.png') }}" alt="Logo"></div>
            <span class="sidebar-brand">{{ $siteName }}<small>Hệ thống CRM nội bộ</small></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>

        <div class="nav-section"><div class="nav-section-title">Quản lý Game Shop</div></div>
        <a href="{{ route('admin.products') }}" class="nav-item {{ request()->routeIs('admin.products') ? 'active' : '' }}">
            <span class="material-symbols-outlined">sports_esports</span> Danh sách Game
        </a>
        <a href="{{ route('admin.keys') }}" class="nav-item {{ request()->routeIs('admin.keys') ? 'active' : '' }}">
            <span class="material-symbols-outlined">key</span> Kho Keys
        </a>
        
        <div class="nav-section"><div class="nav-section-title">Khách hàng</div></div>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <span class="material-symbols-outlined">group</span> Quản lý Người dùng
        </a>

        <div class="nav-section"><div class="nav-section-title">Thanh toán</div></div>
        <a href="{{ route('admin.transactions') }}" class="nav-item {{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
            <span class="material-symbols-outlined">receipt_long</span> Lịch sử Giao dịch
        </a>
        <a href="{{ route('admin.settings.payment') }}" class="nav-item {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
            <span class="material-symbols-outlined">account_balance</span> Cấu hình Thanh toán
        </a>

        {{-- ===== NỘI BỘ - NHÂN SỰ ===== --}}
        {{-- Tạm ẩn theo yêu cầu
        <div class="nav-section"><div class="nav-section-title">Nội bộ</div></div>
        <div class="nav-group {{ request()->is('admin/hradmin*') || request()->is('admin/timekeeper*') || request()->is('admin/penalty_ticket*') || request()->is('admin/admin/thong-ke*') ? 'expanded' : '' }}">
            <div class="nav-group-toggle" onclick="this.parentElement.classList.toggle('expanded')">
                <div class="left-box">
                    <span class="material-symbols-outlined">badge</span>
                    Hành chính - Nhân sự
                </div>
                <span class="material-symbols-outlined arrow">expand_more</span>
            </div>
            <div class="nav-group-menu">
                <a href="/admin/hradmin/index" class="nav-item {{ request()->is('admin/hradmin*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined">people</span> Nhân sự
                </a>
            </div>
        --}}
            <a href="{{ route('admin.admins.index') }}" class="nav-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">manage_accounts</span> Đăng nhập & Tài khoản
            </a>
            <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">admin_panel_settings</span> Phân quyền
            </a>
        {{-- </div> --}}

        <div class="nav-section"><div class="nav-section-title">Cấu hình</div></div>
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">tune</span> Cấu hình chung
        </a>


        <div class="nav-section"><div class="nav-section-title">Hệ thống</div></div>
        <div class="nav-group {{ request()->routeIs('admin.system.*') ? 'expanded' : '' }}">
            <div class="nav-group-toggle" onclick="this.parentElement.classList.toggle('expanded')">
                <div class="left-box"><span class="material-symbols-outlined">settings_suggest</span> Quản trị HT</div>
                <span class="material-symbols-outlined arrow">expand_more</span>
            </div>
            <div class="nav-group-menu">
                <a href="{{ route('admin.system.cache.index') }}" class="nav-item {{ request()->routeIs('admin.system.cache.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined">cached</span> Cache
                </a>

                <a href="{{ route('admin.system.backup.index') }}" class="nav-item {{ request()->routeIs('admin.system.backup.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined">backup</span> Sao lưu DL
                </a>
                <a href="{{ route('admin.system.import.index') }}" class="nav-item {{ request()->routeIs('admin.system.import.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined">upload_file</span> Import
                </a>

            </div>
        </div>
    </nav>

    <!-- Chân Sidebar đã được ẩn gọn gàng -->
</aside>

