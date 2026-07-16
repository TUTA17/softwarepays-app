<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\Client\Models\GameKey;
use App\Modules\Client\Models\Transaction;

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

        // Cập nhật Avatar
        if ($request->hasFile('avatar')) {
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

        $user->save();

        return back()->with('success', 'Đã cập nhật thông tin tài khoản thành công!');
    }
}
