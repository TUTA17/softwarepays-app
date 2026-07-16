<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Modules\Theme\Models\User;

class TwoFactorController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth::theme.2fa');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $userId = $request->session()->get('2fa_user_id');
        $cachedOtp = Cache::get('2fa_otp_' . $userId);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'Mã xác thực không chính xác hoặc đã hết hạn.']);
        }

        // Đăng nhập thành công
        $user = User::find($userId);
        $remember = $request->session()->get('2fa_remember', false);
        
        Auth::login($user, $remember);
        $request->session()->regenerate();
        
        // Xóa cache và session
        Cache::forget('2fa_otp_' . $userId);
        $request->session()->forget(['2fa_user_id', '2fa_remember']);

        return redirect()->intended('dashboard');
    }
}
