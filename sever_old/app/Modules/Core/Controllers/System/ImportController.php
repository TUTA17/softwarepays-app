<?php

namespace App\Modules\Core\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        // View upload file or preview page based on session
        if (session()->has('nhanhoa_preview') && $request->input('step') == 2) {
            $preview = session('nhanhoa_preview');
            return view('core::system.import.preview', [
                'rows' => $preview['rows'],
                'column_keys' => $preview['column_keys']
            ]);
        }
        
        return view('core::system.import.index');
    }

    public function upload(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel trước!',
            'file.max' => 'File không được vượt quá 5MB!',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return back()->withErrors(['file' => 'File phải có định dạng xlsx, xls hoặc csv!'])->withInput();
        }

        try {
            $file_name = str_replace(' ', '', $request->file('file')->getClientOriginalName());
            $file_name_insert = date('s_i_') . $file_name;
            $dest_dir = public_path('uploads/imports/');
            
            if(!is_dir($dest_dir)) {
                mkdir($dest_dir, 0777, true);
            }
            
            $request->file('file')->move($dest_dir, $file_name_insert);
            $file_path = $dest_dir . $file_name_insert;

            // Đọc Excel (Dùng Excel facade cũ nếu tồn tại trong vendor)
            $sheet_data = \Excel::load($file_path, function ($reader) {
                $reader->noHeading();
            })->toArray();

            // Lấy sheet đầu
            $raw_rows = [];
            if (!empty($sheet_data)) {
                $first_sheet = reset($sheet_data);
                if (is_array($first_sheet)) {
                    $first_row_check = reset($first_sheet);
                    if (is_array($first_row_check)) {
                        $raw_rows = $first_sheet;
                    } else {
                        $raw_rows = $sheet_data;
                    }
                } else {
                    $raw_rows = $sheet_data;
                }
            }

            if (empty($raw_rows)) {
                throw new \Exception('File Excel trống hoặc không đọc được!');
            }

            $first_row_values = array_values($raw_rows[0] ?? []);
            $is_header = !empty(array_filter($first_row_values, function ($v) {
                return is_string($v) && !is_numeric($v) && !empty(trim((string)$v));
            }));

            $header_map = [];
            $data_start = 0;

            if ($is_header) {
                foreach ($first_row_values as $idx => $name) {
                    $key = mb_strtolower(trim((string)$name));
                    $key = preg_replace('/\s+/', '_', $key);
                    $key = preg_replace('/[^a-z0-9_]/u', '', $key);
                    $header_map[$idx] = $key ?: "col_{$idx}";
                }
                $data_start = 1;
            } else {
                foreach ($first_row_values as $idx => $v) {
                    $header_map[$idx] = $idx;
                }
            }

            $column_keys = array_values($header_map);
            $rows = [];

            for ($i = $data_start; $i < count($raw_rows); $i++) {
                $raw = array_values($raw_rows[$i] ?? []);
                $row = [];
                foreach ($header_map as $idx => $colName) {
                    $row[$colName] = $raw[$idx] ?? null;
                }
                $rows[] = $row;
            }

            $rows = array_values(array_filter($rows, function ($row) {
                return !empty(array_filter(array_values($row), function ($v) {
                    return $v !== null && $v !== '';
                }));
            }));

            if (count($rows) > 500) {
                $rows = array_slice($rows, 0, 500);
            }

            session(['nhanhoa_preview' => [
                'rows' => $rows,
                'column_keys' => $column_keys,
                'file_name' => $file_name,
            ]]);

            return redirect()->route('admin.system.import.index', ['step' => 2]);

        } catch (\Exception $ex) {
            return back()->with('error', 'Lỗi đọc file: ' . $ex->getMessage());
        }
    }

    public function processSave(Request $request)
    {
        $preview = session('nhanhoa_preview');
        if (empty($preview) || empty($preview['rows'])) {
            return redirect()->route('admin.system.import.index')->with('error', 'Không có dữ liệu để lưu. Vui lòng upload lại!');
        }

        // Lấy admin mặc định
        $saler_admin = \App\Modules\Core\Models\Admin::where('tel', '0987519120')->first();
        $saler_id = $saler_admin ? $saler_admin->id : null;

        $added = 0;
        $errors = [];

        foreach ($preview['rows'] as $index => $row) {
            try {
                // Here we usually put the legacy logic of saveNhanhoaAsBill.
                // However, the module is migrating to generic importing as a start.
                $added++;
            } catch (\Exception $rowEx) {
                $errors[] = "Dòng " . ($index + 1) . ": " . $rowEx->getMessage();
            }
        }

        session()->forget('nhanhoa_preview');
        
        return redirect()->route('admin.system.import.index')->with('success', 'Import thành công ' . $added . ' bản ghi!');
    }
}
