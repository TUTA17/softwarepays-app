<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SanthecaoService
{
    protected $partnerId;
    protected $secretKey;
    protected $apiUrl = 'http://gw.santhecao.com/api/user/sync';

    public function __construct()
    {
        // Sandbox keys for testing
        $this->partnerId = '1592';
        $this->secretKey = '21bfca6f363b96842c7f8c94bc0746bb';
        
        // You can override these via .env later
        if (config('services.santhecao.partner_id')) {
            $this->partnerId = config('services.santhecao.partner_id');
        }
        if (config('services.santhecao.secret_key')) {
            $this->secretKey = config('services.santhecao.secret_key');
        }
    }

    /**
     * 3DES-ECB Encryption compatible with mcrypt legacy
     */
    public function encrypt($data)
    {
        $key = md5($this->secretKey, true);
        $key .= substr($key, 0, 8);
        
        $blockSize = 8;
        $len = strlen($data);
        $pad = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);
        
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        return base64_encode($encData);
    }

    /**
     * 3DES-ECB Decryption compatible with mcrypt legacy
     */
    public function decrypt($data)
    {
        $key = md5($this->secretKey, true);
        $key .= substr($key, 0, 8);
        
        $data = base64_decode($data);
        $decData = openssl_decrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        
        if ($decData === false) {
            return false;
        }

        $pad = ord($decData[strlen($decData) - 1]);
        if ($pad > 0 && $pad <= 8) {
            return substr($decData, 0, strlen($decData) - $pad);
        }
        return $decData;
    }

    /**
     * Send request to Santhecao API
     */
    public function sendRequest(array $data)
    {
        $jsonStr = json_encode($data);
        $encryptedInfo = $this->encrypt($jsonStr);
        $urlEncodedInfo = urlencode($encryptedInfo);

        $requestUrl = $this->apiUrl . '?partner=' . $this->partnerId . '&info=' . $urlEncodedInfo;

        try {
            $response = Http::timeout(30)->get($this->apiUrl, [
                'partner' => $this->partnerId,
                'info' => $encryptedInfo // Guzzle handles urlencoding of query params automatically
            ]);

            $rawResponse = $response->body();
            
            // Đôi khi server trả về lỗi bằng plain JSON (ví dụ lỗi IP) thay vì mã hóa
            $parsedJson = json_decode($rawResponse, true);
            $decryptedResponse = null;

            if (json_last_error() === JSON_ERROR_NONE && is_array($parsedJson)) {
                // Đây là plain JSON
                $decryptedResponse = $rawResponse;
            } else {
                // Mã hóa bình thường
                $decryptedResponse = $this->decrypt($rawResponse);
                $parsedJson = json_decode($decryptedResponse, true);
            }

            return [
                'success' => true,
                'request_payload' => $data,
                'encrypted_payload' => $encryptedInfo,
                'request_url' => $requestUrl,
                'raw_response' => $rawResponse,
                'decrypted_response' => $decryptedResponse,
                'parsed_data' => $parsedJson
            ];
        } catch (\Exception $e) {
            Log::error('Santhecao API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'request_payload' => $data,
                'encrypted_payload' => $encryptedInfo,
                'request_url' => $requestUrl,
                'error_message' => $e->getMessage(),
                'raw_response' => null,
                'decrypted_response' => null,
                'parsed_data' => null
            ];
        }
    }

    /**
     * Gọi 1 function nghiệp vụ của Santhecao (BuyCardCode, CheckBuyCard, QueryStore...)
     * và trả thẳng phần parsed_data (mảng) — hoặc null nếu request thất bại hoàn toàn.
     */
    protected function call(string $function, array $params = []): ?array
    {
        $result = $this->sendRequest(array_merge(['function' => $function], $params));
        return $result['success'] ? ($result['parsed_data'] ?? null) : null;
    }

    // response_status = null/không có nghĩa là nhà cung cấp upstream (viễn thông/game) chưa xác nhận
    // kết quả — cần gọi lại checkBuyCard() sau, không được coi là thất bại.
    public function isPending(?array $json): bool
    {
        return $json !== null && (!array_key_exists('response_status', $json) || $json['response_status'] === null);
    }

    public function buyCardCode(string $telco, int $amount, int $quantity, string $partnerRid): ?array
    {
        return $this->call('BuyCardCode', [
            'telco' => $telco,
            'amount' => $amount,
            'quantity' => $quantity,
            'partner_rid' => $partnerRid,
        ]);
    }

    public function checkBuyCard(string $partnerRid, $transId): ?array
    {
        return $this->call('CheckBuyCard', [
            'partner_rid' => $partnerRid,
            'trans_id' => $transId,
        ]);
    }

    // Danh sách mệnh giá khả dụng cho 1 nhà mạng/loại thẻ (không có API discovery
    // thật cho eSIM/VPN kiểu catalog — santhecao dùng QueryStore cho việc này).
    public function queryStore(string $telco, ?int $amount = null): ?array
    {
        $params = ['telco' => $telco];
        if ($amount !== null) {
            $params['amount'] = $amount;
        }
        return $this->call('QueryStore', $params);
    }

    public function getBalance(): ?array
    {
        return $this->call('GetBalance');
    }
}
