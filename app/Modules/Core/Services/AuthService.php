<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Repositories\AdminRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthService
{
    protected AdminRepository $adminRepo;

    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    /**
     * Đăng nhập bằng email/phone + password
     * (Giống logic cũ: dùng Auth::attempt(), hỗ trợ cả email lẫn SĐT)
     */
    public function login(string $emailOrPhone, string $password, bool $remember = false): array
    {
        // Tìm admin bằng email hoặc SĐT (giống old code: orWhere('tel', ...))
        $admin = $this->adminRepo->findByEmailOrPhone($emailOrPhone);

        if (!$admin) {
            return ['success' => false, 'message' => 'Email hoặc số điện thoại không tồn tại'];
        }

        // Kiểm tra trạng thái tài khoản (giống old code)
        if ($admin->status == 0) {
            return ['success' => false, 'message' => 'Tài khoản chưa được kích hoạt'];
        }

        if ($admin->status == -1) {
            return ['success' => false, 'message' => 'Tài khoản đã bị khóa'];
        }

        // Thử login bằng email
        $loggedIn = Auth::guard('admin')->attempt(
            ['email' => trim($emailOrPhone), 'password' => trim($password)],
            $remember
        );

        // Nếu không login bằng email thì thử bằng SĐT (giống old code line 73)
        if (!$loggedIn) {
            $loggedIn = Auth::guard('admin')->attempt(
                ['tel' => trim($emailOrPhone), 'password' => trim($password)],
                $remember
            );
        }

        if (!$loggedIn) {
            return ['success' => false, 'message' => 'Mật khẩu không đúng'];
        }

        // Load permissions vào session
        $admin = Auth::guard('admin')->user();
        $permissions = $admin->getAllPermissions();
        Session::put('permissions', $permissions);
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->name);

        return ['success' => true, 'admin' => $admin];
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
        Session::flush();
    }

    public function getCurrentAdmin()
    {
        return Auth::guard('admin')->user();
    }
}
