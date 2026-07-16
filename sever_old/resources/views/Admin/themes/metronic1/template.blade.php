<!DOCTYPE html>
<html lang="vi">
<head>
    @include('Admin.themes.metronic1.partials.head_meta')
    @include('Admin.themes.metronic1.partials.head_script')

</head>
<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
@include('Admin.themes.metronic1.partials.header_mobile')
<!-- end:: Header Mobile -->

<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <!-- begin:: Aside -->
        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>

        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
             id="kt_aside">
            <!-- begin:: Aside -->
            @include('Admin.themes.metronic1.partials.aside')
            <!-- end:: Aside -->
            <!-- begin:: Aside Menu -->
            @include('Admin.themes.metronic1.partials.aside_menu')
            <!-- end:: Aside Menu -->
        </div>
        <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper"
             style="position: relative">
            {{--@include('Admin.themes.metronic1.partials.check_exp_date')--}}

            <!-- begin:: Header -->
            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

                <div class="page-title">
                    {{ isset($page_title) ? trans(@$page_title) : '' }}
                </div>

                @include('Admin.themes.metronic1.partials.nv_xuat_sac')

                <!-- begin: Header Menu -->
                @include('Admin.themes.metronic1.partials.header_menu')
                <!-- end: Header Menu -->

                <!-- begin:: Header Topbar -->
                <style>
                    .user-bar-desktop {
                        display: block;
                    }
                    /* Trên mobile (< 768px) thì ẩn */
                    @media (max-width: 767px) {
                        .user-bar-desktop,.kt-header__topbar {
                            display: none;
                        }

                    }
                </style>
                <div class="kt-header__topbar">
                    <!--begin: BillPayment Bar -->

                    <div class="user-bar-desktop">
                        @include('Admin.themes.metronic1.partials.user_bar')
                    </div>
                    <!--end: BillPayment Bar -->
                </div>
                <!-- end:: Header Topbar -->
            </div>
            <!-- end:: Header -->

            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                
                        <!-- begin:: Content -->
                @yield('main')
                <!-- end:: Content -->
            </div>

            <!-- begin:: Footer -->
            @include('Admin.themes.metronic1.partials.footer')
            <!-- end:: Footer -->
        </div>
    </div>
</div>
<!-- end:: Page -->

@include('Admin.themes.metronic1.modal.blank_modal')
@include('Admin.themes.metronic1.modal.delete_warning_modal')
@include('Admin.themes.metronic1.modal.confirm_action_modal')
@include('Admin.themes.metronic1.modal.something_went_wrong')

@include('Admin.themes.metronic1.partials.footer_script')

</body>
<!-- end::Body -->
</html>
