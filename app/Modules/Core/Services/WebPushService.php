<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected function client(): WebPush
    {
        return new WebPush([
            'VAPID' => [
                'subject' => config('services.vapid.subject') ?: config('app.url'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);
    }

    /**
     * Gửi push notification tới tất cả admin đã đăng ký nhận thông báo trên thiết bị.
     */
    public function notifyAllAdmins(string $title, string $body, ?string $url = null): void
    {
        $subscriptions = PushSubscription::all();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $webPush = $this->client();
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? '/',
        ]);

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth,
                ]),
                $payload
            );
        }

        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                $statusCode = $report->getResponse()?->getStatusCode();
                // Subscription hết hạn hoặc bị thu hồi phía trình duyệt -> xoá khỏi DB
                if (in_array($statusCode, [404, 410])) {
                    PushSubscription::where('endpoint', $report->getEndpoint())->delete();
                }
            }
        }
    }
}
