<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    @php
        $loginFavicon = \App\Modules\Core\Models\Setting::where('name','favicon')->where('type','general_tab')->value('value');
        $loginSiteName = \App\Modules\Core\Models\Setting::where('name','name')->where('type','general_tab')->value('value') ?: config('app.name');
        $loginLogo = \App\Modules\Core\Models\Setting::where('name','logo')->where('type','general_tab')->value('value');
    @endphp
    <title>{{ $loginSiteName }} | Admin Portal</title>
    <link rel="icon" href="{{ $loginFavicon ? asset($loginFavicon) : asset('auth_assets/login/images/logo_large.png') }}" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body {
            background-color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        /* Animated Background Elements */
        .bg-element {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: pulse 8s infinite alternate ease-in-out;
        }
        .bg-1 {
            width: 500px; height: 500px;
            background: #e11d48; /* Cyberpunk Rose/Red for Admin */
            top: -150px; left: -150px;
        }
        .bg-2 {
            width: 600px; height: 600px;
            background: #4f46e5; /* Indigo */
            bottom: -200px; right: -200px;
            animation-delay: -4s;
        }

        /* Abstract Cyber Grid overlay */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 1;
        }

        @keyframes pulse {
            0% { transform: scale(1) translate(0, 0); opacity: 0.4; }
            100% { transform: scale(1.2) translate(30px, -30px); opacity: 0.6; }
        }

        /* Glassmorphism Card */
        .login-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
            z-index: 10;
            position: relative;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
        }
        .logo-container img {
            max-height: 50px;
            filter: drop-shadow(0 0 10px rgba(255,255,255,0.1));
        }
        
        .title {
            color: #fff;
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .subtitle {
            color: #94a3b8;
            text-align: center;
            font-size: 14px;
            margin-bottom: 35px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error {
            background: rgba(225, 29, 72, 0.1);
            border: 1px solid rgba(225, 29, 72, 0.3);
            color: #fb7185;
        }

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }
        .input-group label {
            display: block;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }
        .input-group:focus-within label {
            color: #e11d48;
        }

        .input-control {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 16px;
            padding-left: 48px;
            border-radius: 14px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }
        .input-control::placeholder { color: #64748b; }
        .input-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #e11d48;
            box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.15);
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            bottom: 15px;
            color: #64748b;
            transition: color 0.3s ease;
        }
        .input-group:focus-within .input-icon {
            color: #e11d48;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 35px;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }
        .checkbox-container input {
            margin-right: 12px;
            accent-color: #e11d48;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(to right, #e11d48, #be123c);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px -10px rgba(225, 29, 72, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            transition: all 0.7s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(225, 29, 72, 0.8);
        }
        .btn-submit:hover::after {
            left: 200%;
        }
        .btn-submit svg {
            transition: transform 0.3s ease;
        }
        .btn-submit:hover svg {
            transform: translateX(4px);
        }

        /* LIGHT MODE OVERRIDES */
        @media (prefers-color-scheme: light) {
            body { background-color: #f8fafc; }
            .grid-overlay {
                background-image: 
                    linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
            }
            .login-card {
                background: rgba(255, 255, 255, 0.7);
                border: 1px solid rgba(0, 0, 0, 0.1);
                box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1);
            }
            .title { color: #0f172a; }
            .subtitle { color: #475569; }
            .input-group label { color: #334155; }
            .input-control {
                background: rgba(255, 255, 255, 0.8);
                border: 1px solid rgba(0, 0, 0, 0.1);
                color: #0f172a;
            }
            .input-control::placeholder { color: #94a3b8; }
            .input-control:focus {
                background: #fff;
                border-color: #e11d48;
                box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.1);
            }
            .checkbox-container { color: #475569; }
        }

    </style>
</head>
<body>
    <div class="grid-overlay"></div>
    <div class="bg-element bg-1"></div>
    <div class="bg-element bg-2"></div>

    <div class="login-card">
        <div class="logo-container">
            <img src="{{ $loginLogo ? asset($loginLogo) : asset('auth_assets/login/images/logo_large.png') }}" alt="{{ $loginSiteName }}">
        </div>

        <h1 class="title">ADMIN PORTAL</h1>
        <p class="subtitle">Truy cập khu vực quản lý cấp cao</p>

        @if($errors->any() || Session::has('message'))
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <div>
                    @if(Session::has('message'))
                        {{ Session::get('message') }}
                    @else
                        @foreach($errors->all() as $error)
                            <div style="margin-bottom: 2px;">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="input-group">
                <label for="email">Tài Khoản / Email</label>
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <input type="text" id="email" name="email" class="input-control" placeholder="admin@domain.com" value="{{ old('email') }}" required autofocus autocomplete="off">
            </div>

            <div class="input-group">
                <label for="password">Mật Khẩu</label>
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <input type="password" id="password" name="password" class="input-control" placeholder="••••••••" required>
            </div>

            <label class="checkbox-container">
                <input type="checkbox" name="remember_account">
                Ghi nhớ đăng nhập trên thiết bị này
            </label>

            <button type="submit" class="btn-submit">
                ĐĂNG NHẬP 
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </form>
    </div>
</body>
</html>
