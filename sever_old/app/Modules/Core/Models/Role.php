<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name', 'display_name', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'role_admin', 'role_id', 'admin_id');
    }
}
