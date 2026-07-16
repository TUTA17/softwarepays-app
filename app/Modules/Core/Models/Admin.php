<?php

namespace App\Modules\Core\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';

    protected $fillable = [
        'name', 'email', 'password', 'image', 'status', 'super_admin',
        'tel', 'address', 'intro', 'cccd', 'gioitinh', 'birthday',
        'facebook', 'zalo', 'skype',
        'ID_card_photo_on_the_front', 'ID_card_photo_on_the_back',
    ];

    protected $hidden = ['password', 'remember_token', 'password_md5'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_admin', 'admin_id', 'role_id');
    }

    /**
     * Relationship trực tiếp qua cột role_id (dùng cho hiển thị)
     */
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_admin', 'admin_id', 'role_id');
    }



    /**
     * Kiểm tra admin có quyền cụ thể không
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permissionName) {
                $q->where('name', $permissionName);
            })->exists();
    }

    /**
     * Lấy tất cả tên quyền của admin
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $perm) {
                $permissions[] = $perm->name;
            }
        }
        return array_unique($permissions);
    }
}
