<?php

namespace App\Modules\Core\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\DeviceToken;
use Illuminate\Http\Request;

class PushController extends Controller
{
    public function fcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $admin = $request->user();
        $token = $request->input('fcm_token');

        DeviceToken::updateOrCreate(
            ['token_hash' => hash('sha256', $token)],
            [
                'admin_id' => $admin->id,
                'fcm_token' => $token,
                'platform' => 'android',
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        return response()->json(['success' => true]);
    }
}
