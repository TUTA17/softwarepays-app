<?php

namespace App\Modules\Core\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // QUAN TRỌNG: Phải khớp với tên bảng trong database là 'users'
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'address', 'tel', 'image',
        'gender', 'birthday', 'api_token', 'password_md5',
        'facebook_id', 'google_id', 'sale_id', 'invite_by',
        'room_id', 'may_cham_cong_id', 'status', 'admin_id',
        'company_id', 'company_ids', 'last_company_id',
        'short_name', 'note', 'intro', 'code',
        'facebook', 'skype', 'zalo', 'gmail',
        'province_id', 'district_id', 'ward_id',
        'super_admin', 'work_time', 'classify',
        'maximum_projects', 'date_start_work',
        // Các trường bổ sung từ hình ảnh Adminer
        'remember_token', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $hidden = ['password', 'remember_token', 'password_md5'];

    // Các mối quan hệ (Relationships)
    public function roles()
    {
        // Chú ý: Nếu bảng trung gian của bạn là 'role_admin',
        // hãy đảm bảo foreign key khớp với bảng 'users' (thường là user_id)
        return $this->belongsToMany(Role::class, 'role_admin', 'admin_id', 'role_id');
    }

    public function saler()
    {
        return $this->belongsTo(self::class, 'sale_id', 'id');
    }

    public function invite()
    {
        return $this->belongsTo(self::class, 'invite_by', 'id');
    }

    // Các phương thức hỗ trợ phân quyền...
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($permissionName) {
            $q->where('name', $permissionName);
        })->exists();
    }
}
