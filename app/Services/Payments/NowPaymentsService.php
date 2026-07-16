<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsService
{
    protected string $baseUrl = 'https://api.nowpayments.io/v1';

    // Map tên phương thức trong keygame -> mã pay_currency của NOWPayments
    public const CURRENCY_MAP = [
        'bitcoin' => 'btc',
        'ethereum' => 'eth',
        'litecoin' => 'ltc',
        'usdt' => 'usdttrc20',
        'solana' => 'sol',
    ];

    protected function apiKey(): string
    {
        return config('services.nowpayments.api_key');
    }

    // NOWPayments tự áp mức tối thiểu riêng cho từng loại coin (biến động theo phí mạng, thường cao hơn nhiều
    // so với sàn tối thiểu $1 của hệ thống) — phải hỏi trước, nếu không payment sẽ bị từ chối với AMOUNT_MINIMAL_ERROR.
    public function getMinAmountUsd(string $payCurrency): ?float
    {
        return Cache::remember("nowpayments_min_amount_{$payCurrency}", 60 * 10, function () use ($payCurrency) {
            try {
                $response = Http::withHeaders(['x-api-key' => $this->apiKey()])
                    ->timeout(10)
                    ->get("{$this->baseUrl}/min-amount", [
                        'currency_from' => 'usd',
                        'currency_to' => $payCurrency,
                        'fiat_equivalent' => 'usd',
                    ]);

                if ($response->successful()) {
                    return (float) ($response->json()['fiat_equivalent'] ?? $response->json()['min_amount'] ?? 0);
                }
            } catch (\Exception $e) {
                // Ignore, dùng fallback ở nơi gọi
            }
            return null;
        });
    }

    public function createPayment(float $amountUsd, string $method, string $orderId, string $ipnCallbackUrl): ?array
    {
        $payCurrency = self::CURRENCY_MAP[$method] ?? null;
        if (!$payCurrency) return null;

        $response = Http::withHeaders(['x-api-key' => $this->apiKey()])
            ->timeout(15)
            ->post("{$this->baseUrl}/payment", [
                'price_amount' => $amountUsd,
                'price_currency' => 'usd',
                'pay_currency' => $payCurrency,
                'order_id' => $orderId,
                'order_description' => 'SoftwarePays order ' . $orderId,
                'ipn_callback_url' => $ipnCallbackUrl,
            ]);

        if (!$response->successful()) {
            Log::error('NOWPayments create payment error', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function getPaymentStatus(string $paymentId): ?array
    {
        $response = Http::withHeaders(['x-api-key' => $this->apiKey()])
            ->timeout(15)
            ->get("{$this->baseUrl}/payment/{$paymentId}");

        if (!$response->successful()) {
            Log::error('NOWPayments get status error', ['payment_id' => $paymentId, 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    // Xác thực chữ ký IPN: HMAC-SHA512 của JSON đã sort key, so với header x-nowpayments-sig
    public function verifyIpnSignature(array $payload, ?string $signatureHeader): bool
    {
        if (!$signatureHeader) return false;

        $sorted = $this->sortKeysRecursive($payload);
        $jsonString = json_encode($sorted, JSON_UNESCAPED_SLASHES);
        $expectedSignature = hash_hmac('sha512', $jsonString, config('services.nowpayments.ipn_secret'));

        return hash_equals($expectedSignature, $signatureHeader);
    }

    protected function sortKeysRecursive($data)
    {
        if (!is_array($data)) return $data;
        ksort($data);
        foreach ($data as $key => $value) {
            $data[$key] = $this->sortKeysRecursive($value);
        }
        return $data;
    }
}
