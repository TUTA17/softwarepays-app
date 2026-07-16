<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Modules\Theme\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Modules\Core\Models\Setting;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Configure Socialite dynamically from Database settings
     */
    private function configureProvider($provider)
    {
        $sysSettings = Setting::getAllGrouped();
        $socialSettings = $sysSettings['social_login_tab'] ?? [];

        $isEnabled = $socialSettings[$provider . '_login_enable'] ?? '0';
        if ($isEnabled !== '1') {
            return false;
        }

        $clientId = $socialSettings[$provider . '_client_id'] ?? '';
        $clientSecret = $socialSettings[$provider . '_client_secret'] ?? '';

        if (empty($clientId) || empty($clientSecret)) {
            return false;
        }

        Config::set("services.{$provider}.client_id", $clientId);
        Config::set("services.{$provider}.client_secret", $clientSecret);
        Config::set("services.{$provider}.redirect", url("/login/{$provider}/callback"));

        return true;
    }

    /**
     * Redirect to the provider's OAuth page
     */
    public function redirect($provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            abort(404);
        }

        if (!$this->configureProvider($provider)) {
            return redirect()->route('login')->with('error', "Chức năng đăng nhập bằng " . ucfirst($provider) . " đang tạm khóa hoặc chưa cấu hình.");
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the provider
     */
    public function callback($provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            abort(404);
        }

        if (!$this->configureProvider($provider)) {
            return redirect()->route('login')->with('error', "Đăng nhập thất bại. Chức năng đăng nhập bằng " . ucfirst($provider) . " chưa được cấu hình.");
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Không thể xác thực từ ' . ucfirst($provider));
        }

        // Tìm user theo provider ID
        $user = User::where($provider . '_id', $socialUser->getId())->first();

        // Nếu không thấy, tìm theo email
        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
            
            // Cập nhật provider ID nếu tìm thấy user theo email
            if ($user) {
                $user->update([
                    $provider . '_id' => $socialUser->getId(),
                    'avatar_url' => $user->avatar_url ?? $socialUser->getAvatar()
                ]);
            }
        }

        // Nếu vẫn không thấy, tạo mới user
        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User_' . Str::random(6),
                'email' => $socialUser->getEmail(),
                'password' => null, // Không cần password cho social login
                $provider . '_id' => $socialUser->getId(),
                'avatar_url' => $socialUser->getAvatar(),
            ]);

            // Bạn có thể thêm logic tặng số dư vào ví (Wallet) khi đăng ký mới ở đây

            try {
                (new \App\Modules\Core\Services\WebPushService())->notifyAllAdmins(
                    '👤 Tài khoản mới',
                    $user->name . ' (' . $user->email . ') vừa đăng ký qua ' . ucfirst($provider) . '.',
                    route('admin.users')
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Push notify (tài khoản mới - social) thất bại: ' . $e->getMessage());
            }

            try {
                (new \App\Modules\Core\Services\FcmService())->notifyAllAdmins(
                    '👤 Tài khoản mới',
                    $user->name . ' (' . $user->email . ') vừa đăng ký qua ' . ucfirst($provider) . '.',
                    route('admin.users')
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('FCM notify (tài khoản mới - social) thất bại: ' . $e->getMessage());
            }
        }

        // Đăng nhập
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
    }
}
