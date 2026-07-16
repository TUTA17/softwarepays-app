<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $secret;

    public function __construct()
    {
        $isLive = config('services.paypal.mode') === 'live';
        $this->baseUrl = $isLive ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $this->clientId = $isLive ? config('services.paypal.live_client_id') : config('services.paypal.sandbox_client_id');
        $this->secret = $isLive ? config('services.paypal.live_secret') : config('services.paypal.sandbox_secret');
    }

    protected function accessToken(): ?string
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->secret)
            ->timeout(15)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            Log::error('PayPal OAuth token error', ['body' => $response->body()]);
            return null;
        }

        return $response->json()['access_token'] ?? null;
    }

    // Tạo order PayPal, trả về ['order_id' => ..., 'approve_url' => ...] hoặc null nếu lỗi.
    // $landingPage: 'LOGIN' (mặc định, ưu tiên đăng nhập PayPal) hoặc 'BILLING' (bỏ qua đăng nhập,
    // vào thẳng form nhập thẻ Visa/Mastercard dạng khách) — dùng để tách 2 nút "PayPal" và "Thẻ" trên site.
    public function createOrder(float $amount, string $currencyCode, string $referenceId, string $returnUrl, string $cancelUrl, string $landingPage = 'LOGIN'): ?array
    {
        $token = $this->accessToken();
        if (!$token) return null;

        // PayPal không chấp nhận phần thập phân cho các tiền tệ "zero-decimal" như JPY
        $decimals = in_array($currencyCode, ['JPY']) ? 0 : 2;

        $response = Http::withToken($token)
            ->timeout(15)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $referenceId,
                    'amount' => [
                        'currency_code' => $currencyCode,
                        'value' => number_format($amount, $decimals, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'user_action' => 'PAY_NOW',
                    'landing_page' => in_array($landingPage, ['LOGIN', 'BILLING'], true) ? $landingPage : 'LOGIN',
                ],
            ]);

        if (!$response->successful()) {
            Log::error('PayPal create order error', ['body' => $response->body()]);
            return null;
        }

        $data = $response->json();
        $approveUrl = collect($data['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approveUrl) return null;

        return [
            'order_id' => $data['id'],
            'approve_url' => $approveUrl,
        ];
    }

    // Capture order sau khi khách đã approve bên PayPal. Trả về true nếu COMPLETED.
    public function captureOrder(string $orderId): bool
    {
        $token = $this->accessToken();
        if (!$token) return false;

        $response = Http::withToken($token)
            ->timeout(15)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            Log::error('PayPal capture order error', ['order_id' => $orderId, 'body' => $response->body()]);
            return false;
        }

        $status = $response->json()['status'] ?? null;
        return $status === 'COMPLETED';
    }
}
