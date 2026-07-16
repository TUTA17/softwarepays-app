<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\AdminService;
use App\Modules\Core\Services\RoleService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected AdminService $adminService;
    protected RoleService $roleService;

    public function __construct(AdminService $adminService, RoleService $roleService)
    {
        $this->adminService = $adminService;
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role_id', 'status']);
        $admins = $this->adminService->getList($filters);
        $roles = $this->roleService->getAll();

        return view('core::admin.index', compact('admins', 'roles', 'filters'));
    }

    public function create()
    {
        $roles = $this->roleService->getAll();
        return view('core::admin.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|min:6',
        ]);

        $admin = $this->adminService->create($request->all());

        // Gán role
        if ($request->has('role_id')) {
            $admin->roles()->sync([$request->role_id]);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Thêm nhân viên thành công');
    }

    public function edit(int $id)
    {
        $admin = $this->adminService->getById($id);
        $roles = $this->roleService->getAll();

        return view('core::admin.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $id,
        ]);

        $this->adminService->update($id, $request->all());

        $admin = $this->adminService->getById($id);
        if ($request->has('role_id')) {
            $admin->roles()->sync([$request->role_id]);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy(int $id)
    {
        $this->adminService->delete($id);
        return redirect()->route('admin.admins.index')
            ->with('success', 'Xóa thành công');
    }

    /**
     * Trang Profile cá nhân (self-edit)
     */
    public function profile()
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        $admin->load('roles');
        $roles = $this->roleService->getAll();
        
        return view('core::admin.profile', compact('admin', 'roles'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function profileUpdate(Request $request)
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $admin->id,
            'image' => 'nullable|image|max:2048',
            'ID_card_photo_on_the_front' => 'nullable|image|max:51  20',
            'ID_card_photo_on_the_back'  => 'nullable|image|max:5120',
        ]);

        $data = $request->only([
            'name', 'email', 'tel', 'address', 'intro',
            'cccd', 'gioitinh', 'birthday', 'facebook', 'zalo', 'skype',
        ]);

        // Upload avatar
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'avatar_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['image'] = 'uploads/avatars/' . $filename;
        }

        // Upload CCCD mặt trước
        if ($request->hasFile('ID_card_photo_on_the_front')) {
            $file = $request->file('ID_card_photo_on_the_front');
            $filename = 'cccd_front_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cccd'), $filename);
            $data['ID_card_photo_on_the_front'] = 'uploads/cccd/' . $filename;
        }

        // Upload CCCD mặt sau
        if ($request->hasFile('ID_card_photo_on_the_back')) {
            $file = $request->file('ID_card_photo_on_the_back');
            $filename = 'cccd_back_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cccd'), $filename);
            $data['ID_card_photo_on_the_back'] = 'uploads/cccd/' . $filename;
        }

        $this->adminService->update($admin->id, $data);

        // Update session name
        session(['admin_name' => $data['name']]);

        return redirect()->route('admin.profile')
            ->with('success', 'Cập nhật thông tin cá nhân thành công');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'new_password.confirmed'    => 'Xác nhận mật khẩu không khớp',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng');
        }

        $this->adminService->update($admin->id, [
            'password' => $request->new_password,
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Đổi mật khẩu thành công');
    }
}
