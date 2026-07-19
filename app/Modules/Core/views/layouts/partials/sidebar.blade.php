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
        <a href="{{ route('admin.categories') }}" class="nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <span class="material-symbols-outlined">category</span> Danh mục Thể loại
        </a>
        <a href="{{ route('admin.giftcards') }}" class="nav-item {{ request()->routeIs('admin.giftcards') ? 'active' : '' }}">
            <span class="material-symbols-outlined">card_giftcard</span> Thẻ nạp & Giftcard
        </a>
        <a href="{{ route('admin.keys') }}" class="nav-item {{ request()->routeIs('admin.keys') ? 'active' : '' }}">
            <span class="material-symbols-outlined">key</span> Kho Keys
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <span class="material-symbols-outlined">receipt_long</span> Đơn Hàng
        </a>
        <a href="{{ route('admin.esim') }}" class="nav-item {{ request()->routeIs('admin.esim') ? 'active' : '' }}">
            <span class="material-symbols-outlined">sim_card</span> Quản lý eSIM
        </a>
        <a href="{{ route('admin.card') }}" class="nav-item {{ request()->routeIs('admin.card') ? 'active' : '' }}">
            <span class="material-symbols-outlined">credit_card</span> Quản lý Thẻ Nạp
        </a>
        <a href="{{ route('admin.subscription') }}" class="nav-item {{ request()->routeIs('admin.subscription') ? 'active' : '' }}">
            <span class="material-symbols-outlined">subscriptions</span> Quản lý Gói Đăng Ký
        </a>
        <a href="{{ route('admin.software') }}" class="nav-item {{ request()->routeIs('admin.software') ? 'active' : '' }}">
            <span class="material-symbols-outlined">desktop_windows</span> Quản lý Phần Mềm
        </a>
        <a href="{{ route('admin.banners') }}" class="nav-item {{ request()->routeIs('admin.banners') ? 'active' : '' }}">
            <span class="material-symbols-outlined">view_carousel</span> Banner Trang Chủ
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">local_offer</span> Mã Giảm Giá
        </a>
        
        <div class="nav-section"><div class="nav-section-title">Khách hàng</div></div>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <span class="material-symbols-outlined">group</span> Quản lý Người dùng
        </a>

        <div class="nav-section"><div class="nav-section-title">Nội dung Blog</div></div>
        <a href="{{ route('admin.blog.posts') }}" class="nav-item {{ request()->routeIs('admin.blog.posts', 'admin.blog.posts.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">article</span> Bài viết
        </a>
        <a href="{{ route('admin.blog.categories') }}" class="nav-item {{ request()->routeIs('admin.blog.categories', 'admin.blog.categories.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">category</span> Danh mục
        </a>

        <div class="nav-section"><div class="nav-section-title">Sound World</div></div>
        <a href="{{ route('admin.soundmeme.sounds') }}" class="nav-item {{ request()->routeIs('admin.soundmeme.sounds', 'admin.soundmeme.sounds.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">graphic_eq</span> Âm thanh
        </a>
        <a href="{{ route('admin.soundmeme.categories') }}" class="nav-item {{ request()->routeIs('admin.soundmeme.categories') ? 'active' : '' }}">
            <span class="material-symbols-outlined">category</span> Danh mục
        </a>

        <div class="nav-section"><div class="nav-section-title">Thanh toán</div></div>
        <a href="{{ route('admin.transactions') }}" class="nav-item {{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
            <span class="material-symbols-outlined">receipt_long</span> Lịch sử Giao dịch
        </a>
        <a href="{{ route('admin.settings.payment') }}" class="nav-item {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
            <span class="material-symbols-outlined">account_balance</span> Cấu hình Thanh toán
        </a>
        <a href="{{ route('admin.settings.affiliate') }}" class="nav-item {{ request()->routeIs('admin.settings.affiliate') ? 'active' : '' }}">
            <span class="material-symbols-outlined">share_windows</span> Tiếp thị liên kết
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
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.index', 'admin.settings.store') ? 'active' : '' }}">
            <span class="material-symbols-outlined">tune</span> Cấu hình chung
        </a>
        <a href="{{ route('admin.settings.smm') }}" class="nav-item {{ request()->routeIs('admin.settings.smm', 'admin.settings.smm.store') ? 'active' : '' }}">
            <span class="material-symbols-outlined">network_node</span> SMM Panel (Like.vn)
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

