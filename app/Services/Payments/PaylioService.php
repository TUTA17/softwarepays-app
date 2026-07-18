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

    // $provider: 1 trong số các provider hợp lệ đã kiểm tra trực tiếp qua API (stripe, banxa, transak,
    // revolut, rampnetwork, coinbase, paypal, binance, klarna [chỉ nhận EUR/SEK]) — bỏ trống/null thì
    // Paylio mặc định "multi" (hiện màn chọn provider của chính họ trước khi vào thanh toán).
    public function createWallet(string $address, string $callbackUrl, float $amountUsd, string $email, string $note, ?string $provider = null): ?array
    {
        $payload = [
            'address' => $address,
            'callback' => $callbackUrl,
            'amount' => (string) $amountUsd,
            'currency' => 'USD',
            'email' => $email,
            'note' => $note,
        ];
        if ($provider) {
            $payload['provider'] = $provider;
        }

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey()])
            ->timeout(15)
            ->post("{$this->baseUrl}/wallet", $payload);

        if (!$response->successful()) {
            Log::error('Paylio createWallet error', ['body' => $response->body()]);
            // Trả lại body lỗi thật (VD: "Provider requires at least X USD") thay vì chỉ null,
            // để controller hiển thị đúng nguyên nhân cho khách thay vì thông báo chung chung.
            return ['error' => $response->json('error')];
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
