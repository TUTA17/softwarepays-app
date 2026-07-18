<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaylioService
{
    protected string $baseUrl = 'https://paylio.org/api/v1';

    protected function apiKey(): string
    {
        return config('services.paylio.api_key');
    }

    public function createWallet(string $address, string $callbackUrl, float $amountUsd, string $email, string $note): ?array
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey()])
            ->timeout(15)
            ->post("{$this->baseUrl}/wallet", [
                'address' => $address,
                'callback' => $callbackUrl,
                'amount' => (string) $amountUsd,
                'currency' => 'USD',
                'email' => $email,
                'note' => $note,
            ]);

        if (!$response->successful()) {
            Log::error('Paylio createWallet error', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    // Callback GET của Paylio không có chữ ký xác thực (theo tài liệu API), chỉ là gợi ý —
    // BẮT BUỘC phải gọi lại endpoint này để xác minh trạng thái thật trước khi cộng tiền cho khách.
    public function getStatus(?string $ipnToken, ?string $paymentId): ?array
    {
        if (!$ipnToken && !$paymentId) return null;

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey()])
            ->timeout(15)
            ->get("{$this->baseUrl}/payment-status", $ipnToken
                ? ['ipn_token' => $ipnToken]
                : ['payment_id' => $paymentId]);

        if (!$response->successful()) {
            Log::error('Paylio getStatus error', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }
}
