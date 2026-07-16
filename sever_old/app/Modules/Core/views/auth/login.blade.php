<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    @php
        $loginFavicon = \App\Modules\Core\Models\Setting::where('name','favicon')->where('type','general_tab')->value('value');
        $loginSiteName = \App\Modules\Core\Models\Setting::where('name','name')->where('type','general_tab')->value('value') ?: config('app.name');
    @endphp
    <title>{{ $loginSiteName }} | Đăng nhập</title>
    <link rel="icon" href="{{ $loginFavicon ? asset($loginFavicon) : asset('login/images/logo_large.png') }}" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () { sessionStorage.fonts = true; }
        });
    </script>

    <link rel="stylesheet" href="{{ asset('login/css/vendors.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('login/css/style.bundle.css') }}">

    <style>
        a.with-smedia {
            color: #fff; display: inline-block; font-weight: normal;
            margin: 10px auto 0; padding: 10px 30px; text-transform: capitalize;
            border-radius: 30px; font-size: 12px;
        }
        a.facebook { background: #516eab; }
        a.google { background: #dd4b39; }
    </style>
</head>

<body class="hold-transition login-page m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-1"
         id="m_login" style="background-image: url({{ asset('/login/images/bg-1.jpg') }}) !important;">
        <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <a href="#">
                        @php $loginLogo = \App\Modules\Core\Models\Setting::where('name','logo')->where('type','general_tab')->value('value'); @endphp
                        <img src="{{ $loginLogo ? asset($loginLogo) : asset('login/images/logo_large.png') }}" alt="{{ config('app.name') }}" style="max-height: 60px;">
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert bg-success text-white" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert text-center text-danger" role="alert" style="margin: 0; font-size: 16px;">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if(Session::has('message'))
                    <div class="alert text-center text-danger" role="alert" style="margin: 0; font-size: 16px;">
                        {{ Session::get('message') }}
                    </div>
                @endif

                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title">Đăng nhập hệ thống</h3>
                    </div>
                    <form method="POST" class="m-login__form m-form" action="{{ route('admin.login.post') }}">
                        @csrf
                        <div class="form-group m-form__group has-feedback">
                            <input style="color: #000 !important;" type="text" name="email" class="form-control m-input"
                                   placeholder="Email hoặc điện thoại" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="form-group m-form__group has-feedback">
                            <input style="color: #000 !important;" type="password" name="password"
                                   class="form-control m-input m-login__form-input--last" placeholder="Mật khẩu" required>
                        </div>
                        <div class="row m-login__form-sub">
                            <div class="col m--align-left m-login__form-left">
                                <label class="m-checkbox m-checkbox--light">
                                    <input type="checkbox" name="remember_account"> Ghi nhớ đăng nhập
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <button type="submit"
                                    class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">
                                Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</body>
</html>
