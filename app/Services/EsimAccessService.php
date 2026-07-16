<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EsimAccessService
{
    protected function baseUrl(): string
    {
        return config('services.esimaccess.base_url');
    }

    protected function apiKey(): string
    {
        return config('services.esimaccess.api_key');
    }

    protected function request(string $path, array $body = []): ?array
    {
        // json_encode([]) cho ra "[]" chứ không phải "{}" — API này báo lỗi
        // "request json invalid" nếu nhận mảng rỗng thay vì object rỗng.
        $payload = empty($body) ? '{}' : json_encode($body);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'RT-AccessCode' => $this->apiKey(),
        ])->timeout(20)->withBody($payload, 'application/json')->post($this->baseUrl() . $path);

        $json = $response->json();

        if (!$response->successful() || ($json['success'] ?? false) !== true) {
            Log::error("eSIM Access {$path} failed", [
                'errorCode' => $json['errorCode'] ?? null,
                'errorMsg' => $json['errorMsg'] ?? $response->reason(),
            ]);
            return null;
        }

        return $json['obj'] ?? null;
    }

    public function listPackages(array $params = []): ?array
    {
        return $this->request('/api/v1/open/package/list', $params);
    }

    public function listLocations(array $params = []): ?array
    {
        return $this->request('/api/v1/open/location/list', $params);
    }

    public function createOrder(string $transactionId, string $packageCode, int $count = 1): ?array
    {
        return $this->request('/api/v1/open/esim/order', [
            'transactionId' => $transactionId,
            'packageInfoList' => [[
                'packageCode' => $packageCode,
                'count' => $count,
            ]],
        ]);
    }

    public function queryEsims(string $orderNo): ?array
    {
        return $this->request('/api/v1/open/esim/query', [
            'orderNo' => $orderNo,
            'pager' => ['pageNum' => 1, 'pageSize' => 20],
        ]);
    }

    // Gọi ngay sau khi mua để lấy orderNo; kết quả eSIM (mã kích hoạt/QR) thường
    // CHƯA sẵn sàng ngay, cần poll qua queryEsims() sau đó (xem PollEsimStatus job).
    public function purchaseEsim(int $orderItemId, string $packageCode, int $count = 1): ?string
    {
        $transactionId = "keygame-item-{$orderItemId}-" . time();
        $result = $this->createOrder($transactionId, $packageCode, $count);

        return $result['orderNo'] ?? null;
    }

    // Trả về delivery_data đã sẵn sàng nếu tất cả eSIM trong đơn đã có mã kích hoạt,
    // null nếu vẫn đang xử lý (gọi lại sau).
    public function checkReady(string $orderNo): ?array
    {
        $result = $this->queryEsims($orderNo);
        $esimList = $result['esimList'] ?? [];

        if (empty($esimList) || !collect($esimList)->every(fn ($e) => !empty($e['ac']))) {
            return null;
        }

        $delim = "\n---\n";
        return [
            'esim_tran_no' => collect($esimList)->pluck('esimTranNo')->join($delim),
            'iccid' => collect($esimList)->pluck('iccid')->join($delim),
            'activation_code' => collect($esimList)->pluck('ac')->join($delim),
            'qr_code_url' => collect($esimList)->pluck('qrCodeUrl')->join($delim),
        ];
    }
}
