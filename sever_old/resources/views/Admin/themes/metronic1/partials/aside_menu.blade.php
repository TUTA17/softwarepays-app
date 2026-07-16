<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
    <div id="kt_aside_menu" class="kt-aside-menu" data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
        <ul class="kt-menu__nav">
            
            <li class="kt-menu__section ">
                <h4 class="kt-menu__section-text">Quản Lý KeyGame</h4>
                <i class="kt-menu__section-icon flaticon-more-v2"></i>
            </li>

            <!-- Dashboard -->
            <li class="kt-menu__item" aria-haspopup="true">
                <a href="{{ route('admin.dashboard') }}" class="kt-menu__link">
                    <span class="kt-menu__link-icon"><i class="flaticon-line-graph"></i></span>
                    <span class="kt-menu__link-text">Tổng Quan</span>
                </a>
            </li>

            <!-- Products -->
            <li class="kt-menu__item" aria-haspopup="true">
                <a href="{{ route('admin.products') }}" class="kt-menu__link">
                    <span class="kt-menu__link-icon"><i class="flaticon-app"></i></span>
                    <span class="kt-menu__link-text">Sản Phẩm (Games)</span>
                </a>
            </li>

            <!-- Keys -->
            <li class="kt-menu__item" aria-haspopup="true">
                <a href="{{ route('admin.keys') }}" class="kt-menu__link">
                    <span class="kt-menu__link-icon"><i class="flaticon-safe-shield-protection"></i></span>
                    <span class="kt-menu__link-text">Kho Key</span>
                </a>
            </li>

            <!-- Users -->
            <li class="kt-menu__item" aria-haspopup="true">
                <a href="{{ route('admin.users') }}" class="kt-menu__link">
                    <span class="kt-menu__link-icon"><i class="flaticon-users"></i></span>
                    <span class="kt-menu__link-text">Khách Hàng</span>
                </a>
            </li>

            <li class="kt-menu__section ">
                <h4 class="kt-menu__section-text">Tài Khoản</h4>
                <i class="kt-menu__section-icon flaticon-more-v2"></i>
            </li>

            <li class="kt-menu__item" aria-haspopup="true">
                <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
                <a href="#" onclick="document.getElementById('logout-form').submit();" class="kt-menu__link">
                    <span class="kt-menu__link-icon"><i class="flaticon-logout"></i></span>
                    <span class="kt-menu__link-text">Đăng Xuất</span>
                </a>
            </li>

        </ul>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        <?php $uri = "$_SERVER[REQUEST_URI]";?>
        $("#kt_aside_menu a[href='{{ $uri }}']").parents('li').addClass('kt-menu__item--active');
        $("#kt_aside_menu a[href='{{ $uri }}']").parents('li').parents('li')
            .addClass('kt-menu__item--here kt-menu__item--open')
            .removeClass('kt-menu__item--active');
        $("#kt_aside_menu a[href='{{ $uri }}']").parents('li').parents('li')
            .find('.kt-menu__submenu').attr('style', '');
    });
</script>
