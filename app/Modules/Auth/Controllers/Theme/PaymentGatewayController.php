<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Transaction;
use App\Services\Payments\NowPaymentsService;
use App\Services\Payments\PaylioService;
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

    // Sàn tối thiểu USD riêng của từng provider Paylio — đã kiểm tra trực tiếp qua API live
    // (khác nhau đáng kể: paypal $5, stripe $2, binance $15, banxa $20).
    protected const PAYLIO_MIN_USD = [
        'stripe' => 2,
        'paypal' => 5,
        'binance' => 15,
        'banxa' => 20,
    ];

    // Paylio: khách trả bằng thẻ/PayPal/Chuyển khoản/Binance Pay... qua trang checkout hosted của Paylio,
    // tiền về thẳng ví USDC (Polygon) của mình — không cần tài khoản merchant PayPal/Stripe riêng.
    // provider: bắt buộc chọn 1 provider cụ thể (stripe/paypal/binance/banxa) để bỏ qua màn "chọn provider"
    // của chính Paylio (tương ứng các nút Thẻ/PayPal/Binance Pay/Chuyển khoản tách riêng trên site).
    public function paylioPay(Request $request, PaylioService $paylio)
    {
        $walletAddress = config('services.paylio.wallet_address');
        if (!$walletAddress) {
            return response()->json(['success' => false, 'message' => 'Paylio chưa được cấu hình ví nhận tiền.'], 500);
        }

        $provider = $request->query('provider');
        $minUsd = self::PAYLIO_MIN_USD[$provider] ?? null;

        $user = Auth::user();
        $purpose = $request->query('purpose', 'topup');
        $isWalletTopup = $purpose === 'topup';

        if ($isWalletTopup) {
            // Ví USD riêng — khách nhập thẳng số USD, không quy đổi qua VNĐ ở bất kỳ bước nào.
            $amountUsd = round((float) $request->query('amount', 0), 2);
            if ($amountUsd < 1) {
                return response()->json(['success' => false, 'message' => 'Không xác định được số tiền cần thanh toán.'], 422);
            }
            if ($minUsd && $amountUsd < $minUsd) {
                return response()->json(['success' => false, 'message' => "Số tiền tối thiểu cho phương thức này là \${$minUsd}."], 422);
            }
            $amountVnd = null;
        } else {
            $amountVnd = $this->resolveAmountVnd($request);
            if (!$amountVnd) {
                return response()->json(['success' => false, 'message' => 'Không xác định được số tiền cần thanh toán.'], 422);
            }
            $usdRate = \App\Helpers\CurrencyHelper::usdRate();
            // Sàn tối thiểu $1 chung cho các cổng thanh toán quốc tế, hoặc cao hơn nếu provider yêu cầu
            // (khách trả đúng số PHẢI charge, phần dư nếu có vẫn được cộng đủ vào ví — không mất tiền).
            $amountUsd = max(1, $minUsd ?? 1, round($amountVnd * $usdRate, 2));
        }

        $reference = 'PLIO' . $user->id . '_' . time();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $isWalletTopup ? $amountUsd : $amountVnd,
            'type' => 'deposit',
            'status' => 'pending',
            'description' => 'Nạp tiền qua Paylio' . ($isWalletTopup ? ' (USD)' : ''),
            'reference_id' => $reference,
            'currency' => $isWalletTopup ? 'USD' : 'VND',
            'metadata' => [
                'gateway' => 'paylio',
                'provider' => $provider,
                'purpose' => $purpose,
                'amount_usd' => $amountUsd,
            ],
        ]);

        // Nhúng sẵn ID giao dịch của MÌNH vào URL callback -> nhận diện giao dịch không phụ thuộc
        // vào bất kỳ tham số nào Paylio gửi kèm (query string callback không có chữ ký xác thực).
        $callbackUrl = route('payments.paylio.callback', ['transaction' => $transaction->id]);

        $result = $paylio->createWallet($walletAddress, $callbackUrl, $amountUsd, $user->email, $reference, $provider);

        if (!$result || empty($result['checkout_url'])) {
            $transaction->update(['status' => 'failed']);
            $errorMsg = $result['error'] ?? null;
            return response()->json(['success' => false, 'message' => $errorMsg ?: 'Không thể khởi tạo thanh toán Paylio. Vui lòng thử lại sau.'], 500);
        }

        $transaction->update(['metadata' => array_merge($transaction->metadata, [
            'paylio_payment_id' => $result['payment_id'] ?? null,
            'paylio_ipn_token' => $result['ipn_token'] ?? null,
        ])]);

        return response()->json(['success' => true, 'checkout_url' => $result['checkout_url']]);
    }

    // Paylio GET-redirect khách về đây sau khi hoàn tất (hoặc hủy) thanh toán trên trang hosted của họ.
    // Tài liệu API nói rõ query string đi kèm (ipn_token/status) chỉ là gợi ý, KHÔNG có chữ ký xác thực
    // -> tuyệt đối không tin trực tiếp, luôn gọi lại GET /payment-status để lấy trạng thái thật.
    // Trang này được mở trong POPUP (cùng domain với trang checkout/ví) -> tự gọi lại
    // window.opener.paylioPaymentCompleted() rồi đóng chính nó, xem resources/views/payments/paylio-return.blade.php.
    public function paylioCallback(Request $request, int $transaction, PaylioService $paylio)
    {
        $tx = Transaction::where('id', $transaction)->first();

        if (!$tx) {
            return view('payments.paylio-return', [
                'success' => false,
                'message' => 'Giao dịch không tồn tại.',
                'fallbackUrl' => route('wallet.show'),
            ]);
        }

        $fallbackUrl = ($tx->metadata['purpose'] ?? null) === 'checkout' ? route('cart.checkout') : route('wallet.show');

        // Đã xử lý xong trước đó (VD: khách bấm lại nút back rồi mở lại trang callback cũ).
        if ($tx->status !== 'pending') {
            return view('payments.paylio-return', [
                'success' => $tx->status === 'completed',
                'message' => $tx->status === 'completed' ? 'Giao dịch đã được xử lý trước đó.' : 'Giao dịch đã bị hủy hoặc thất bại.',
                'fallbackUrl' => $fallbackUrl,
            ]);
        }

        $ipnToken = $tx->metadata['paylio_ipn_token'] ?? null;
        $paymentId = $tx->metadata['paylio_payment_id'] ?? null;
        $status = $paylio->getStatus($ipnToken, $paymentId);

        if (!$status || ($status['status'] ?? null) !== 'paid') {
            if (($status['status'] ?? null) === 'canceled') {
                $tx->update(['status' => 'cancelled']);
            }
            return view('payments.paylio-return', [
                'success' => false,
                'message' => 'Thanh toán Paylio chưa hoàn tất hoặc đã bị hủy.',
                'fallbackUrl' => $fallbackUrl,
            ]);
        }

        $this->completeDeposit($tx);

        // Chỉ dùng khi popup bị chặn (fallback không có window.opener) -> trang callback tự
        // điều hướng sang $fallbackUrl; cờ này để checkout.blade.php tự bấm nốt nút Thanh Toán Bằng Ví.
        if (($tx->metadata['purpose'] ?? null) === 'checkout') {
            session()->flash('auto_checkout', true);
        }

        return view('payments.paylio-return', [
            'success' => true,
            'message' => 'Nạp tiền qua Paylio thành công!',
            'fallbackUrl' => $fallbackUrl,
        ]);
    }

    // Trả mức tối thiểu THẬT (theo coin) để hiển thị đúng ngay trên form trước khi khách bấm nạp —
    // trước đây form chỉ hiện sàn $1 chung chung, còn số thật (theo NOWPayments) chỉ áp lặng lẽ ở
    // backend sau khi submit, khiến số tiền thực nạp khác với số khách thấy lúc nhập.
    public function nowpaymentsMinAmount(Request $request, string $method, NowPaymentsService $nowPayments)
    {
        $payCurrency = NowPaymentsService::CURRENCY_MAP[$method] ?? null;
        if (!$payCurrency) {
            return response()->json(['success' => false, 'message' => 'Loại tiền không hợp lệ.'], 422);
        }

        $minUsd = $nowPayments->getMinAmountUsd($payCurrency);

        return response()->json([
            'success' => true,
            'min_usd' => $minUsd ? round($minUsd * 1.05, 2) : 1,
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
