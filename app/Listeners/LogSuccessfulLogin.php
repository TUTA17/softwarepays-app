<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewDeviceAlertMail;

class LogSuccessfulLogin
{
    public $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();
        
        // Kiểm tra xem IP này đã từng đăng nhập chưa
        $isNewIp = !DB::table('login_histories')
            ->where('user_id', $user->id)
            ->where('ip_address', $ip)
            ->exists();

        // Ghi lại lịch sử
        DB::table('login_histories')->insert([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'login_at' => now(),
        ]);

        // Nếu là IP mới, gửi email cảnh báo
        if ($isNewIp) {
            try {
                Mail::to($user->email)->send(new NewDeviceAlertMail($ip, $userAgent, now()->format('d/m/Y H:i:s')));
            } catch (\Exception $e) {
                // Ignore mail errors to not disrupt login
            }
        }
    }
}
