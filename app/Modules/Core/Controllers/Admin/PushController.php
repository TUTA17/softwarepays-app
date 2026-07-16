<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\DeviceToken;
use App\Modules\Core\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    public function publicKey()
    {
        return response()->json(['publicKey' => config('services.vapid.public_key')]);
    }

    public function manifest()
    {
        $prefix = config('app.admin_prefix', 'admin');

        return response()->json([
            'name' => config('app.name') . ' Admin',
            'short_name' => 'Admin',
            'start_url' => '/' . $prefix,
            'scope' => '/' . $prefix . '/',
            'display' => 'standalone',
            'background_color' => '#f1f5f9',
            'theme_color' => '#2563eb',
            'icons' => [
                ['src' => asset('images/pwa-icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => asset('images/pwa-icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $admin = Auth::guard('admin')->user();
        $endpoint = $request->input('endpoint');

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $endpoint)],
            [
                'admin_id' => $admin->id,
                'endpoint' => $endpoint,
                'p256dh' => $request->input('keys.p256dh'),
                'auth' => $request->input('keys.auth'),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint_hash', hash('sha256', $request->input('endpoint')))->delete();

        return response()->json(['success' => true]);
    }

    public function fcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $admin = Auth::guard('admin')->user();
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
