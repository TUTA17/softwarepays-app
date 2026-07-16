<header class="top-header">
        <div class="header-left">
            <button class="header-btn" onclick="history.back()" title="Quay lại" style="margin-right:4px;">
                <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <button class="header-btn sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="breadcrumb">@yield('breadcrumb')</div>
        </div>
        <div class="header-right">
            <button class="header-btn" id="push-notif-btn" onclick="togglePushNotification()" title="Bật/tắt thông báo đẩy (đơn hàng mới, tài khoản mới)">
                <span class="material-symbols-outlined" id="push-notif-icon">notifications</span>
            </button>
            <button class="header-btn"><span class="material-symbols-outlined">help_outline</span></button>
            <div class="dropdown">
                <div class="user-menu" onclick="event.stopPropagation(); this.parentElement.querySelector('.user-dropdown').classList.toggle('show');">
                    @php $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user(); @endphp
                    <div class="user-avatar">
                        @if($adminUser && $adminUser->image && $adminUser->image !== 'admin_default.png')
                            <img src="{{ asset($adminUser->image) }}" alt="{{ $adminUser->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:999px;">
                        @else
                            {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ session('admin_name', 'Admin') }}</span>
                    </div>
                    <span class="material-symbols-outlined" style="font-size:18px;color:var(--text-muted);">expand_more</span>
                </div>

                <!-- NEW PREMIUM USER DROPDOWN (Like Mockup) -->
                <div class="user-dropdown" onclick="event.stopPropagation();">
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-logo">
                            <img src="{{ $siteLogo ? asset($siteLogo) : asset('auth_assets/images/logo_large.png') }}" alt="Logo">
                        </div>
                        <div class="user-dropdown-name">{{ session('admin_name', 'Quản trị viên') }}</div>
                    </div>

                    <div class="user-dropdown-body">
                        <a href="{{ route('admin.profile') }}" class="user-action-item">
                            <div class="user-action-left">
                                <div class="user-action-icon"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">badge</span></div>
                                <div class="user-action-text">
                                    <span>Thông tin của tôi</span>
                                    <span>Cài đặt cá nhân</span>
                                </div>
                            </div>
                            <span class="material-symbols-outlined user-action-arrow">chevron_right</span>
                        </a>
                    </div>

                    <!-- Theme Toggle (Restored & Made Interactive) -->
                    <div class="theme-selector">
                        <div class="theme-title">Màu giao diện</div>
                        <div class="theme-options">
                            <div class="theme-opt" id="btn-theme-dark" onclick="switchTheme('dark')">Dark</div>
                            <div class="theme-opt active" id="btn-theme-light" onclick="switchTheme('light')">Light</div>
                        </div>
                    </div>

                    <div class="user-dropdown-footer" style="justify-content: center;">
                        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0; width: 100%;">
                            @csrf
                            <button type="submit" class="btn-logout" style="width: 100%; justify-content: center;">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
