<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Modules\Theme\Models\User;
use App\Mail\ResetPasswordOtpMail;

class ForgotPasswordController extends Controller
{
    public function showEmailForm()
    {
        return view('auth::theme.forgot-password.email');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'g-recaptcha-response' => 'required'
        ], [
            'email.exists' => 'Email này chưa được đăng ký trong hệ thống.',
            'g-recaptcha-response.required' => 'Vui lòng xác nhận bạn không phải là robot.'
        ]);

        // Lấy cấu hình từ database
        $sysSettings = \App\Modules\Core\Models\Setting::getAllGrouped();
        $secSettings = $sysSettings['security_tab'] ?? [];
        $secretKey = $secSettings['recaptcha_secret_key'] ?? '';
        
        // Bỏ qua check captcha nếu chưa cấu hình
        if ($secretKey != '') {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip()
            ]);

            $captchaResult = $response->json();
            if (empty($captchaResult['success'])) {
                return back()->withErrors(['g-recaptcha-response' => 'Xác thực Captcha thất bại. Vui lòng thử lại.'])->withInput();
            }
        }

        // Tạo mã OTP 6 số
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        
        // Lưu OTP vào Cache với thời hạn 10 phút
        Cache::put('password_reset_otp_' . $request->email, $otp, now()->addMinutes(10));

        // Gửi OTP qua Email
        try {
            Mail::to($request->email)->send(new ResetPasswordOtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Không thể gửi email OTP lúc này. Vui lòng thử lại sau.'])->withInput();
        }

        // Chuyển hướng sang trang nhập OTP
        return redirect()->route('password.otp.form', ['email' => $request->email])
                         ->with('success', 'Mã OTP đã được gửi đến email của bạn.');
    }

    public function showOtpForm(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('password.request');
        }
        return view('auth::theme.forgot-password.otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $cachedOtp = Cache::get('password_reset_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.'])->withInput();
        }

        // OTP hợp lệ, cấp token tạm thời để cho phép đổi mật khẩu
        $resetToken = \Illuminate\Support\Str::random(60);
        Cache::put('password_reset_token_' . $request->email, $resetToken, now()->addMinutes(15));
        
        // Xóa OTP cũ
        Cache::forget('password_reset_otp_' . $request->email);

        return redirect()->route('password.reset.form', ['email' => $request->email, 'token' => $resetToken]);
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        if (!$email || !$token) {
            return redirect()->route('password.request')->withErrors(['email' => 'Yêu cầu đổi mật khẩu không hợp lệ.']);
        }

        $cachedToken = Cache::get('password_reset_token_' . $email);
        if (!$cachedToken || $cachedToken !== $token) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên đổi mật khẩu đã hết hạn. Vui lòng yêu cầu lại.']);
        }

        return view('auth::theme.forgot-password.reset', compact('email', 'token'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cachedToken = Cache::get('password_reset_token_' . $request->email);
        
        if (!$cachedToken || $cachedToken !== $request->token) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên đổi mật khẩu đã hết hạn. Vui lòng yêu cầu lại.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Không tìm thấy người dùng.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa token
        Cache::forget('password_reset_token_' . $request->email);

        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công! Bạn có thể đăng nhập ngay bây giờ.');
    }
}
