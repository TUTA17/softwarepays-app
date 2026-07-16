<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <title>KeyGame | Đăng nhập Admin</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!--end::Web font -->

    <!--begin::Global Theme Styles -->
    <link rel="stylesheet" href="{{asset('auth_assets/css/vendors.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('auth_assets/css/vendors.bundle.rtl.css')}}">

    <link rel="stylesheet" href="{{asset('auth_assets/css/style.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('libs/bootstrap/js/bootstrap.min.js')}}">

    <!--end::Global Theme Styles -->
    <style>
        a.with-smedia {
            color: #fff;
            display: inline-block;
            font-weight: normal;
            margin: 10px auto 0;
            padding: 10px 30px;
            text-transform: capitalize;
            border-radius: 30px;
            font-size: 12px;
        }

        a.facebook {
            background: #516eab;
        }

        a.google {
            background: #dd4b39;
        }
    </style>
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="hold-transition login-page m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-1"
         id="m_login" style="background-image: url({{ asset('/auth_assets/images/bg-1.jpg') }}) !important;">
        <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <h1 style="color: white; font-size: 3rem; font-weight: bold;">KEYGAME</h1>
                </div>
                
                @if (session('error'))
                    <div class="alert bg-danger text-white text-center" role="alert">
                        {!!session('error')!!}
                    </div>
                @endif
                
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title">Đăng Nhập Quản Trị</h3>
                    </div>
                    <form method="post" class="m-login__form m-form" action="{{ route('admin.login.submit') }}">
                        @csrf
                        <div class="form-group m-form__group has-feedback">
                            <input style="color: #000 !important;" type="text" name="email" class="form-control m-input"
                                   placeholder="Email đăng nhập" required>
                        </div>
                        <div class="form-group m-form__group has-feedback">
                            <input style="color:#000 !important;" type="password" name="password"
                                   class="form-control m-input m-login__form-input--last" placeholder="Mật khẩu" required>
                        </div>
                        <div class="row m-login__form-sub">
                            <div class="col m--align-left m-login__form-left">
                                <label class="m-checkbox  m-checkbox--light">
                                    <input type="checkbox"
                                           name="remember"> Ghi nhớ
                                    <span></span>
                                </label>
                            </div>
                            <div class="col m--align-right m-login__form-right">
                                <a href="#" id="m_login_forget_password"
                                   class="m-link">Quên mật khẩu?</a>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <button type="submit"
                                    class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primary">ĐĂNG NHẬP</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- end:: Page -->

<!--begin::Page Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="{{asset('libs/bootstrap/js/bootstrap.min.js')}}"></script>

<!--end::Page Scripts -->
</body>

<!-- end::Body -->
</html>
