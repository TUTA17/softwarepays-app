<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Transaction;
use App\Services\Payments\NowPaymentsService;
use App\Services\Payments\PaypalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentGatewayController extends Controller
{
    // purpose=checkout: chỉ nạp đúng phần còn thiếu để đủ trả cho giỏ hàng hiện tại.
    // purpose=topup: nạp theo số tiền khách nhập ở trang Ví.
    protected function resolveAmountVnd(Request $request): ?float
    {
        $user = Auth::user();

        if ($request->query('purpose') === 'checkout') {
            $cart = session()->get('cart', []);
            if (empty($cart)) return null;

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $discount = 0;
            if (session()->has('applied_coupon_id')) {
                $coupon = \App\Modules\Theme\Models\Coupon::find(session('applied_coupon_id'));
                if ($coupon && $coupon->isValid() && $total >= $coupon->min_order_amount) {
                    $discount = $coupon->calculateDiscount($total);
                }
            }
            $finalTotal = $total - $discount;

            $shortfall = $finalTotal - $user->balance;
            return $shortfall > 0 ? $shortfall : 0.01; // luôn tạo giao dịch dương nhỏ nếu vô tình đã đủ
        }

        $amount = (float) $request->query('amount', 0);
        return $amount > 0 ? $amount : null;
    }

    // Dùng chung cho cả flow redirect cũ (paypalPay) và flow popup JS SDK mới (paypalCreateOrderApi):
    // tính số tiền + tạo Transaction pending, KHÔNG gọi PayPal API (để 2 nơi tự chọn landing_page/flow riêng).
    protected function buildPaypalTransaction(Request $request): ?array
    {
        $user = Auth::user();
        $purpose = $request->query('purpose', 'topup');

        // purpose=topup: ví USD riêng — khách nhập thẳng số USD, PayPal charge đúng số đó,
        // KHÔNG quy đổi qua VNĐ ở bất kỳ bước nào (khác với purpose=checkout bên dưới).
        if ($purpose === 'topup') {
            $amountUsd = round((float) $request->query('amount', 0), 2);
            if ($amountUsd < 1) return null;

            $reference = 'PP' . $user->id . '_' . time();

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amountUsd,
                'type' => 'deposit',
                'status' => 'pending',
                'description' => 'Nạp tiền qua PayPal (USD)',
                'reference_id' => $reference,
                'currency' => 'USD',
                'metadata' => [
                    'gateway' => 'paypal',
                    'purpose' => 'topup',
                    'amount_usd' => $amountUsd,
                ],
            ]);

            return ['transaction' => $transaction, 'amountInCurrency' => $amountUsd, 'currency' => 'USD', 'reference' => $reference];
        }

        // purpose=checkout: trả bù thiếu hụt giỏ hàng — giữ nguyên hành vi cũ, quy đổi ra VNĐ
        // để cộng thẳng vào ví VNĐ và tự động hoàn tất đơn hàng.
        $amountVnd = $this->resolveAmountVnd($request);
        if (!$amountVnd) return null;

        $usdRate = \App\Helpers\CurrencyHelper::usdRate();
        // Sàn tối thiểu $1 cho các cổng thanh toán quốc tế (PayPal, crypto)
        $amountUsd = max(1, round($amountVnd * $usdRate, 2));

        // Thanh toán bằng tiền tệ địa phương (PayPal hỗ trợ) theo ngôn ngữ đang chọn của khách
        $currency = \App\Helpers\CurrencyHelper::paypalCurrencyForSelection(session('currency', 'VND'), session('locale', 'vi'));
        $currencyRate = \App\Helpers\CurrencyHelper::rate($currency);
        $decimals = $currency === 'JPY' ? 0 : 2;
        // Sàn tối thiểu tương đương $1 USD, quy đổi sang tiền tệ đích
        $floorInCurrency = $currencyRate === 0.0 ? 1 : (1 * $currencyRate / $usdRate);
        $amountInCurrency = round(max($floorInCurrency, $amountVnd * $currencyRate), $decimals);

        // Nếu sàn tối thiểu đẩy số tiền charge thực tế lên cao hơn $amountVnd ban đầu, phải quy đổi ngược lại
        // để cộng đúng số tiền khách THỰC SỰ trả vào ví — nếu không khách trả nhiều hơn nhưng chỉ được cộng ít hơn.
        if ($currencyRate > 0) {
            $amountVnd = max($amountVnd, round($amountInCurrency / $currencyRate));
        }

        $reference = 'PP' . $user->id . '_' . time();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amountVnd,
            'type' => 'deposit',
            'status' => 'pending',
            'description' => 'Nạp tiền qua PayPal',
            'reference_id' => $reference,
            'currency' => 'VND',
            'metadata' => [
                'gateway' => 'paypal',
                'purpose' => 'checkout',
                'amount_usd' => $amountUsd,
                'currency' => $currency,
                'amount_in_currency' => $amountInCurrency,
            ],
        ]);

        return compact('transaction', 'amountInCurrency', 'currency', 'reference');
    }

    public function paypalPay(Request $request, PaypalService $paypal)
    {
        $built = $this->buildPaypalTransaction($request);
        if (!$built) {
            return back()->with('error', 'Không xác định được số tiền cần thanh toán.');
        }
        ['transaction' => $transaction, 'amountInCurrency' => $amountInCurrency, 'currency' => $currency] = $built;

        // 'card' = khách chọn nút "Thẻ Visa/Mastercard" riêng -> vào thẳng form nhập thẻ khách (BILLING),
        // bỏ qua màn đăng nhập PayPal; mặc định vẫn ưu tiên đăng nhập PayPal (LOGIN).
        $landingPage = $request->query('flow') === 'card' ? 'BILLING' : 'LOGIN';

        $order = $paypal->createOrder(
            $amountInCurrency,
            $currency,
            $transaction->reference_id,
            route('payments.paypal.return', ['tx' => $transaction->id]),
            route('payments.paypal.cancel', ['tx' => $transaction->id]),
            $landingPage
        );

        if (!$order) {
            $transaction->update(['status' => 'failed']);
            return back()->with('error', 'Không thể khởi tạo thanh toán PayPal. Vui lòng thử lại sau.');
        }

        $transaction->update(['metadata' => array_merge($transaction->metadata, ['paypal_order_id' => $order['order_id']])]);

        return redirect()->away($order['approve_url']);
    }

    // Tạo order cho PayPal JS SDK (popup, không rời trang) — trả về đúng {id: order_id} mà
    // callback createOrder() của paypal.Buttons() cần để mở popup xác nhận thanh toán.
    public function paypalCreateOrderApi(Request $request, PaypalService $paypal)
    {
        $built = $this->buildPaypalTransaction($request);
        if (!$built) {
            return response()->json(['success' => false, 'message' => 'Không xác định được số tiền cần thanh toán.'], 422);
        }
        ['transaction' => $transaction, 'amountInCurrency' => $amountInCurrency, 'currency' => $currency] = $built;

        $order = $paypal->createOrder(
            $amountInCurrency,
            $currency,
            $transaction->reference_id,
            route('payments.paypal.return', ['tx' => $transaction->id]),
            route('payments.paypal.cancel', ['tx' => $transaction->id])
        );

        if (!$order) {
            $transaction->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Không thể khởi tạo thanh toán PayPal. Vui lòng thử lại sau.'], 500);
        }

        $transaction->update(['metadata' => array_merge($transaction->metadata, ['paypal_order_id' => $order['order_id']])]);

        return response()->json(['success' => true, 'id' => $order['order_id'], 'tx' => $transaction->id]);
    }

    // Capture order sau khi khách xác nhận trong popup PayPal — gọi từ callback onApprove() phía JS.
    public function paypalCaptureOrderApi(Request $request, PaypalService $paypal)
    {
        $request->validate(['orderID' => 'required|string', 'tx' => 'required|integer']);

        $transaction = Transaction::where('id', $request->tx)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$transaction || ($transaction->metadata['paypal_order_id'] ?? null) !== $request->orderID) {
            return response()->json(['success' => false, 'message' => 'Giao dịch không hợp lệ hoặc đã xử lý.'], 404);
        }

        if (!$paypal->captureOrder($request->orderID)) {
            $transaction->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Thanh toán PayPal không thành công.'], 500);
        }

        $this->completeDeposit($transaction);

        return response()->json(['success' => true, 'purpose' => $transaction->metadata['purpose'] ?? 'topup']);
    }

    public function paypalReturn(Request $request, PaypalService $paypal)
    {
        $transaction = Transaction::where('id', $request->query('tx'))
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            return redirect()->route('wallet.show')->with('error', 'Giao dịch không hợp lệ hoặc đã xử lý.');
        }

        $orderId = $transaction->metadata['paypal_order_id'] ?? null;
        if (!$orderId || !$paypal->captureOrder($orderId)) {
            $transaction->update(['status' => 'failed']);
            return redirect()->route('wallet.show')->with('error', 'Thanh toán PayPal không thành công.');
        }

        $this->completeDeposit($transaction);

        if (($transaction->metadata['purpose'] ?? null) === 'checkout') {
            return redirect()->route('cart.checkout')->with('success', 'Nạp tiền qua PayPal thành công! Vui lòng bấm Thanh Toán Bằng Ví để hoàn tất đơn hàng.')->with('auto_checkout', true);
        }

        return redirect()->route('wallet.show')->with('success', 'Nạp tiền qua PayPal thành công!');
    }

    public function paypalCancel(Request $request)
    {
        Transaction::where('id', $request->query('tx'))
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        return redirect()->route('wallet.show')->with('error', 'Đã hủy thanh toán PayPal.');
    }

    public function nowpaymentsPay(Request $request, NowPaymentsService $nowPayments)
    {
        $request->validate(['method' => 'required|string']);

        if (!array_key_exists($request->method, NowPaymentsService::CURRENCY_MAP)) {
            return response()->json(['success' => false, 'message' => 'Phương thức không hỗ trợ.'], 422);
        }

        $user = Auth::user();
        $purpose = $request->query('purpose', 'topup');
        $isWalletTopup = $purpose === 'topup';

        if ($isWalletTopup) {
            // Ví USD riêng — khách nhập thẳng số USD, không quy đổi qua VNĐ ở bất kỳ bước nào.
            $orderAmountUsd = round((float) $request->query('amount', 0), 2);
            if ($orderAmountUsd < 1) {
                return response()->json(['success' => false, 'message' => 'Không xác định được số tiền cần thanh toán.'], 422);
            }
            $amountUsd = $orderAmountUsd;
        } else {
            $amountVnd = $this->resolveAmountVnd($request);
            if (!$amountVnd) {
                return response()->json(['success' => false, 'message' => 'Không xác định được số tiền cần thanh toán.'], 422);
            }

            $usdRate = \App\Helpers\CurrencyHelper::usdRate();
            // Sàn tối thiểu $1 cho các cổng thanh toán quốc tế (PayPal, crypto)
            $amountUsd = max(1, round($amountVnd * $usdRate, 2));
            $orderAmountUsd = $amountUsd;
        }

        // NOWPayments tự áp mức tối thiểu riêng cho từng coin (biến động theo phí mạng, có lúc >$18)
        // -> phải nâng lên đúng mức đó trước khi tạo payment, nếu không sẽ bị từ chối AMOUNT_MINIMAL_ERROR.
        // Cộng thêm 5% dự phòng vì tỷ giá có thể biến động giữa lúc kiểm tra và lúc NOWPayments thực sự tạo hoá đơn.
        $payCurrency = NowPaymentsService::CURRENCY_MAP[$request->method];
        $minUsd = $nowPayments->getMinAmountUsd($payCurrency);
        if ($minUsd !== null && $minUsd > 0) {
            $amountUsd = max($amountUsd, round($minUsd * 1.05, 2));
        }
        $wasBumpedToMinimum = $amountUsd > $orderAmountUsd;

        if (!$isWalletTopup) {
            // purpose=checkout: nếu sàn tối thiểu đẩy số tiền charge thực tế lên cao hơn $amountVnd ban đầu,
            // phải quy đổi ngược lại để cộng đúng số tiền khách THỰC SỰ trả vào ví VNĐ.
            if ($usdRate > 0) {
                $amountVnd = max($amountVnd, round($amountUsd / $usdRate));
            }
        }

        $reference = 'CRYPTO' . $user->id . '_' . time();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $isWalletTopup ? $amountUsd : $amountVnd,
            'type' => 'deposit',
            'status' => 'pending',
            'description' => 'Nạp tiền qua ' . ucfirst($request->method) . ($isWalletTopup ? ' (USD)' : ''),
            'reference_id' => $reference,
            'currency' => $isWalletTopup ? 'USD' : 'VND',
            'metadata' => [
                'gateway' => 'nowpayments',
                'method' => $request->method,
                'purpose' => $purpose,
                'amount_usd' => $amountUsd,
            ],
        ]);

        $payment = $nowPayments->createPayment($amountUsd, $request->method, $reference, route('payments.nowpayments.ipn'));

        if (!$payment || empty($payment['pay_address'])) {
            $transaction->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Không thể khởi tạo thanh toán. Vui lòng thử lại sau.'], 500);
        }

        $transaction->update(['metadata' => array_merge($transaction->metadata, ['nowpayments_payment_id' => $payment['payment_id']])]);

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'pay_address' => $payment['pay_address'],
            'pay_amount' => $payment['pay_amount'],
            'pay_currency' => $payment['pay_currency'],
            'min_bumped' => $wasBumpedToMinimum,
            'order_amount_usd' => $orderAmountUsd,
            'charged_amount_usd' => $amountUsd,
        ]);
    }

    public function nowpaymentsStatus(Request $request, $transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->where('user_id', Auth::id())->first();
        if (!$transaction) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $transaction->status,
            'purpose' => $transaction->metadata['purpose'] ?? 'topup',
        ]);
    }

    public function nowpaymentsIpn(Request $request, NowPaymentsService $nowPayments)
    {
        $payload = $request->all();
        $signature = $request->header('x-nowpayments-sig');

        if (!$nowPayments->verifyIpnSignature($payload, $signature)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $orderId = $payload['order_id'] ?? null;
        $status = $payload['payment_status'] ?? null;

        $transaction = Transaction::where('reference_id', $orderId)->first();
        if (!$transaction || $transaction->status !== 'pending') {
            return response()->json(['success' => true]); // đã xử lý trước đó hoặc không tìm thấy
        }

        if (in_array($status, ['finished', 'confirmed'])) {
            $this->completeDeposit($transaction);
        } elseif (in_array($status, ['failed', 'expired', 'refunded'])) {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }

    protected function completeDeposit(Transaction $transaction): void
    {
        $transaction->update(['status' => 'completed']);
        $user = $transaction->user;
        if ($transaction->currency === 'USD') {
            $user->balance_usd += $transaction->amount;
        } else {
            $user->balance += $transaction->amount;
        }
        $user->save();

        if (\App\Modules\Core\Models\Setting::getValue('transaction_confirmation_email_enable', '1') == '1') {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TransactionConfirmationMail($user, $transaction));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gửi email xác nhận giao dịch thất bại: ' . $e->getMessage());
            }
        }
    }
}
