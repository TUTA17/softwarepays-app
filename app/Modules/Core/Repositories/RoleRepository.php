<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\Permission;

class RoleRepository
{
    public function getAll()
    {
        return Role::with('permissions')->orderBy('id', 'asc')->get();
    }

    public function findById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Role::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Role::where('id', $id)->delete();
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
    }

    public function getAllPermissions()
    {
        return Permission::orderBy('id', 'asc')->get();
    }
}
