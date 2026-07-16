@extends('core::layouts.admin')
@section('title', 'Profile Admin')
@section('content')
    <div class="page-header">
        <h1 class="page-title">Profile Admin</h1>
        <div style="display:flex;gap:10px;">
            <a href="{{ url()->previous() }}" class="btn btn-outline"><span class="material-symbols-outlined"
                    style="font-size:18px;">arrow_back</span> Quay lại</a>
            <button type="submit" form="profile-form" class="btn btn-primary"><span class="material-symbols-outlined"
                    style="font-size:18px;">check</span> Lưu</button>
        </div>
    </div>

    <div class="profile-layout">
        {{-- ===== LEFT SIDEBAR ===== --}}
        <div class="profile-sidebar">
            <div class="card">
                <div class="card-body" style="text-align:center;padding:28px 20px;">
                    {{-- Avatar --}}
                    <div class="profile-avatar-wrap">
                        @if ($admin->image && $admin->image !== 'admin_default.png')
                            <img src="{{ asset($admin->image) }}" alt="{{ $admin->name }}" class="profile-avatar-img">
                        @else
                            <div class="profile-avatar-placeholder">{{ strtoupper(substr($admin->name, 0, 2)) }}</div>
                        @endif
                    </div>
                    <h3 class="profile-name">{{ $admin->name }}</h3>
                    <span class="badge badge-primary">{{ $admin->roles->first()?->name ?? 'Nhân viên' }}</span>
                </div>

                {{-- Info Summary --}}
                <div style="border-top:1px solid var(--border);padding:20px;">
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Mã thành viên</span>
                        <span class="sidebar-info-value">{{ $admin->code ?: 'N/A' }}</span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Email</span>
                        <span class="sidebar-info-value">{{ $admin->email }}</span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">SĐT</span>
                        <span class="sidebar-info-value">{{ $admin->tel ?: 'N/A' }}</span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Số căn cước</span>
                        <span class="sidebar-info-value">{{ $admin->cccd ?: 'N/A' }}</span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Giới tính</span>
                        <span class="sidebar-info-value">
                            @if ($admin->gioitinh == 1)
                                Nam
                            @elseif($admin->gioitinh == 2)
                                Nữ
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>

                {{-- CCCD Photos --}}
                <div style="border-top:1px solid var(--border);padding:20px;">
                    <div class="sidebar-info-label" style="margin-bottom:12px;">Ảnh căn cước mặt trước</div>
                    @if ($admin->ID_card_photo_on_the_front)
                        <img src="{{ asset($admin->ID_card_photo_on_the_front) }}" alt="CCCD mặt trước"
                            class="cccd-preview-img">
                    @else
                        <div class="cccd-placeholder"><span class="material-symbols-outlined">badge</span> Chưa cập nhật
                        </div>
                    @endif

                    <div class="sidebar-info-label" style="margin-bottom:12px;margin-top:16px;">Ảnh căn cước mặt sau</div>
                    @if ($admin->ID_card_photo_on_the_back)
                        <img src="{{ asset($admin->ID_card_photo_on_the_back) }}" alt="CCCD mặt sau"
                            class="cccd-preview-img">
                    @else
                        <div class="cccd-placeholder"><span class="material-symbols-outlined">badge</span> Chưa cập nhật
                        </div>
                    @endif
                </div>

                {{-- Navigation Tabs --}}
                <div style="border-top:1px solid var(--border);padding:16px 20px;">
                    <a href="#section-info" class="profile-nav-item active"
                        onclick="switchProfileTab(event, 'section-info')">
                        <span class="material-symbols-outlined">person</span> Thông tin chính
                    </a>
                    <a href="#section-password" class="profile-nav-item"
                        onclick="switchProfileTab(event, 'section-password')">
                        <span class="material-symbols-outlined">lock</span> Đổi mật khẩu
                    </a>
                    <a href="#section-security" class="profile-nav-item"
                        onclick="switchProfileTab(event, 'section-security')">
                        <span class="material-symbols-outlined">shield</span> Bảo mật
                    </a>
                </div>

                {{-- Logout link --}}
                <div style="border-top:1px solid var(--border);padding:16px 20px;">
                    <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="profile-logout-link">
                            <span class="material-symbols-outlined"
                                style="font-size:18px;color:var(--danger);">logout</span>
                            Đăng xuất khỏi các thiết bị khác
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT CONTENT ===== --}}
        <div class="profile-content">
            {{-- TAB 1: Thông tin cơ bản --}}
            <div class="profile-section" id="section-info">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><span class="material-symbols-outlined"
                                style="font-size:20px;vertical-align:middle;margin-right:6px;">info</span> Thông tin cơ bản
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="profile-form" method="POST" action="{{ route('admin.profile.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="profile-form-section-title">Thông tin cơ bản :</div>

                            {{-- Avatar Upload --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Ảnh đại diện</label>
                                <div class="profile-form-field">
                                    <div class="avatar-upload-box" id="avatar-upload-box">
                                        <div class="avatar-preview" id="avatar-preview">
                                            @if ($admin->image && $admin->image !== 'admin_default.png')
                                                <img src="{{ asset($admin->image) }}" alt="Avatar" id="avatar-img">
                                            @else
                                                <div class="avatar-initials" id="avatar-initials">
                                                    {{ strtoupper(substr($admin->name, 0, 2)) }}</div>
                                            @endif
                                            <label for="avatar-input" class="avatar-edit-btn" title="Thay ảnh">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:16px;">edit</span>
                                            </label>
                                        </div>
                                        @php
                                            $siteName =
                                                \App\Modules\Core\Models\Setting::where('name', 'name')
                                                    ->where('type', 'general_tab')
                                                    ->value('value') ?:
                                                config('app.name');
                                        @endphp
                                        <div class="avatar-brand">{{ $siteName }}</div>
                                        <input type="file" name="image" id="avatar-input" accept="image/*"
                                            style="display:none;" onchange="previewAvatar(this)">
                                        <button type="button" class="avatar-remove-btn" onclick="removeAvatar()"><span
                                                class="material-symbols-outlined"
                                                style="font-size:14px;">close</span></button>
                                    </div>
                                </div>
                            </div>

                            {{-- Họ & tên --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Họ & tên</label>
                                <div class="profile-form-field">
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $admin->name) }}" required>
                                </div>
                            </div>

                            {{-- SĐT --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">SĐT</label>
                                <div class="profile-form-field">
                                    <div class="input-with-icon">
                                        <span class="material-symbols-outlined input-icon">call</span>
                                        <input type="text" name="tel" class="form-control"
                                            style="padding-left:40px;" value="{{ old('tel', $admin->tel) }}"
                                            placeholder="Số điện thoại">
                                    </div>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Email</label>
                                <div class="profile-form-field">
                                    <div class="input-with-icon">
                                        <span class="material-symbols-outlined input-icon">alternate_email</span>
                                        <input type="email" name="email" class="form-control"
                                            style="padding-left:40px;" value="{{ old('email', $admin->email) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            {{-- Phân quyền (read-only) --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Phân quyền</label>
                                <div class="profile-form-field">
                                    <span
                                        class="profile-readonly-value">{{ $admin->roles->first()?->name ?? 'Nhân viên' }}</span>
                                </div>
                            </div>

                            {{-- Giới thiệu --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Giới thiệu</label>
                                <div class="profile-form-field">
                                    <input type="text" name="intro" class="form-control"
                                        value="{{ old('intro', $admin->intro) }}"
                                        placeholder="Vài dòng giới thiệu về bạn...">
                                </div>
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Địa chỉ</label>
                                <div class="profile-form-field">
                                    <input type="text" name="address" class="form-control"
                                        value="{{ old('address', $admin->address) }}" placeholder="Địa chỉ hiện tại">
                                </div>
                            </div>

                            {{-- Số CCCD --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Số CCCD</label>
                                <div class="profile-form-field">
                                    <input type="text" name="cccd" class="form-control"
                                        value="{{ old('cccd', $admin->cccd) }}" placeholder="Căn cước công dân">
                                </div>
                            </div>

                            {{-- Giới tính --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Giới tính</label>
                                <div class="profile-form-field">
                                    <select name="gioitinh" class="form-control" style="max-width:200px;">
                                        <option value="">-- Chọn --</option>
                                        <option value="1"
                                            {{ old('gioitinh', $admin->gioitinh) == 1 ? 'selected' : '' }}>Nam</option>
                                        <option value="2"
                                            {{ old('gioitinh', $admin->gioitinh) == 2 ? 'selected' : '' }}>Nữ</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Ngày sinh --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Ngày sinh</label>
                                <div class="profile-form-field">
                                    <input type="date" name="birthday" class="form-control" style="max-width:200px;"
                                        value="{{ old('birthday', $admin->birthday) }}">
                                </div>
                            </div>

                            <div class="profile-form-divider"></div>
                            <div class="profile-form-section-title">Ảnh căn cước công dân :</div>

                            @php
                                // Helper: xử lý nhiều dạng path ảnh (URL đầy đủ, relative, storage...)
                                function cccdImgUrl($path)
                                {
                                    if (!$path) {
                                        return null;
                                    }
                                    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                                        return $path;
                                    }
                                    if (str_starts_with($path, '/')) {
                                        return asset($path);
                                    }
                                    return asset($path);
                                }
                                $frontUrl = cccdImgUrl($admin->ID_card_photo_on_the_front);
                                $backUrl = cccdImgUrl($admin->ID_card_photo_on_the_back);
                            @endphp

                            <div class="cccd-cards-grid">
                                {{-- CCCD mặt trước --}}
                                <div class="cccd-card">
                                    <div class="cccd-card-label">
                                        <span class="material-symbols-outlined" style="font-size:18px;">badge</span>
                                        Mặt trước
                                    </div>
                                    <div class="cccd-card-body" id="cccd-front-preview"
                                        onclick="document.getElementById('cccd-front-input').click()">
                                        @if ($frontUrl)
                                            <img src="{{ $frontUrl }}" alt="CCCD mặt trước" class="cccd-img"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="cccd-drop-zone-inner"
                                                style="display:none;position:absolute;inset:0;">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:36px;color:var(--text-muted);">broken_image</span>
                                                <span style="font-size:12px;color:var(--text-muted);">Ảnh lỗi - Bấm để chọn
                                                    ảnh mới</span>
                                            </div>
                                            <div class="cccd-overlay-btn">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:16px;">edit</span> Thay ảnh
                                            </div>
                                        @else
                                            <div class="cccd-drop-zone-inner">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:36px;color:var(--primary);opacity:0.5;">add_photo_alternate</span>
                                                <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Bấm
                                                    để chọn ảnh mặt trước</span>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="ID_card_photo_on_the_front" id="cccd-front-input"
                                        accept="image/*" style="display:none;"
                                        onchange="previewCCCD(this, 'cccd-front-preview')">
                                </div>

                                {{-- CCCD mặt sau --}}
                                <div class="cccd-card">
                                    <div class="cccd-card-label">
                                        <span class="material-symbols-outlined" style="font-size:18px;">badge</span>
                                        Mặt sau
                                    </div>
                                    <div class="cccd-card-body" id="cccd-back-preview"
                                        onclick="document.getElementById('cccd-back-input').click()">
                                        @if ($backUrl)
                                            <img src="{{ $backUrl }}" alt="CCCD mặt sau" class="cccd-img"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="cccd-drop-zone-inner"
                                                style="display:none;position:absolute;inset:0;">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:36px;color:var(--text-muted);">broken_image</span>
                                                <span style="font-size:12px;color:var(--text-muted);">Ảnh lỗi - Bấm để chọn
                                                    ảnh mới</span>
                                            </div>
                                            <div class="cccd-overlay-btn">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:16px;">edit</span> Thay ảnh
                                            </div>
                                        @else
                                            <div class="cccd-drop-zone-inner">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:36px;color:var(--primary);opacity:0.5;">add_photo_alternate</span>
                                                <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Bấm
                                                    để chọn ảnh mặt sau</span>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="ID_card_photo_on_the_back" id="cccd-back-input"
                                        accept="image/*" style="display:none;"
                                        onchange="previewCCCD(this, 'cccd-back-preview')">
                                </div>
                            </div>

                            <div class="profile-form-divider"></div>
                            <div class="profile-form-section-title">Mạng xã hội :</div>

                            {{-- Facebook --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Facebook</label>
                                <div class="profile-form-field">
                                    <input type="text" name="facebook" class="form-control"
                                        value="{{ old('facebook', $admin->facebook) }}" placeholder="URL Facebook">
                                </div>
                            </div>

                            {{-- Zalo --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Zalo</label>
                                <div class="profile-form-field">
                                    <input type="text" name="zalo" class="form-control"
                                        value="{{ old('zalo', $admin->zalo) }}" placeholder="Số Zalo">
                                </div>
                            </div>

                            {{-- Skype --}}
                            <div class="profile-form-row">
                                <label class="profile-form-label">Skype</label>
                                <div class="profile-form-field">
                                    <input type="text" name="skype" class="form-control"
                                        value="{{ old('skype', $admin->skype) }}" placeholder="Skype ID">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TAB 2: Đổi mật khẩu --}}
            <div class="profile-section" id="section-password" style="display:none;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><span class="material-symbols-outlined"
                                style="font-size:20px;vertical-align:middle;margin-right:6px;">lock</span> Đổi mật khẩu
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.profile.changePassword') }}">
                            @csrf
                            <div class="profile-form-row">
                                <label class="profile-form-label">Mật khẩu hiện tại</label>
                                <div class="profile-form-field">
                                    <input type="password" name="current_password" class="form-control" required
                                        placeholder="Nhập mật khẩu hiện tại">
                                </div>
                            </div>
                            <div class="profile-form-row">
                                <label class="profile-form-label">Mật khẩu mới</label>
                                <div class="profile-form-field">
                                    <input type="password" name="new_password" class="form-control" required
                                        placeholder="Tối thiểu 6 ký tự">
                                </div>
                            </div>
                            <div class="profile-form-row">
                                <label class="profile-form-label">Xác nhận mật khẩu</label>
                                <div class="profile-form-field">
                                    <input type="password" name="new_password_confirmation" class="form-control" required
                                        placeholder="Nhập lại mật khẩu mới">
                                </div>
                            </div>
                            <div style="display:flex;justify-content:flex-end;margin-top:24px;">
                                <button type="submit" class="btn btn-primary"><span class="material-symbols-outlined"
                                        style="font-size:18px;">key</span> Đổi mật khẩu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TAB 3: Bảo mật --}}
            <div class="profile-section" id="section-security" style="display:none;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><span class="material-symbols-outlined"
                                style="font-size:20px;vertical-align:middle;margin-right:6px;">shield</span> Bảo mật</h3>
                    </div>
                    <div class="card-body">
                        <div class="security-item">
                            <div class="security-item-info">
                                <div class="security-item-icon"
                                    style="background:rgba(37,99,235,0.1);color:var(--primary);">
                                    <span class="material-symbols-outlined">devices</span>
                                </div>
                                <div>
                                    <h4 class="security-item-title">Phiên đăng nhập</h4>
                                    <p class="security-item-desc">Quản lý các phiên đăng nhập trên các thiết bị</p>
                                </div>
                            </div>
                            <span class="badge badge-success">Đang hoạt động</span>
                        </div>
                        <div class="security-item">
                            <div class="security-item-info">
                                <div class="security-item-icon"
                                    style="background:rgba(22,163,74,0.1);color:var(--success);">
                                    <span class="material-symbols-outlined">verified_user</span>
                                </div>
                                <div>
                                    <h4 class="security-item-title">Xác thực hai yếu tố (2FA)</h4>
                                    <p class="security-item-desc">Tăng cường bảo mật cho tài khoản</p>
                                </div>
                            </div>
                            <span class="badge badge-muted">Chưa kích hoạt</span>
                        </div>
                        <div class="security-item">
                            <div class="security-item-info">
                                <div class="security-item-icon"
                                    style="background:rgba(217,119,6,0.1);color:var(--warning);">
                                    <span class="material-symbols-outlined">history</span>
                                </div>
                                <div>
                                    <h4 class="security-item-title">Lịch sử truy cập</h4>
                                    <p class="security-item-desc">Xem toàn bộ hoạt động đăng nhập gần đây</p>
                                </div>
                            </div>
                            <a href="#" class="btn btn-outline btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* === PROFILE LAYOUT === */
        .profile-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
        }

        /* === SIDEBAR === */
        .profile-avatar-wrap {
            width: 96px;
            height: 96px;
            margin: 0 auto 16px;
            position: relative;
        }

        .profile-avatar-img {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.18);
        }

        .profile-avatar-placeholder {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            font-size: 32px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.18);
        }

        .profile-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 8px 0 8px;
        }

        .sidebar-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-info-item:last-child {
            border-bottom: none;
        }

        .sidebar-info-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .sidebar-info-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
        }

        .cccd-preview-img {
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--border);
            object-fit: cover;
            max-height: 140px;
        }

        .cccd-placeholder {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 16px;
            background: var(--bg-body);
            border-radius: 8px;
            border: 1px dashed var(--border);
            color: var(--text-muted);
            font-size: 13px;
            justify-content: center;
        }

        /* Profile Nav Items in sidebar */
        .profile-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 4px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .profile-nav-item:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .profile-nav-item.active {
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary);
        }

        .profile-nav-item .material-symbols-outlined {
            font-size: 20px;
        }

        .profile-logout-link {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--danger);
            font-size: 13px;
            font-weight: 600;
            font-family: 'Public Sans', sans-serif;
            padding: 6px 0;
            transition: opacity 0.2s;
        }

        .profile-logout-link:hover {
            opacity: 0.8;
        }

        /* === PROFILE FORM === */
        .profile-form-section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .profile-form-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 16px;
            align-items: center;
            margin-bottom: 18px;
        }

        @media (max-width: 768px) {
            .profile-form-row {
                grid-template-columns: 1fr;
                gap: 6px;
            }
        }

        .profile-form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            text-align: right;
        }

        @media (max-width: 768px) {
            .profile-form-label {
                text-align: left;
            }
        }

        .profile-form-field {
            max-width: 500px;
        }

        .profile-form-divider {
            border-top: 1px solid var(--border);
            margin: 28px 0;
        }

        .profile-readonly-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            padding: 9px 0;
            display: block;
        }

        /* Input with icon */
        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: var(--text-muted);
            pointer-events: none;
        }

        /* Avatar Upload */
        .avatar-upload-box {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--bg-body);
        }

        .avatar-preview {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            background: var(--bg-card-hover);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-initials {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
        }

        .avatar-edit-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        .avatar-edit-btn:hover {
            background: var(--primary-hover);
        }

        .avatar-brand {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .avatar-remove-btn {
            position: absolute;
            bottom: -6px;
            right: -6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--danger);
            color: white;
            border: 2px solid var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .avatar-remove-btn:hover {
            transform: scale(1.15);
        }

        /* === SECURITY ITEMS === */
        .security-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 0;
            border-bottom: 1px solid var(--border);
        }

        .security-item:last-child {
            border-bottom: none;
        }

        .security-item-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .security-item-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .security-item-icon .material-symbols-outlined {
            font-size: 22px;
        }

        .security-item-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .security-item-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin: 2px 0 0;
        }

        /* === CCCD UPLOAD === */
        .cccd-cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-left: 166px;
            max-width: 700px;
        }

        @media (max-width: 768px) {
            .cccd-cards-grid {
                grid-template-columns: 1fr;
                margin-left: 0;
            }
        }

        .cccd-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            background: var(--bg-body);
            transition: box-shadow 0.2s;
        }

        .cccd-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .cccd-card-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
        }

        .cccd-card-body {
            position: relative;
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cccd-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }

        .cccd-overlay-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 14px;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            backdrop-filter: blur(6px);
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .cccd-overlay-btn:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: translateY(-1px);
        }

        .cccd-drop-zone-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 24px;
            cursor: pointer;
            width: 100%;
            height: 100%;
            min-height: 140px;
            transition: background 0.2s;
        }

        .cccd-drop-zone-inner:hover {
            background: rgba(37, 99, 235, 0.04);
        }
    </style>
@endpush

@push('scripts')
    <script>
        function switchProfileTab(event, sectionId) {
            event.preventDefault();
            // Hide all sections
            document.querySelectorAll('.profile-section').forEach(s => s.style.display = 'none');
            // Show selected
            document.getElementById(sectionId).style.display = 'block';
            // Update nav active
            document.querySelectorAll('.profile-nav-item').forEach(n => n.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    const existing = preview.querySelector('img');
                    const initials = document.getElementById('avatar-initials');
                    if (initials) initials.style.display = 'none';
                    if (existing) {
                        existing.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Avatar';
                        img.id = 'avatar-img';
                        preview.insertBefore(img, preview.firstChild);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeAvatar() {
            const input = document.getElementById('avatar-input');
            input.value = '';
            const img = document.getElementById('avatar-img');
            if (img) img.remove();
            const initials = document.getElementById('avatar-initials');
            if (initials) initials.style.display = 'flex';
        }

        function previewCCCD(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById(previewId);
                    container.innerHTML = `
                    <img src="${e.target.result}" alt="CCCD Preview" class="cccd-img">
                    <div class="cccd-overlay-btn">
                        <span class="material-symbols-outlined" style="font-size:16px;">edit</span> Thay ảnh
                    </div>
                `;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
