<?php

namespace App\Services;

use App\Modules\Core\Models\Setting;
use Illuminate\Support\Facades\Log;

class SmmApi
{
    /** API URL */
    public $api_url = '';

    /** Your API key */
    public $api_key = '';

    public function __construct()
    {
        // Tự động lấy cấu hình từ Database (thiết lập trong trang Admin)
        $this->api_url = Setting::getValue('smm_api_tab_api_url', 'https://like.vn/api/v2');
        $this->api_key = Setting::getValue('smm_api_tab_api_token', '');
    }

    /** Add order */
    public function order($data)
    {
        $post = array_merge(['key' => $this->api_key, 'action' => 'add'], $data);
        return json_decode($this->connect($post));
    }

    /** Get order status  */
    public function status($order_id)
    {
        $result = $this->connect([
            'key' => $this->api_key,
            'action' => 'status',
            'order' => $order_id
        ]);
        return json_decode($result);
    }

    /** Get orders status */
    public function multiStatus($order_ids)
    {
        $result =  $this->connect([
            'key' => $this->api_key,
            'action' => 'status',
            'orders' => implode(",", (array)$order_ids)
        ]);
        return json_decode($result);
    }

    /** Get services */
    public function services()
    {
        $result = $this->connect([
            'key' => $this->api_key,
            'action' => 'services',
        ]);
        return json_decode($result);
    }
    
    /** Get balance */
    public function balance()
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'balance',
            ])
        );
    }

    private function connect($post)
    {
        $_post = [];
        if (is_array($post)) {
            foreach ($post as $name => $value) {
                $_post[] = $name . '=' . urlencode($value);
            }
        }

        $ch = curl_init(trim($this->api_url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        }
        
        // Timeout configuration
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch) != 0) {
            Log::error('SMM API Error: ' . curl_error($ch));
            $result = false;
        }
        
        curl_close($ch);
        return $result;
    }
}
