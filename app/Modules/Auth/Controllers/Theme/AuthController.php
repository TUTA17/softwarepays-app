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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'affiliate_code' => strtoupper(Str::random(8)),
            'referred_by' => $referredBy,
            'balance' => 0,
            'points' => 0,
        ]);

        // Cộng tiền thưởng giới thiệu nếu có
        if ($referredBy) {
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

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
