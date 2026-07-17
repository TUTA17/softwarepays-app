<?php

namespace App\Modules\Shop\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Theme\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('shop::theme.cart', compact('cart', 'total'));
    }

    // Các loại sản phẩm bán theo "gói/mệnh giá" (nhiều mức giá cho 1 sản phẩm) thay vì
    // 1 sản phẩm = 1 giá cố định như game key — cần chọn variant_id khi thêm vào giỏ.
    protected const VARIANT_TYPES = [Product::TYPE_VPN, Product::TYPE_ESIM, Product::TYPE_CARD];

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (!$product->is_active) {
            return back()->with('error', 'Sản phẩm này tạm thời hết hàng!');
        }

        $cartKey = $id;
        $variantId = $request->input('variant_id');
        $price = $product->price;
        $variantLabel = null;

        if (in_array($product->product_type, self::VARIANT_TYPES)) {
            if (!$variantId) {
                return back()->with('error', 'Vui lòng chọn gói/mệnh giá trước khi thêm vào giỏ.');
            }

            $variant = match ($product->product_type) {
                Product::TYPE_VPN => $product->vpnPackages()->where('id', $variantId)->where('is_active', true)->first(),
                Product::TYPE_ESIM => $product->esimPackages()->where('id', $variantId)->where('is_active', true)->first(),
                Product::TYPE_CARD => $product->cardPackages()->where('id', $variantId)->where('is_active', true)->first(),
            };

            if (!$variant) {
                return back()->with('error', 'Gói/mệnh giá không hợp lệ.');
            }

            $cartKey = "{$id}:{$variantId}";
            $price = $variant->price;
            $variantLabel = $variant->name ?? ('Mệnh giá ' . number_format($variant->face_value ?? 0) . 'đ');
        } elseif ($product->available_keys == 0) {
            return back()->with('error', 'Sản phẩm này tạm thời hết hàng!');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                "product_id" => $product->id,
                "product_type" => $product->product_type,
                "variant_id" => $variantId,
                "variant_label" => $variantLabel,
                "name" => $product->name . ($variantLabel ? " ({$variantLabel})" : ''),
                "quantity" => 1,
                "price" => $price,
                "image" => $product->header_image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function checkoutView()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $discount_amount = 0;
        $applied_coupon = null;
        if (session()->has('applied_coupon_id')) {
            $applied_coupon = \App\Modules\Theme\Models\Coupon::find(session('applied_coupon_id'));
            if ($applied_coupon && $applied_coupon->isValid() && $total >= $applied_coupon->min_order_amount) {
                $discount_amount = $applied_coupon->calculateDiscount($total);
            } else {
                session()->forget('applied_coupon_id');
                $applied_coupon = null;
            }
        }

        $final_total = $total - $discount_amount;

        // Lấy danh sách mã người dùng đã lưu
        $saved_coupons = [];
        if (\Illuminate\Support\Facades\Auth::check()) {
            $saved_coupons = \Illuminate\Support\Facades\Auth::user()->coupons()->where('status', 'saved')->get();
        }

        $feeConfig = \App\Modules\Core\Models\Setting::getAllGrouped()['payment_fee_tab'] ?? [];
        $usdRate = \App\Helpers\CurrencyHelper::usdRate();
        $paypalCurrency = \App\Helpers\CurrencyHelper::paypalCurrencyForSelection(session('currency', 'VND'), session('locale', 'vi'));

        return view('shop::theme.checkout', compact('cart', 'total', 'discount_amount', 'final_total', 'applied_coupon', 'saved_coupons', 'feeConfig', 'usdRate', 'paypalCurrency'));
    }

    public function checkoutProcess(Request $request, \App\Services\WholesaleProviderService $providerService)
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $discount_amount = 0;
        $applied_coupon = null;
        if (session()->has('applied_coupon_id')) {
            $applied_coupon = \App\Modules\Theme\Models\Coupon::find(session('applied_coupon_id'));
            if ($applied_coupon && $applied_coupon->isValid() && $total >= $applied_coupon->min_order_amount) {
                $discount_amount = $applied_coupon->calculateDiscount($total);
            } else {
                session()->forget('applied_coupon_id');
                $applied_coupon = null;
            }
        }

        $final_total = $total - $discount_amount;

        $paymentMethod = $request->input('payment_method', 'wallet');
        $feeConfig = \App\Modules\Core\Models\Setting::getAllGrouped()['payment_fee_tab'] ?? [];

        if (in_array($paymentMethod, \App\Modules\Core\Controllers\Admin\SettingController::INTL_FEE_METHODS)) {
            return back()->with('error', 'Phương thức thanh toán quốc tế đang cập nhật, vui lòng chọn phương thức khác.');
        }

        if (in_array($paymentMethod, \App\Modules\Core\Controllers\Admin\SettingController::DOMESTIC_FEE_METHODS)) {
            $feePct = (float) ($feeConfig['fee_pct_' . $paymentMethod] ?? 0);
            $feeFixed = (float) ($feeConfig['fee_fixed_vnd'] ?? 0);
            $final_total += round($final_total * $feePct / 100) + $feeFixed;
        }

        // Ví USD là số dư thật riêng biệt (balance_usd) — thanh toán bằng ví này phải trừ đúng USD thật,
        // không quy đổi cosmetic qua VNĐ như các phương thức nội địa khác.
        $isWalletUsd = $paymentMethod === 'wallet_usd';
        $final_total_usd = $isWalletUsd ? round($final_total * \App\Helpers\CurrencyHelper::rate('USD'), 2) : null;

        if ($isWalletUsd) {
            if ($user->balance_usd < $final_total_usd) {
                return back()->with('error', 'Số dư ví USD không đủ! Vui lòng nạp thêm tiền vào ví USD.');
            }
        } elseif ($user->balance < $final_total) {
            return back()->with('error', 'Số dư không đủ! Vui lòng nạp thêm tiền vào ví.');
        }

        // --- CHECKOUT OTP VERIFICATION ---
        if ($user->checkout_otp_enabled && !session()->has('checkout_otp_verified')) {
            // Generate OTP
            $otp = sprintf("%06d", mt_rand(100000, 999999));
            \Illuminate\Support\Facades\Cache::put('checkout_otp_' . $user->id, $otp, now()->addMinutes(5));
            
            // Send Email
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\CheckoutOtpMail($otp, $final_total));

            // Lưu lại đúng phương thức khách đã chọn (đặc biệt ví VNĐ/ví USD) để bước xác thực OTP
            // gọi lại checkoutProcess() không bị rơi về mặc định 'wallet' (form OTP không có field payment_method).
            session()->put('checkout_pending_payment_method', $paymentMethod);

            return redirect()->route('cart.checkout.verify');
        }
        // ---------------------------------

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Trừ tiền user 1 lần cho tổng bill — đúng ví thật đang thanh toán (VNĐ hoặc USD).
            if ($isWalletUsd) {
                $user->balance_usd -= $final_total_usd;
            } else {
                $user->balance -= $final_total;
            }
            $user->points += round($final_total / 1000);
            $user->save();

            $boughtItems = [];
            $emailItems = [];
            $hasPending = false;

            foreach ($cart as $cartKey => $details) {
                $productId = $details['product_id'] ?? $cartKey;
                $product = Product::find($productId);
                if (!$product) continue;

                for ($i = 0; $i < $details['quantity']; $i++) {
                    $gameKey = $this->fulfillCartItem($product, $details, $user);

                    if ($gameKey) {
                        $itemAmount = $isWalletUsd
                            ? round($details['price'] * \App\Helpers\CurrencyHelper::rate('USD'), 2)
                            : $details['price'];

                        \App\Modules\Theme\Models\Transaction::create([
                            'user_id' => $user->id,
                            'amount' => -$itemAmount,
                            'currency' => $isWalletUsd ? 'USD' : 'VND',
                            'type' => 'purchase',
                            'description' => 'Mua ' . $product->name . ($details['variant_label'] ?? null ? ' (' . $details['variant_label'] . ')' : ''),
                            'status' => 'completed',
                            'reference_id' => 'ORD' . time() . $gameKey->id
                        ]);

                        if ($gameKey->status === 'pending_manual') {
                            $hasPending = true;
                        }

                        $boughtItems[] = $product->name;
                        $emailItems[] = ['name' => $product->name . ($details['variant_label'] ?? null ? ' (' . $details['variant_label'] . ')' : ''), 'price' => $details['price']];
                    } else {
                        throw new \Exception('Giao dịch tự động thất bại (có thể do đối tác bảo trì hoặc hết số dư). Hệ thống đã hoàn tiền, bạn vui lòng thử lại sau.');
                    }
                }
            }

            // Affiliate
            if ($user->referred_by) {
                $referrer = \App\Models\User::find($user->referred_by);
                if ($referrer) {
                    $commissionPercent = \App\Modules\Core\Models\Setting::getValue('affiliate_commission', 5);
                    $commission = round($total * ($commissionPercent / 100));
                    
                    if ($commission > 0) {
                        $referrer->balance += $commission;
                        $referrer->save();

                        \App\Modules\Theme\Models\Transaction::create([
                            'user_id' => $referrer->id,
                            'amount' => $commission,
                            'type' => 'affiliate_reward',
                            'description' => 'Hoa hồng từ user: ' . $user->name,
                            'status' => 'completed',
                            'reference_id' => 'AFF' . time() . $user->id
                        ]);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            if (\App\Modules\Core\Models\Setting::getValue('order_confirmation_email_enable', '1') == '1') {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderConfirmationMail($user, $emailItems, $final_total));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Gửi email xác nhận đơn hàng thất bại: ' . $e->getMessage());
                }
            }

            try {
                (new \App\Modules\Core\Services\WebPushService())->notifyAllAdmins(
                    '🛒 Đơn hàng mới',
                    $user->name . ' vừa mua ' . count($boughtItems) . ' sản phẩm - ' . number_format($final_total) . 'đ',
                    route('admin.orders')
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Push notify (đơn hàng mới) thất bại: ' . $e->getMessage());
            }

            try {
                (new \App\Modules\Core\Services\FcmService())->notifyAllAdmins(
                    '🛒 Đơn hàng mới',
                    $user->name . ' vừa mua ' . count($boughtItems) . ' sản phẩm - ' . number_format($final_total) . 'đ',
                    route('admin.orders')
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('FCM notify (đơn hàng mới) thất bại: ' . $e->getMessage());
            }

            session()->forget('cart');
            
            if ($applied_coupon) {
                $applied_coupon->increment('used_count');
                $user->coupons()->updateExistingPivot($applied_coupon->id, ['status' => 'used', 'used_at' => now()]);
                session()->forget('applied_coupon_id');
            }
            
            // Get the last bought product details for the toast
            $lastCartItem = end($cart);
            $lastProductId = $lastCartItem['product_id'] ?? array_key_last($cart);
            $lastProduct = $lastProductId ? \App\Modules\Theme\Models\Product::find($lastProductId) : null;
            
            $toastData = [
                'title' => count($boughtItems) > 1 ? count($boughtItems) . ' Tựa Game' : ($lastProduct->name ?? 'Game'),
                'message' => $hasPending ? 'Đơn hàng đang được xử lý...' : 'Đã gửi Key vào hòm đồ!',
                'image' => $lastProduct->header_image ?? 'https://placehold.co/100x100/1e293b/ffffff?text=Game',
                'time' => 'Vừa xong'
            ];

            $successMessage = $hasPending
                ? 'Thanh toán thành công ' . count($boughtItems) . ' sản phẩm! Một số đơn đang chờ xử lý, vui lòng xem trạng thái trong Kho Game.'
                : 'Thanh toán thành công ' . count($boughtItems) . ' sản phẩm! Bạn có thể xem Key trong Lịch sử mua hàng.';

            return redirect()->route('dashboard')
                ->with('success', $successMessage)
                ->with('purchase_toast', $toastData);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Lỗi thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Giao hàng theo đúng loại sản phẩm, trả về bản ghi GameKey đã "sold" (dùng chung
     * bảng game_keys làm nơi lưu kết quả giao hàng cho MỌI loại sản phẩm — key đơn giản
     * dùng cột key_code, VPN/eSIM/Thẻ dùng thêm cột delivery_data JSON).
     * Trả về null nếu giao hàng thất bại.
     */
    protected function fulfillCartItem(Product $product, array $details, $user): ?\App\Modules\Theme\Models\GameKey
    {
        $variantId = $details['variant_id'] ?? null;

        // Chế độ xử lý đơn áp dụng chung cho mọi loại sản phẩm cần gọi API đối tác ngoài
        // (Game/Giftcard/Subscription/Software/Thẻ nạp/VPN/eSIM). Ở chế độ thủ công, không gọi
        // API mà tạo thẳng đơn "chờ xử lý" để Admin/Staff tự nhập tay hoặc bấm lấy qua API riêng lẻ.
        $fulfillmentMode = \App\Modules\Core\Models\Setting::getValue('order_fulfillment_mode', 'manual');

        switch ($product->product_type) {
            case Product::TYPE_VPN:
                $variant = $product->vpnPackages()->find($variantId);
                if (!$variant) return null;

                // Lưu lại ngữ cảnh (server + gói) để Admin bấm "Lấy qua API" ở trang Đơn Hàng
                // sau này biết chính xác cần mua lại gói nào.
                $vpnRetryContext = ['retry_vpn_server_id' => $product->vpn_server_id, 'retry_package_key' => $variant->package_key];

                if ($fulfillmentMode === 'manual') {
                    return $product->keys()->create([
                        'key_code' => '',
                        'status' => 'pending_manual',
                        'delivery_data' => $vpnRetryContext,
                        'sold_to_user_id' => $user->id,
                        'sold_at' => now(),
                    ]);
                }

                $result = app(\App\Services\VpnPanelsService::class)->purchaseVpn($product->vpn_server_id, $variant->package_key);
                if (!$result) {
                    // Không huỷ cả đơn — chuyển sang "chờ xử lý" để Admin/Staff xử lý tay thay vì
                    // hoàn tiền và làm khách mất đơn hàng chỉ vì API đối tác lỗi tạm thời.
                    return $product->keys()->create([
                        'key_code' => '',
                        'status' => 'pending_manual',
                        'error_message' => 'Không thể khởi tạo VPN qua API',
                        'delivery_data' => $vpnRetryContext,
                        'sold_to_user_id' => $user->id,
                        'sold_at' => now(),
                    ]);
                }

                return $product->keys()->create([
                    'key_code' => $result['subscription_link'] ?? $result['username'] ?? 'N/A',
                    'status' => 'sold',
                    'delivery_data' => $result,
                    'sold_to_user_id' => $user->id,
                    'sold_at' => now(),
                ]);

            case Product::TYPE_ESIM:
                $variant = $product->esimPackages()->find($variantId);
                if (!$variant) return null;

                // Lưu lại mã gói eSIM để Admin bấm "Lấy qua API" ở trang Đơn Hàng biết cần mua lại gói nào.
                $esimRetryContext = ['retry_package_code' => $variant->package_code, 'package_name' => $variant->name];

                if ($fulfillmentMode === 'manual') {
                    return $product->keys()->create([
                        'key_code' => $variant->package_code,
                        'status' => 'pending_manual',
                        'delivery_data' => $esimRetryContext,
                        'sold_to_user_id' => $user->id,
                        'sold_at' => now(),
                    ]);
                }

                // Tạo record trước với status=processing để giữ chỗ, rồi gọi API tạo đơn.
                $gameKey = $product->keys()->create([
                    'key_code' => $variant->package_code,
                    'status' => 'processing',
                    'delivery_data' => $esimRetryContext,
                    'sold_to_user_id' => $user->id,
                    'sold_at' => now(),
                ]);

                $orderNo = app(\App\Services\EsimAccessService::class)->purchaseEsim($gameKey->id, $variant->package_code, 1);
                if (!$orderNo) {
                    // Chuyển sang "chờ xử lý" (thay vì "failed" + return null bị rollback mất) để
                    // Admin/Staff xử lý tay thay vì làm khách mất cả đơn hàng khi API lỗi.
                    $gameKey->update(['status' => 'pending_manual', 'error_message' => 'Không thể khởi tạo đơn eSIM qua API']);
                    return $gameKey;
                }

                $gameKey->update(['delivery_data' => ['order_no' => $orderNo, 'package_name' => $variant->name]]);
                \App\Jobs\PollEsimStatus::dispatch($gameKey->id)->delay(now()->addSeconds(8));

                return $gameKey; // status=processing — khách xem tiến độ ở trang đơn hàng

            case Product::TYPE_CARD:
                $variant = $product->cardPackages()->find($variantId);
                if (!$variant) return null;

                $telco = str_replace('card_', '', $product->wholesale_product_id);
                // Lưu lại nhà mạng + mệnh giá để Admin bấm "Lấy qua API" ở trang Đơn Hàng biết cần mua lại thẻ nào.
                $cardRetryContext = ['retry_telco' => $telco, 'retry_amount' => $variant->face_value];

                if ($fulfillmentMode === 'manual') {
                    return $product->keys()->create([
                        'key_code' => '',
                        'status' => 'pending_manual',
                        'delivery_data' => $cardRetryContext,
                        'sold_to_user_id' => $user->id,
                        'sold_at' => now(),
                    ]);
                }

                $partnerRid = 'kg' . $user->id . '_' . time() . rand(10, 99);
                $santhecao = app(\App\Services\SanthecaoService::class);

                $result = $santhecao->buyCardCode($telco, $variant->face_value, 1, $partnerRid);
                if ($santhecao->isPending($result)) {
                    $result = $santhecao->checkBuyCard($partnerRid, $result['trans_id'] ?? null);
                }

                if (!$result || (int) ($result['status'] ?? 0) !== 1) {
                    Log::error('Santhecao buyCardCode failed', ['telco' => $telco, 'result' => $result]);
                    // Không huỷ cả đơn — chuyển sang "chờ xử lý" để Admin/Staff xử lý tay thay vì
                    // hoàn tiền và làm khách mất đơn hàng chỉ vì API đối tác lỗi tạm thời.
                    return $product->keys()->create([
                        'key_code' => '',
                        'status' => 'pending_manual',
                        'error_message' => $result['desc'] ?? 'Lỗi API Santhecao không xác định',
                        'delivery_data' => $cardRetryContext,
                        'sold_to_user_id' => $user->id,
                        'sold_at' => now(),
                    ]);
                }

                $cards = $result['cards'] ?? [];
                $pin = collect($cards)->pluck('code')->join("\n---\n") ?: null;
                $serial = collect($cards)->pluck('serial')->join("\n---\n") ?: null;

                return $product->keys()->create([
                    'key_code' => $pin ?: ('Mã giao dịch: ' . ($result['trans_id'] ?? '')),
                    'status' => 'sold',
                    'delivery_data' => ['pin_code' => $pin, 'serial' => $serial, 'trans_id' => $result['trans_id'] ?? null],
                    'sold_to_user_id' => $user->id,
                    'sold_at' => now(),
                ]);

            default:
                // game / giftcard / subscription / software: dùng chung cơ chế key đơn qua Kinguin (đã có sẵn)
                $gameKey = $product->keys()->where('status', 'available')->first();

                if (!$gameKey) {
                    // Chế độ thủ công: KHÔNG tự gọi API mua key thật — tạo đơn "chờ xử lý" để Admin
                    // vào trang Đơn Hàng tự quyết định (nhập key tay hoặc bấm lấy qua API cho đơn đó).
                    if ($fulfillmentMode === 'manual') {
                        return $product->keys()->create([
                            'key_code' => '',
                            'status' => 'pending_manual',
                            'sold_to_user_id' => $user->id,
                            'sold_at' => now(),
                        ]);
                    }

                    $newKeyString = app(\App\Services\WholesaleProviderService::class)->purchaseKey($product->id);
                    if ($newKeyString) {
                        $gameKey = $product->keys()->create(['key_code' => $newKeyString, 'status' => 'available']);
                    }
                }

                if (!$gameKey) return null;

                $gameKey->status = 'sold';
                $gameKey->sold_to_user_id = $user->id;
                $gameKey->sold_at = now();
                $gameKey->save();

                return $gameKey;
        }
    }

    public function checkoutVerifyForm()
    {
        $user = Auth::user();
        if (!$user->checkout_otp_enabled) {
            return redirect()->route('cart.index');
        }

        if (!\Illuminate\Support\Facades\Cache::has('checkout_otp_' . $user->id)) {
            return redirect()->route('cart.index')->with('error', 'Mã OTP đã hết hạn hoặc không hợp lệ. Vui lòng thanh toán lại.');
        }

        return view('shop::theme.checkout_verify');
    }

    public function checkoutVerifyProcess(Request $request, \App\Services\WholesaleProviderService $providerService)
    {
        $user = Auth::user();
        $inputOtp = implode('', $request->input('otp', []));
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('checkout_otp_' . $user->id);

        if (!$cachedOtp || $inputOtp !== $cachedOtp) {
            return back()->with('error', 'Mã OTP không chính xác hoặc đã hết hạn.');
        }

        // OTP is valid
        \Illuminate\Support\Facades\Cache::forget('checkout_otp_' . $user->id);

        // Put a short-lived flag to bypass OTP check
        session()->put('checkout_otp_verified', true);

        // Khôi phục đúng phương thức thanh toán khách đã chọn trước khi qua bước OTP.
        $request->merge(['payment_method' => session()->pull('checkout_pending_payment_method', 'wallet')]);

        // Retry checkout process
        return $this->checkoutProcess($request, $providerService);
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper($request->code);
        $coupon = \App\Modules\Theme\Models\Coupon::where('code', $code)->first();
        
        if (!$coupon) {
            return back()->with('error', 'Mã giảm giá không tồn tại.');
        }
        
        if (!$coupon->isValid()) {
            return back()->with('error', 'Mã giảm giá đã hết hạn hoặc hết lượt sử dụng.');
        }

        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($coupon->min_order_amount > 0 && $total < $coupon->min_order_amount) {
            return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu ('.number_format($coupon->min_order_amount).'đ) để áp dụng mã này.');
        }

        session()->put('applied_coupon_id', $coupon->id);
        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }

    public function removeCoupon()
    {
        session()->forget('applied_coupon_id');
        return back()->with('success', 'Đã gỡ mã giảm giá.');
    }
}
