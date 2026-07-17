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

    // Tên dịch vụ/máy chủ (SV1, SV2...) và tên danh mục (Facebook Việt Nam, TikTok Quốc Tế...)
    // đến thẳng từ API nhà cung cấp SMM bằng tiếng Việt, không đi qua hệ thống dịch __() của site.
    // Với khách dùng locale khác 'vi', dịch các cụm/từ tiếng Việt phổ biến trong đó sang tiếng Anh
    // để không hiển thị lẫn lộn ngôn ngữ như khi site đã chuyển sang mặc định English cho khách nước ngoài.
    private const SMM_PHRASE_MAP = [
        'Tăng like bình luận' => 'Increase Comment Likes',
        'Tăng like bài viết' => 'Increase Post Likes',
        'Tăng like/follow Fanpage' => 'Increase Page Likes/Follows',
        'Tăng like video' => 'Increase Video Likes',
        'Tăng lượt xem' => 'Increase Views',
        'Tăng mắt xem' => 'Increase Views',
        'Tăng người theo dõi' => 'Increase Followers',
        'Tăng theo dõi' => 'Increase Followers',
        'Tăng bình luận' => 'Increase Comments',
        'Tăng chia sẻ' => 'Increase Shares',
        'Tăng thành viên' => 'Increase Members',
        'Tăng follow' => 'Increase Followers',
        'Tăng sub' => 'Increase Subscribers',
        'Tăng view' => 'Increase Views',
        'Tăng like' => 'Increase Likes',
        'Chất lượng cao' => 'High Quality',
        'Tốc độ nhanh' => 'Fast Speed',
        'Giá rẻ' => 'Cheap',
        'Không bảo hành' => 'No Warranty',
        'Có bảo hành' => 'With Warranty',
        'Bảo hành' => 'Warranty',
        'Không tụt' => 'No Drop',
        'Việt Nam' => 'Vietnam',
        'Quốc Tế' => 'International',
        'Quốc tế' => 'International',
        'Ngẫu nhiên' => 'Random',
        'Người thật' => 'Real Users',
        'Người dùng thật' => 'Real Users',
        'Tài khoản ảo' => 'Virtual Accounts',
        'Ảnh đại diện' => 'Profile Picture',
        'Trực tiếp' => 'Live',
        'giờ xem' => 'Watch Hours',
        'đăng lại' => 'Repost',
        'Đăng ký' => 'Subscribe',
        'cá nhân' => 'Personal',
        'lưu' => 'Save',
        'tim' => 'Like',
        'bình luận' => 'comment',
        'bài viết' => 'post',
        'người theo dõi' => 'followers',
        'theo dõi' => 'follow',
        'mắt xem' => 'views',
        'lượt xem' => 'views',
        'chia sẻ' => 'share',
        'thành viên' => 'members',
        'tài khoản' => 'account',
        'kênh' => 'channel',
        'video' => 'video',
        'trang' => 'page',
        'nhóm' => 'group',
        'nam' => 'male',
        'nữ' => 'female',
        'Tăng' => 'Increase',
    ];

    private function translateSmmText(string $text): string
    {
        if (app()->getLocale() === 'vi' || $text === '') {
            return $text;
        }

        // Cụm dài hơn ưu tiên thay trước để tránh khớp nhầm một phần của cụm dài hơn.
        $phrases = self::SMM_PHRASE_MAP;
        uksort($phrases, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        foreach ($phrases as $vi => $en) {
            $text = preg_replace('/' . preg_quote($vi, '/') . '/ui', $en, $text);
        }

        return trim(preg_replace('/\s+/', ' ', $text));
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
                    if ($originalRate <= 0) continue; // Giá 0đ là dữ liệu lỗi từ nhà cung cấp, không cho khách chọn được
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
                        $shortName = $this->translateSmmText($shortName);

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

            // Tên danh mục (tab nền tảng) cũng đến từ API bằng tiếng Việt (vd: "Facebook Việt Nam") —
            // dịch riêng ra một mảng hiển thị, giữ nguyên key gốc để hash (md5) khớp với $jsCategories.
            $categoryLabels = [];
            foreach (array_keys($categories) as $category) {
                $categoryLabels[$category] = $this->translateSmmText($category);
            }

            return [
                'categories' => $categories,
                'categoryLabels' => $categoryLabels,
                'jsCategoriesJson' => json_encode($jsCategories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
            ];
        });

        $categories = $cachedData['categories'];
        $categoryLabels = $cachedData['categoryLabels'];
        $jsCategoriesJson = $cachedData['jsCategoriesJson'];

        // Get user's order history
        $orders = SmmOrder::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

        return view('smm::client.index', compact('categories', 'categoryLabels', 'jsCategoriesJson', 'orders'));
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

        // Chế độ xử lý đơn chung (Cài đặt > Tự động hoá & API) — trước đây đơn MXH luôn gọi API
        // ngay bất kể chế độ, khác với Game/Giftcard/Thẻ nạp/VPN/eSIM đã tôn trọng thiết lập này.
        $fulfillmentMode = Setting::getValue('order_fulfillment_mode', 'manual');

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

            if ($fulfillmentMode === 'manual') {
                // Không gọi API ngay — tạo đơn "Chờ duyệt" để admin tự bấm gửi qua API sau,
                // giống hệt cách Thẻ nạp/VPN/eSIM đang chờ xử lý ở trang Đơn Hàng.
                SmmOrder::create([
                    'user_id' => $user->id,
                    'service_id' => $request->service_id,
                    'service_name' => $selectedService->name,
                    'link' => $request->link,
                    'quantity' => $request->quantity,
                    'charge' => $totalCharge,
                    'api_order_id' => null,
                    'status' => 'Chờ duyệt',
                ]);
            } else {
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
            }

            DB::commit();

            // Thông báo admin có đơn SMM mới — trước đây chỉ CartController (Game/Giftcard/Thẻ nạp/VPN/eSIM)
            // gọi thông báo này, đơn SMM bị bỏ sót hoàn toàn nên admin không biết có đơn mới.
            try {
                (new \App\Modules\Core\Services\WebPushService())->notifyAllAdmins(
                    '🛒 Đơn hàng MXH mới',
                    $user->name . ' vừa mua ' . $selectedService->name . ' (' . $request->quantity . ') - ' . number_format($totalCharge) . 'đ',
                    route('admin.orders')
                );
            } catch (\Throwable $e) {
                Log::warning('Push notify (đơn SMM mới) thất bại: ' . $e->getMessage());
            }

            try {
                (new \App\Modules\Core\Services\FcmService())->notifyAllAdmins(
                    '🛒 Đơn hàng MXH mới',
                    $user->name . ' vừa mua ' . $selectedService->name . ' (' . $request->quantity . ') - ' . number_format($totalCharge) . 'đ',
                    route('admin.orders')
                );
            } catch (\Throwable $e) {
                Log::warning('FCM notify (đơn SMM mới) thất bại: ' . $e->getMessage());
            }

            return back()->with('success', 'Đã đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMM Order Failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }
}
