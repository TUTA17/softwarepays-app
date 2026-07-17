<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\Theme\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth::theme.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->two_factor_enabled) {
                // Generate OTP
                $otp = sprintf("%06d", mt_rand(100000, 999999));
                
                // Store in Cache instead of Session for persistence across redirects
                \Illuminate\Support\Facades\Cache::put('2fa_otp_' . $user->id, $otp, now()->addMinutes(10));
                
                // Store user ID in session temporarily
                $request->session()->put('2fa_user_id', $user->id);
                $request->session()->put('2fa_remember', $request->has('remember-me'));

                // Send email
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordOtpMail($otp)); // Reuse OTP mail
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Lỗi gửi email OTP 2FA: ' . $e->getMessage());
                }

                return redirect()->route('twofactor.verify')->with('success', 'Mã xác thực đã được gửi đến email của bạn.');
            }

            Auth::login($user, $request->has('remember-me'));
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request)
    {
        $ref = $request->query('ref');
        return view('auth::theme.register', compact('ref'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Vui lòng nhập tên hiển thị.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'terms.required' => 'Bạn phải đồng ý với Điều khoản và Chính sách bảo mật.',
            'terms.accepted' => 'Bạn phải đồng ý với Điều khoản và Chính sách bảo mật.',
        ]);

        // Logic xử lý người giới thiệu
        $referredBy = null;
        if ($request->ref) {
            $referrer = User::where('affiliate_code', $request->ref)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $hashedPassword = Hash::make($request->password);

        // --- REGISTER OTP VERIFICATION ---
        if (\App\Modules\Core\Models\Setting::getValue('verify_email_enable', '0') == '1') {
            $pending = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'referred_by' => $referredBy,
            ];

            $cacheKey = 'pending_register_' . sha1($request->email);
            $otp = sprintf('%06d', mt_rand(100000, 999999));
            \Illuminate\Support\Facades\Cache::put($cacheKey, $pending, now()->addMinutes(15));
            \Illuminate\Support\Facades\Cache::put($cacheKey . '_otp', $otp, now()->addMinutes(15));

            try {
                $tempUser = new User(['name' => $pending['name'], 'email' => $pending['email']]);
                \Illuminate\Support\Facades\Mail::to($pending['email'])->send(new \App\Mail\VerifyEmailMail($tempUser, $otp));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gửi email xác minh đăng ký thất bại: ' . $e->getMessage());
            }

            $request->session()->put('pending_register_email', $request->email);

            return redirect()->route('register.verify.form');
        }
        // ---------------------------------

        $user = $this->createUserAccount($request->name, $request->email, $hashedPassword, $referredBy, false);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function registerVerifyForm(Request $request)
    {
        $email = $request->session()->get('pending_register_email');

        if (!$email || !\Illuminate\Support\Facades\Cache::has('pending_register_' . sha1($email) . '_otp')) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn, vui lòng đăng ký lại.');
        }

        return view('auth::theme.register-verify', ['email' => $email]);
    }

    public function registerVerifyProcess(Request $request)
    {
        $email = $request->session()->get('pending_register_email');

        if (!$email) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn, vui lòng đăng ký lại.');
        }

        $cacheKey = 'pending_register_' . sha1($email);
        $pending = \Illuminate\Support\Facades\Cache::get($cacheKey);
        $cachedOtp = \Illuminate\Support\Facades\Cache::get($cacheKey . '_otp');
        $inputOtp = implode('', $request->input('otp', []));

        if (!$pending || !$cachedOtp || $inputOtp !== $cachedOtp) {
            return back()->with('error', 'Mã xác minh không chính xác hoặc đã hết hạn.');
        }

        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        \Illuminate\Support\Facades\Cache::forget($cacheKey . '_otp');
        $request->session()->forget('pending_register_email');

        $user = $this->createUserAccount($pending['name'], $pending['email'], $pending['password'], $pending['referred_by'], true);

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Xác minh email thành công! Tài khoản của bạn đã được tạo.');
    }

    public function registerVerifyResend(Request $request)
    {
        $email = $request->session()->get('pending_register_email');

        if (!$email) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn, vui lòng đăng ký lại.');
        }

        $cacheKey = 'pending_register_' . sha1($email);
        $pending = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$pending) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn, vui lòng đăng ký lại.');
        }

        $otp = sprintf('%06d', mt_rand(100000, 999999));
        \Illuminate\Support\Facades\Cache::put($cacheKey, $pending, now()->addMinutes(15));
        \Illuminate\Support\Facades\Cache::put($cacheKey . '_otp', $otp, now()->addMinutes(15));

        $tempUser = new User(['name' => $pending['name'], 'email' => $pending['email']]);
        \Illuminate\Support\Facades\Mail::to($pending['email'])->send(new \App\Mail\VerifyEmailMail($tempUser, $otp));

        return back()->with('success', 'Đã gửi lại mã xác minh, vui lòng kiểm tra hộp thư.');
    }

    /**
     * Tạo tài khoản mới + xử lý thưởng giới thiệu, email chào mừng, và thông báo admin.
     * Dùng chung cho cả đăng ký tức thì (verify_email_enable tắt) và sau khi xác minh OTP.
     */
    protected function createUserAccount(string $name, string $email, string $hashedPassword, ?int $referredBy, bool $emailVerified): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'affiliate_code' => strtoupper(Str::random(8)),
            'referred_by' => $referredBy,
            'balance' => 0,
            'points' => 0,
            'email_verified_at' => $emailVerified ? now() : null,
        ]);

        // Cộng tiền thưởng giới thiệu nếu có
        if ($referredBy) {
            $referrer = User::find($referredBy);
            if ($referrer) {
                $bonus = \App\Modules\Core\Models\Setting::getValue('referral_signup_bonus', 500);
                if ($bonus > 0) {
                    $referrer->increment('balance', $bonus);
                    \App\Modules\Theme\Models\Transaction::create([
                        'user_id' => $referrer->id,
                        'amount' => $bonus,
                        'type' => 'referral_bonus',
                        'status' => 'completed',
                        'description' => 'Thưởng giới thiệu bạn bè: ' . $user->name,
                        'reference_id' => $user->id
                    ]);
                }
            }
        }

        if (\App\Modules\Core\Models\Setting::getValue('welcome_email_enable', '1') == '1') {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gửi email chào mừng thất bại: ' . $e->getMessage());
            }
        }

        try {
            (new \App\Modules\Core\Services\WebPushService())->notifyAllAdmins(
                '👤 Tài khoản mới',
                $user->name . ' (' . $user->email . ') vừa đăng ký tài khoản.',
                route('admin.users')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Push notify (tài khoản mới) thất bại: ' . $e->getMessage());
        }

        try {
            (new \App\Modules\Core\Services\FcmService())->notifyAllAdmins(
                '👤 Tài khoản mới',
                $user->name . ' (' . $user->email . ') vừa đăng ký tài khoản.',
                route('admin.users')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('FCM notify (tài khoản mới) thất bại: ' . $e->getMessage());
        }

        return $user;
    }

    public function verifyEmailForm()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        return view('auth::theme.verify-email');
    }

    public function verifyEmailProcess(Request $request)
    {
        $user = Auth::user();
        $inputOtp = implode('', $request->input('otp', []));
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('verify_email_otp_' . $user->id);

        if (!$cachedOtp || $inputOtp !== $cachedOtp) {
            return back()->with('error', 'Mã xác minh không chính xác hoặc đã hết hạn.');
        }

        \Illuminate\Support\Facades\Cache::forget('verify_email_otp_' . $user->id);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Xác minh địa chỉ email thành công!');
    }

    public function verifyEmailResend()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $verifyOtp = sprintf('%06d', mt_rand(100000, 999999));
        \Illuminate\Support\Facades\Cache::put('verify_email_otp_' . $user->id, $verifyOtp, now()->addMinutes(15));
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifyEmailMail($user, $verifyOtp));

        return back()->with('success', 'Đã gửi lại mã xác minh, vui lòng kiểm tra hộp thư.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
