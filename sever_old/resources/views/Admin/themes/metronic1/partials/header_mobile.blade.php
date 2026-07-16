<style>
    /* Trên mobile (màn < 768px) */
    @media (max-width: 767px) {
        .kt-header__topbar-user img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-left: 10px;

        }

        /* Nếu dùng avatar dạng chữ thay cho ảnh */
        .kt-header__topbar-user .kt-badge--username {
            width: 32px;
            height: 32px;
            line-height: 32px;
            font-size: 14px;
            margin-top: 10px;
        }

    }
</style>
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <a href="/admin/dashboard">
            {{--            <img alt="{{ @$settings['name'] }}" class="lazy" data-src="{{ asset(@\Auth::guard('admin')->user()->image,35,null) }}" style="max-width:30px; max-height: 30px;">--}}
            
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        {{-- Nút toggle aside --}}
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler">
            <span></span>
        </button>
        {{--         <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>--}}

        {{-- User menu cho mobile --}}
        @include('Admin.themes.metronic1.partials.user_bar')
    </div>
</div>