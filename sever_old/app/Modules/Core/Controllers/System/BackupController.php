<?php

namespace App\Modules\Core\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Core\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class BackupController extends Controller
{
    public function index()
    {
        $settings = Setting::where('type', 'backup_database')->get()->keyBy('name');

        // Lấy danh sách file backup
        $files = [];
        $backupDir = storage_path('app/file-backup');
        if (is_dir($backupDir)) {
            $rawFiles = array_diff(scandir($backupDir), array('.', '..'));
            // Sắp xếp file mới nhất lên đầu
            rsort($rawFiles);
            foreach ($rawFiles as $file) {
                $files[] = [
                    'name' => $file,
                    'size' => number_format(filesize($backupDir . '/' . $file) / 1048576, 2) . ' MB',
                    'time' => date("d/m/Y H:i:s", filemtime($backupDir . '/' . $file))
                ];
            }
        }

        return view('core::system.backup.index', compact('settings', 'files'));
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'status', 'minute_backup', 'hour_backup', 
            'day_in_month_backup', 'month_backup', 'day_in_week_backup'
        ]);

        // Trạng thái checkbox (nếu không check thì là 0)
        $data['status'] = $request->has('status') ? '1' : '0';

        foreach ($data as $key => $value) {
            $item = Setting::where('name', $key)->where('type', 'backup_database')->first();
            if (!$item) {
                $item = new Setting();
                $item->name = $key;
                $item->type = 'backup_database';
            }
            $item->value = $value;
            $item->save();
        }

        return back()->with('success', 'Lưu cấu hình tự động backup thành công!');
    }

    public function runBackup()
    {
        try {
            // Thiết lập root disk cẩn thận để backup
            Config::set('filesystems.disks.local.root', storage_path('app/file-backup'));
            Artisan::call('backup:run', [
                '--only-db' => true
            ]);

            // Giữ lại 3 bản backup mới nhất, xóa các bản cũ hơn
            $backupDir = storage_path('app/file-backup');
            if (is_dir($backupDir)) {
                $files = array_diff(scandir($backupDir), array('.', '..'));
                rsort($files);
                foreach ($files as $key => $file) {
                    if ($key >= 3) {
                        unlink($backupDir . '/' . $file);
                    }
                }
            }

            return back()->with('success', 'Sao lưu Database thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi sao lưu: ' . $e->getMessage());
        }
    }

    public function downloadDB($name)
    {
        $file_url = storage_path('app/file-backup/' . basename($name));
        if (file_exists($file_url)) {
            return response()->download($file_url);
        }
        return back()->with('error', 'Không tìm thấy file backup!');
    }

    public function deleteDB($name)
    {
        $file_url = storage_path('app/file-backup/' . basename($name));
        if (file_exists($file_url) && unlink($file_url)) {
            return back()->with('success', 'Xóa file backup thành công!');
        }
        return back()->with('error', 'Không tìm thấy file hoặc không thể xóa!');
    }
}
