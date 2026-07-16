<?php

namespace App\Modules\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $permissions = session('permissions', []);

        if (!in_array($permission, $permissions) && !in_array('super_admin', $permissions)) {
            abort(403, 'Bạn không có quyền truy cập');
        }

        return $next($request);
    }
}
