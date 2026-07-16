<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\Theme\Models\GameKey;
use App\Modules\Theme\Models\Transaction;

class UserController extends Controller
{
    /**
     * Hiển thị Kho Game Của Tôi
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Lấy danh sách key game đã mua
        $gameKeys = GameKey::with('product')
            ->where('sold_to_user_id', $user->id)
            ->orderBy('sold_at', 'desc')
            ->paginate(12);
            
        // Thống kê
        $totalGames = GameKey::where('sold_to_user_id', $user->id)->count();
        $totalSpent = Transaction::where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('status', 'completed')
            ->sum('amount');
            
        return view('dashboard', compact('gameKeys', 'totalGames', 'totalSpent'));
    }

    /**
     * Lịch sử giao dịch (có thể để ở route riêng hoặc tích hợp)
     */
    public function transactions()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('profile.transactions', compact('transactions'));
    }

    /**
     * Hiển thị form cài đặt tài khoản
     */
    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        // Cập nhật tên, email
        $user->name = $request->name;
        $user->email = $request->email;
        $user->two_factor_enabled = $request->has('two_factor_enabled') ? 1 : 0;

        // Cập nhật Avatar từ chuỗi Base64 (Cropper)
        if ($request->filled('avatar_base64')) {
            $base64_image = $request->input('avatar_base64');
            
            // Xử lý chuỗi base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                $base64_image = substr($base64_image, strpos($base64_image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $base64_image = base64_decode($base64_image);
                    
                    if ($base64_image !== false) {
                        $filename = time() . '_' . uniqid() . '.jpg'; // Cropper xuất jpeg
                        $path = public_path('uploads/avatars');
                        
                        if (!file_exists($path)) {
                            mkdir($path, 0755, true);
                        }
                        
                        file_put_contents($path . '/' . $filename, $base64_image);
                        $user->avatar = 'uploads/avatars/' . $filename;
                    }
                }
            }
        } elseif ($request->hasFile('avatar')) {
            // Dự phòng trường hợp trình duyệt cũ không hỗ trợ JS
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
        }

        // Cập nhật Mật khẩu
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Mật khẩu hiện tại không chính xác!');
            }
            $user->password = Hash::make($request->new_password);
        }
        
        $user->two_factor_enabled = $request->has('two_factor_enabled') ? true : false;
        $user->checkout_otp_enabled = $request->has('checkout_otp_enabled') ? true : false;

        $user->save();

        return back()->with('success', 'Cập nhật cài đặt thành công!');
    }

    /**
     * Hiển thị trang Quản lý Giới thiệu bạn bè
     */
    public function referrals()
    {
        $user = Auth::user();
        
        // Ensure user has an affiliate code
        if (empty($user->affiliate_code)) {
            $user->affiliate_code = strtoupper(\Illuminate\Support\Str::random(8));
            $user->save();
        }

        $referrals = \App\Models\User::where('referred_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $totalCommission = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['affiliate_reward', 'referral_bonus'])
            ->where('status', 'completed')
            ->sum('amount');
            
        $commissionPercent = \App\Modules\Core\Models\Setting::getValue('affiliate_commission', 5);
        $signupBonus = \App\Modules\Core\Models\Setting::getValue('referral_signup_bonus', 500);
            
        return view('profile.referral', compact('user', 'referrals', 'totalCommission', 'commissionPercent', 'signupBonus'));
    }
}
