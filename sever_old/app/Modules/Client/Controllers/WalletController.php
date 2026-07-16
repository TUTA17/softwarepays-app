<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Client\Models\Transaction;

class WalletController extends Controller
{
    public function show()
    {
        return view('client::wallet.index');
    }

    public function deposit(Request $request)
    {
        // ... (Keep existing fake deposit for dev if needed, or remove later)
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000',
        ]);

        $user = Auth::user();
        $user->balance += $request->amount;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'deposit',
            'status' => 'completed',
            'description' => 'Nạp tiền vào ví qua hệ thống',
            'reference_id' => 'DEP' . time() . rand(1000, 9999)
        ]);

        return redirect()->route('dashboard')->with('success', 'Nạp tiền thành công ' . number_format($request->amount) . 'đ vào ví!');
    }

    // Webhook nhận thông báo chuyển khoản (VD: SePay / Casso)
    public function webhook(Request $request)
    {
        // Lấy Secret Key từ Cấu hình
        $secretKey = \App\Modules\Core\Models\Setting::getValue('sepay_secret_key', 'spsk_test_cCAXsNHwBUjNLrSCoAPh75NFiJ7u3w7T');
        
        // Xác thực Header
        $headerToken = $request->header('Authorization');
        if ($headerToken !== 'Apikey ' . $secretKey) {
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

        // Kiểm tra xem transaction đã xử lý chưa
        if (Transaction::where('reference_id', $reference)->exists()) {
            return response()->json(['success' => true, 'message' => 'Đã xử lý trước đó']);
        }

        // Lọc ID User từ nội dung (VD: NAPTIEN 123)
        preg_match('/NAPTIEN\s+(\d+)/', $content, $matches);
        if (isset($matches[1])) {
            $userId = $matches[1];
            $user = \App\Modules\Client\Models\User::find($userId);
            
            if ($user) {
                // Cộng tiền
                $user->balance += $amount;
                $user->save();

                // Lưu Transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'deposit',
                    'status' => 'completed',
                    'description' => 'Nạp tiền qua chuyển khoản tự động',
                    'reference_id' => $reference
                ]);

                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'User not found or invalid format']);
    }
}
