<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VpnPanelsService
{
    protected function baseUrl(): string
    {
        return config('services.vpnpanels.base_url');
    }

    protected function apiKey(): string
    {
        return config('services.vpnpanels.api_key');
    }

    // API này không nhất quán: lỗi validate trả {"error": "..."},
    // lỗi nghiệp vụ (vd không đủ số dư) trả {"status": "error", "message": "..."}.
    protected function request(string $action, array $params = []): ?array
    {
        $response = Http::asForm()->timeout(20)->post($this->baseUrl(), array_merge([
            'key' => $this->apiKey(),
            'action' => $action,
        ], $params));

        $json = $response->json();

        if (!$response->successful() || !empty($json['error']) || (($json['status'] ?? null) === 'error')) {
            $message = $json['error'] ?? $json['message'] ?? $response->reason();
            Log::error("VPN Panels {$action} failed", ['message' => $message]);
            return null;
        }

        return $json;
    }

    public function getInfo(): ?array
    {
        return $this->request('info');
    }

    public function listServers(): ?array
    {
        return $this->request('servers');
    }

    public function createOrder(string $server, string $selectedPackage): ?array
    {
        return $this->request('add', ['server' => $server, 'selectedPackage' => $selectedPackage]);
    }

    public function getOrderStatus(string $id): ?array
    {
        return $this->request('status', ['id' => $id]);
    }

    protected function extractCredentials(?array $serverResponse): array
    {
        if (!$serverResponse) {
            return ['username' => null, 'password' => null, 'subscription_link' => null];
        }

        $username = $serverResponse['username'] ?? $serverResponse['server_username'] ?? null;
        $password = $serverResponse['trojan']['password'] ?? $serverResponse['server_password'] ?? null;

        $subscriptionLink = null;
        if (!empty($serverResponse['subscription_url'])) {
            $url = $serverResponse['subscription_url'];
            $subscriptionLink = str_starts_with($url, 'http') ? $url : ('https://vpnpanels.com' . $url);
        } elseif (!empty($serverResponse['links'][0])) {
            $subscriptionLink = $serverResponse['links'][0];
        }

        return ['username' => $username, 'password' => $password, 'subscription_link' => $subscriptionLink];
    }

    // Đồng bộ (không cần polling) — trả về mảng delivery_data sẵn sàng lưu vào GameKey::delivery_data,
    // hoặc null nếu thất bại.
    public function purchaseVpn(string $server, string $selectedPackage): ?array
    {
        $result = $this->createOrder($server, $selectedPackage);
        if (!$result) {
            return null;
        }

        $credentials = $this->extractCredentials($result['server_response'] ?? null);

        return array_merge($credentials, [
            'order_id' => isset($result['order']) ? (string) $result['order'] : null,
            'status' => $result['server_response']['status'] ?? 'active',
        ]);
    }
}
