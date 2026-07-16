<?php

namespace App\Modules\Smm\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\SmmApi;
use App\Modules\Smm\Models\SmmOrder;
use App\Modules\Core\Models\Setting;
use App\Modules\Theme\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SmmController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = new SmmApi();
    }

    public function index()
    {
        $profitMargin = (float) Setting::getValue('smm_api_tab_profit_margin', 50);

        // Get services and process them, cache for 10 minutes (tách theo locale vì $groupName được dịch theo ngôn ngữ)
        $cacheKey = 'smm_view_data_v2_' . app()->getLocale() . '_' . md5($profitMargin);
        $cachedData = Cache::remember($cacheKey, 600, function () use ($profitMargin) {
            $apiServices = $this->api->services();
            $categories = [];
            $jsCategories = [];

            if (is_array($apiServices) && !isset($apiServices->error)) {
                // Group by category first
                foreach ($apiServices as $service) {
                    // BỘ LỌC CỨNG: Bỏ qua các dịch vụ không hỗ trợ API hoặc đang bảo trì
                    if (isset($service->type) && strtolower($service->type) !== 'default') continue; // Chỉ hỗ trợ loại Default (cần link và số lượng)
                    if (isset($service->api) && $service->api == 0) continue;
                    if (isset($service->is_api) && $service->is_api == 0) continue;
                    if (isset($service->cancel) && strtolower($service->cancel) === 'true') {} // Một số API dùng cờ này, nhưng không hoàn toàn chắc chắn nên bỏ qua

                    $nameLower = mb_strtolower($service->name ?? '');
                    if (str_contains($nameLower, 'no api') || 
                        str_contains($nameLower, 'đóng api') || 
                        str_contains($nameLower, 'bảo trì') || 
                        str_contains($nameLower, 'ngừng') || 
                        str_contains($nameLower, 'không hỗ trợ api')) {
                        continue;
                    }

                    $originalRate = (float) $service->rate;
                    $service->user_rate = round($originalRate + ($originalRate * ($profitMargin / 100)), 2);
                    
                    if (!isset($categories[$service->category])) {
                        $categories[$service->category] = [];
                    }
                    $categories[$service->category][] = $service;
                }

                // Process categories for JS
                foreach($categories as $category => $services) {
                    $catHash = md5($category);
                    $jsCategories[$catHash] = [];
                    $platformName = trim(explode(' ', $category)[0]);
                    
                    foreach($services as $service) {
                        $nameLower = mb_strtolower($service->name);
                        $groupName = "📌 " . __('smmgroup.other');
                        if (str_contains($nameLower, 'like') || str_contains($nameLower, 'tim') || str_contains($nameLower, 'thích') || str_contains($nameLower, 'cảm xúc') || str_contains($nameLower, 'react')) $groupName = "👍 " . __('smmgroup.like');
                        elseif (str_contains($nameLower, 'follow') || str_contains($nameLower, 'sub') || str_contains($nameLower, 'theo dõi')) $groupName = "👤 " . __('smmgroup.follow');
                        elseif (str_contains($nameLower, 'view') || str_contains($nameLower, 'mắt') || str_contains($nameLower, 'xem') || str_contains($nameLower, 'giờ')) $groupName = "👁️ " . __('smmgroup.view');
                        elseif (str_contains($nameLower, 'comment') || str_contains($nameLower, 'cmt') || str_contains($nameLower, 'bình luận')) $groupName = "💬 " . __('smmgroup.comment');
                        elseif (str_contains($nameLower, 'share') || str_contains($nameLower, 'chia sẻ')) $groupName = "↗️ " . __('smmgroup.share');
                        elseif (str_contains($nameLower, 'member') || str_contains($nameLower, 'thành viên') || str_contains($nameLower, 'group')) $groupName = "👥 " . __('smmgroup.member');
                        
                        $shortName = str_ireplace($platformName, '', $service->name);
                        $shortName = trim(preg_replace('/\s+/', ' ', $shortName));
                        $shortName = trim($shortName, '- |/');
                        
                        $jsCategories[$catHash][$groupName][] = [
                            'id' => $service->service,
                            'name' => $shortName,
                            'min' => $service->min,
                            'max' => $service->max,
                            'rate' => $service->user_rate
                        ];
                    }
                }
            }

            return [
                'categories' => $categories,
                'jsCategoriesJson' => json_encode($jsCategories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
            ];
        });

        $categories = $cachedData['categories'];
        $jsCategoriesJson = $cachedData['jsCategoriesJson'];

        // Get user's order history
        $orders = SmmOrder::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

        return view('smm::client.index', compact('categories', 'jsCategoriesJson', 'orders'));
    }

    public function order(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:10',
        ]);

        $user = Auth::user();
        $apiServices = $this->api->services();
        $profitMargin = (float) Setting::getValue('smm_api_tab_profit_margin', 50);

        if (!is_array($apiServices) || isset($apiServices->error)) {
            return back()->with('error', 'Lỗi kết nối với nhà cung cấp dịch vụ. Vui lòng thử lại sau.');
        }

        // Find the requested service
        $selectedService = null;
        foreach ($apiServices as $service) {
            if ($service->service == $request->service_id) {
                $selectedService = $service;
                break;
            }
        }

        if (!$selectedService) {
            return back()->with('error', 'Dịch vụ không tồn tại.');
        }

        // Validate quantity constraints
        if ($request->quantity < $selectedService->min || $request->quantity > $selectedService->max) {
            return back()->with('error', "Số lượng phải từ {$selectedService->min} đến {$selectedService->max}.");
        }

        // Calculate total cost
        $originalRate = (float) $selectedService->rate;
        $userRate = $originalRate + ($originalRate * ($profitMargin / 100));
        
        // Rate is usually per 1000 items in SMM panels
        $totalCharge = ($userRate / 1000) * $request->quantity;

        // Check user balance
        if ($user->balance < $totalCharge) {
            return back()->with('error', 'Số dư không đủ để thực hiện giao dịch này. Vui lòng nạp thêm tiền.');
        }

        try {
            DB::beginTransaction();

            // 1. Deduct user balance
            $user->balance -= $totalCharge;
            $user->save();

            // 2. Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => -$totalCharge,
                'type' => 'purchase',
                'description' => "Mua dịch vụ mạng xã hội: {$selectedService->name} ({$request->quantity})",
                'status' => 'completed'
            ]);

            // 3. Send order to API
            $apiResponse = $this->api->order([
                'service' => $request->service_id,
                'link' => $request->link,
                'quantity' => $request->quantity,
            ]);

            if (isset($apiResponse->error)) {
                throw new \Exception("Lỗi từ nhà cung cấp: " . $apiResponse->error);
            }

            // 4. Save order to database
            SmmOrder::create([
                'user_id' => $user->id,
                'service_id' => $request->service_id,
                'service_name' => $selectedService->name,
                'link' => $request->link,
                'quantity' => $request->quantity,
                'charge' => $totalCharge,
                'api_order_id' => $apiResponse->order ?? null,
                'status' => 'Pending'
            ]);

            DB::commit();

            return back()->with('success', 'Đã đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMM Order Failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }
}
