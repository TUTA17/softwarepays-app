<?php

namespace App\Modules\Core\Controllers;

use App\Modules\Core\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SmmSettingController extends Controller
{
    /**
     * Hiển thị trang cấu hình SMM
     */
    public function index()
    {
        return view('core::smm.settings');
    }

    /**
     * Xử lý lưu cấu hình
     */
    public function store(Request $request)
    {
        $request->validate([
            'smm_api_url' => 'required|url',
            'smm_api_token' => 'required|string',
            'smm_profit_margin' => 'required|numeric|min:0',
        ]);

        Setting::updateOrCreate(['name' => 'smm_api_tab_api_url', 'type' => 'smm_api_tab'], ['value' => $request->smm_api_url]);
        Setting::updateOrCreate(['name' => 'smm_api_tab_api_token', 'type' => 'smm_api_tab'], ['value' => $request->smm_api_token]);
        Setting::updateOrCreate(['name' => 'smm_api_tab_profit_margin', 'type' => 'smm_api_tab'], ['value' => $request->smm_profit_margin]);

        return back()->with('success', 'Đã lưu cấu hình SMM thành công!');
    }
}
