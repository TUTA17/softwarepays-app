<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Models\Admin;

class AdminRepository
{
    public function findById(int $id): ?Admin
    {
        return Admin::find($id);
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }

    public function findByEmailOrPhone(string $value): ?Admin
    {
        return Admin::where('email', $value)->orWhere('tel', $value)->first();
    }

    public function getAll(array $filters = [], int $perPage = 20)
    {
        $query = Admin::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('tel', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role_id'])) {
            $query->whereHas('roles', fn($q) => $q->where('roles.id', $filters['role_id']));
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Admin::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Admin::where('id', $id)->delete();
    }
}
