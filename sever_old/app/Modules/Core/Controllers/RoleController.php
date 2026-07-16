<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getAll();
        return view('core::role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->roleService->getAllPermissions();
        return view('core::role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->roleService->create(
            $request->only(['name', 'display_name', 'description']),
            $request->input('permissions', [])
        );

        return redirect()->route('admin.roles.index')
            ->with('success', 'Tạo vai trò thành công');
    }

    public function edit(int $id)
    {
        $role = $this->roleService->getById($id);
        $permissions = $this->roleService->getAllPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('core::role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->roleService->update(
            $id,
            $request->only(['name', 'display_name', 'description']),
            $request->input('permissions', [])
        );

        return redirect()->route('admin.roles.index')
            ->with('success', 'Cập nhật vai trò thành công');
    }

    public function destroy(int $id)
    {
        $this->roleService->delete($id);
        return redirect()->route('admin.roles.index')
            ->with('success', 'Xóa vai trò thành công');
    }
}
