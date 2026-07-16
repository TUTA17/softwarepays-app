<?php

namespace App\Modules\Auth\Controllers\Theme;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Theme\Models\Transaction;

class WalletController extends Controller
{
    public function show()
    {
        $transactions = Transaction::where('user_id', Auth::id())
                                   ->where('type', 'deposit')
                                   ->orderBy('created_at', 'desc')
                                   ->take(20)
                                   ->get();
        $paypalCurrency = \App\Helpers\CurrencyHelper::paypalCurrencyForSelection(session('currency', 'VND'), session('locale', 'vi'));
        return view('wallet.index', compact('transactions', 'paypalCurrency'));
    }

    public function createInvoice(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:100000000',
        ]);

        $user = Auth::user();
        
        // Generate a unique reference code, preferably containing user ID
        $reference = 'NAPTIEN' . $user->id . '_' . time() . rand(10, 99);

        // Mark any old pending transactions as failed to avoid clutter
        Transaction::where('user_id', $user->id)
                   ->where('type', 'deposit')
                   ->where('status', 'pending')
                   ->update(['status' => 'failed']);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'deposit',
            'status' => 'pending',
            'description' => 'Nạp tiền qua chuyển khoản tự động',
            'reference_id' => $reference
        ]);

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'reference' => $reference,
            'amount' => $request->amount
        ]);
    }

    public function cancelTransaction(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();
            
        if ($transaction) {
            $transaction->status = 'cancelled';
            $transaction->save();
            return response()->json(['success' => true, 'message' => 'Đã hủy giao dịch.']);
        }
        
        return response()->json(['success' => false, 'message' => 'Không tìm thấy giao dịch hoặc đã hoàn thành.'], 404);
    }


    // Webhook nhận thông báo chuyển khoản (VD: SePay / Casso)
    public function webhook(Request $request)
    {
        // Lấy Secret Key từ Cấu hình — không dùng giá trị mặc định vì mã nguồn có thể bị lộ,
        // nếu admin chưa cấu hình secret riêng thì từ chối toàn bộ request (fail closed).
        $secretKey = \App\Modules\Core\Models\Setting::getValue('sepay_secret_key');

        // Xác thực Header
        $headerToken = $request->header('Authorization');
        if (!$secretKey || $headerToken !== 'Apikey ' . $secretKey) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Code mẫu cho SePay Webhook
        $data = $request->all();
        
        // Bỏ qua nếu không có thông tin
        if (!isset($data['content']) || !isset($data['transferAmount'])) {
            return response()->json(['success' => false, 'message' => 'Invalid payload']);
        }

        $content = strtoupper($data['content']);
        $amount = (float) $data['transferAmount'];
        $reference = $data['referenceCode'] ?? ('BNK' . time());

        // Nếu transaction đã tồn tại với reference này và đã completed
        if (Transaction::where('reference_id', $reference)->where('status', 'completed')->exists()) {
            return response()->json(['success' => true, 'message' => 'Đã xử lý trước đó']);
        }

        // Cố gắng tìm giao dịch pending khớp với user_id và số tiền
        preg_match('/NAPTIEN\s*(\d+)/', $content, $matches);
        $userId = $matches[1] ?? null;

        if ($userId) {
            $user = \App\Modules\Theme\Models\User::find($userId);
            if ($user) {
                // Find a pending transaction for this user with matching amount
                $pendingTx = Transaction::where('user_id', $user->id)
                                        ->where('type', 'deposit')
                                        ->where('status', 'pending')
                                        ->where('amount', $amount)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

                if ($pendingTx) {
                    $pendingTx->status = 'completed';
                    $pendingTx->reference_id = $reference; // Save actual bank ref
                    $pendingTx->save();
                } else {
                    // Create new if not found (maybe they didn't generate QR on site but manually sent)
                    Transaction::create([
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'type' => 'deposit',
                        'status' => 'completed',
                        'description' => 'Nạp tiền qua chuyển khoản tự động (Webhook)',
                        'reference_id' => $reference
                    ]);
                }

                $user->balance += $amount;
                $user->save();

                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'User not found or invalid format']);
    }
}
