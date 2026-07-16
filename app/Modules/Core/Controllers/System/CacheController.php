<?php

namespace App\Modules\Core\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    public function index()
    {
        return view('core::system.cache.index');
    }

    public function clear($type)
    {
        try {
            switch ($type) {
                case 'all':
                    Artisan::call('cache:clear');
                    $msg = 'Xóa tất cả bộ nhớ đệm ứng dụng thành công!';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $msg = 'Làm mới bộ đệm giao diện thành công!';
                    break;
                case 'setting':
                    Artisan::call('config:clear');
                    $msg = 'Xóa bộ nhớ đệm cấu hình thành công!';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $msg = 'Xóa cache đường dẫn thành công!';
                    break;
                case 'error':
                    \App\Modules\Core\Models\ErrorLog::truncate();
                    $msg = 'Xóa toàn bộ lịch sử lỗi thành công!';
                    break;
                default:
                    return back()->with('error', 'Loại cache không hợp lệ!');
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
