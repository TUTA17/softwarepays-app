<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(30)->withQueryString();

        return response()->json([
            'users' => $users->through(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'balance' => (float) $u->balance,
                'balance_usd' => (float) $u->balance_usd,
                'points' => $u->points,
                'created_at' => $u->created_at,
            ]),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
        ]);
    }

    // Cộng tiền thủ công vào ví khách hàng từ app (mirror của web admin) — dùng khi thanh toán tự động bị lỗi.
    public function addBalance(Request $request, $id)
    {
        $currency = $request->input('currency', 'VND');
        $minAmount = $currency === 'USD' ? 0.01 : 1000;

        $request->validate([
            'amount' => "required|numeric|min:{$minAmount}",
            'currency' => 'nullable|in:VND,USD',
            'note' => 'nullable|string|max:255',
        ], [
            'amount.required' => 'Vui lòng nhập số tiền.',
            'amount.numeric' => 'Số tiền không hợp lệ.',
            'amount.min' => $currency === 'USD' ? 'Số tiền phải lớn hơn 0.' : 'Số tiền tối thiểu là 1.000đ.',
        ]);

        $user = User::findOrFail($id);
        $amount = (float) $request->amount;
        $admin = $request->user();

        if ($currency === 'USD') {
            $user->increment('balance_usd', $amount);
        } else {
            $user->increment('balance', $amount);
        }

        $description = 'Admin cộng tiền thủ công (bù thanh toán tự động lỗi)'
            . ($admin ? ' - Thực hiện bởi: ' . $admin->name : '')
            . ($request->filled('note') ? ' - Ghi chú: ' . $request->note : '');

        $transaction = \App\Modules\Theme\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'deposit',
            'status' => 'completed',
            'description' => $description,
            'reference_id' => 'MANUAL' . time() . $user->id,
            'currency' => $currency,
        ]);

        if (\App\Modules\Core\Models\Setting::getValue('transaction_confirmation_email_enable', '1') == '1') {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TransactionConfirmationMail($user, $transaction));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gửi email xác nhận giao dịch (cộng tiền thủ công qua app) thất bại: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'balance' => (float) $user->balance,
            'balance_usd' => (float) $user->balance_usd,
        ]);
    }
}
