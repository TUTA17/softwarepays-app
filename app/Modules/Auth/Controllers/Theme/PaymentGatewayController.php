<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Transaction;
use App\Services\Payments\NowPaymentsService;
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

            // Ví chỉ còn 1 số dư USD duy nhất — quy đổi ngược về VNĐ-tương-đương để so với tổng đơn (VNĐ).
            $userBalanceVnd = $user->balance / \App\Helpers\CurrencyHelper::rate('USD');
            $shortfall = $finalTotal - $userBalanceVnd;
            return $shortfall > 0 ? $shortfall : 0.01; // luôn tạo giao dịch dương nhỏ nếu vô tình đã đủ
        }

        $amount = (float) $request->query('amount', 0);
        return $amount > 0 ? $amount : null;
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
            // Sàn tối thiểu $1 cho các cổng thanh toán quốc tế (crypto)
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

        // Ví chỉ còn 1 số dư USD duy nhất — Transaction vẫn ghi đúng tiền tệ gốc đã charge thật
        // (USD cho topup trực tiếp, VNĐ cho flow checkout cũ), nhưng cộng vào ví luôn quy đổi ra USD.
        $user->balance += $transaction->currency === 'USD'
            ? $transaction->amount
            : round($transaction->amount * \App\Helpers\CurrencyHelper::rate('USD'), 2);
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
