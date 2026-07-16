<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\DeviceToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FcmService
{
    protected function messaging()
    {
        return (new Factory())
            ->withServiceAccount(config('services.firebase.credentials'))
            ->createMessaging();
    }

    /**
     * Gửi push notification (FCM) tới tất cả admin đã đăng ký thiết bị (app Android).
     */
    public function notifyAllAdmins(string $title, string $body, ?string $url = null): void
    {
        $deviceTokens = DeviceToken::all();

        if ($deviceTokens->isEmpty()) {
            return;
        }

        $message = CloudMessage::new()
            ->withNotification(FcmNotification::create($title, $body))
            ->withData(['url' => $url ?? '/']);

        $report = $this->messaging()->sendMulticast($message, $deviceTokens->pluck('fcm_token')->all());

        if ($report->hasFailures()) {
            $deadTokens = array_merge($report->invalidTokens(), $report->unknownTokens());
            if (!empty($deadTokens)) {
                DeviceToken::whereIn('fcm_token', $deadTokens)->delete();
            }
        }
    }
}
