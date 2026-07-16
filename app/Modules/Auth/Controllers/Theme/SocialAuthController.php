<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng sang trang đăng nhập của Provider (Google, Github)
     */
    public function redirect($provider)
    {
        // Kiểm tra xem provider có được hỗ trợ hay không
        if (!in_array($provider, ['google', 'github'])) {
            return redirect()->route('login')->with('error', 'Nền tảng đăng nhập không được hỗ trợ!');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Chưa cấu hình API cho nền tảng này: ' . $e->getMessage());
        }
    }

    /**
     * Nhận phản hồi từ Provider (Google, Github) và xử lý đăng nhập/đăng ký
     */
    public function callback($provider)
    {
        try {
            // Lấy thông tin user từ Socialite
            $socialUser = Socialite::driver($provider)->user();

            // Kiểm tra xem email đã tồn tại trong hệ thống chưa
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Nếu tài khoản đã tồn tại nhưng không cùng nền tảng đăng nhập (Ví dụ: Đã đăng ký thường, hoặc đã đăng nhập bằng Github nhưng giờ lại bấm Google)
                if ($user->provider !== $provider) {
                    if (empty($user->provider)) {
                        return redirect()->route('login')->with('error', 'Email này đã được đăng ký tài khoản thường trước đó. Vui lòng đăng nhập bằng Mật khẩu!');
                    } else {
                        return redirect()->route('login')->with('error', 'Email này đã được liên kết với ' . ucfirst($user->provider) . ' trước đó. Vui lòng đăng nhập bằng ' . ucfirst($user->provider) . '!');
                    }
                }

                // Nếu đúng nền tảng cũ, cập nhật lại thông tin avatar nếu bị trống
                $user->update([
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $user->avatar ?? $socialUser->getAvatar(),
                ]);
            } else {
                // Tạo mới tài khoản
                $user = clone new User;
                $user->name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'User_' . Str::random(5);
                $user->email = $socialUser->getEmail();
                $user->provider = $provider;
                $user->provider_id = $socialUser->getId();
                $user->avatar = $socialUser->getAvatar();
                $user->balance = 0; // Tài khoản mới
                $user->role = 'user';
                $user->status = 'active';
                $user->save();
            }

            // Thực hiện đăng nhập, hỗ trợ "Nhớ mật khẩu" (Remember me)
            Auth::login($user, true);

            return redirect()->route('home')->with('success', 'Đăng nhập thành công qua ' . ucfirst($provider));
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập thất bại: ' . $e->getMessage());
        }
    }
}
