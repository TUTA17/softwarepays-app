<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    protected AdminRepository $adminRepo;

    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    public function getList(array $filters = [], int $perPage = 20)
    {
        return $this->adminRepo->getAll($filters, $perPage);
    }

    public function getById(int $id)
    {
        return $this->adminRepo->findById($id);
    }

    public function create(array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->adminRepo->create($data);
    }

    public function update(int $id, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->adminRepo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->adminRepo->delete($id);
    }
}
