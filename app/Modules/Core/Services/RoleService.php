<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Repositories\RoleRepository;

class RoleService
{
    protected RoleRepository $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function getAll()
    {
        return $this->roleRepo->getAll();
    }

    public function getById(int $id)
    {
        return $this->roleRepo->findById($id);
    }

    public function create(array $data, array $permissionIds = [])
    {
        $role = $this->roleRepo->create($data);
        if (!empty($permissionIds)) {
            $this->roleRepo->syncPermissions($role->id, $permissionIds);
        }
        return $role;
    }

    public function update(int $id, array $data, array $permissionIds = [])
    {
        $this->roleRepo->update($id, $data);
        $this->roleRepo->syncPermissions($id, $permissionIds);
        return $this->roleRepo->findById($id);
    }

    public function delete(int $id)
    {
        return $this->roleRepo->delete($id);
    }

    public function getAllPermissions()
    {
        return $this->roleRepo->getAllPermissions();
    }
}
