<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Client\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('shop.cart', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->is_active || $product->available_keys == 0) {
            return back()->with('error', 'Sản phẩm này tạm thời hết hàng!');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
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
        $vat = round($total * 0.05);
        $final_total = $total + $vat;

        return view('shop.checkout', compact('cart', 'total', 'vat', 'final_total'));
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
        $vat = round($total * 0.05);
        $final_total = $total + $vat;

        if ($user->balance < $final_total) {
            return back()->with('error', 'Số dư không đủ! Vui lòng nạp thêm tiền vào ví.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Trừ tiền user 1 lần cho tổng bill
            $user->balance -= $final_total;
            $user->points += round($final_total / 1000);
            $user->save();

            $boughtItems = [];

            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                if (!$product) continue;

                for ($i = 0; $i < $details['quantity']; $i++) {
                    // Mua Key
                    $gameKey = $product->keys()->where('status', 'available')->first();

                    if (!$gameKey) {
                        $newKeyString = $providerService->purchaseKey($product->id);
                        if ($newKeyString) {
                            $gameKey = $product->keys()->create([
                                'key_code' => $newKeyString,
                                'status' => 'available'
                            ]);
                        }
                    }

                    if ($gameKey) {
                        $gameKey->status = 'sold';
                        $gameKey->sold_to_user_id = $user->id;
                        $gameKey->sold_at = now();
                        $gameKey->save();

                        \App\Modules\Client\Models\Transaction::create([
                            'user_id' => $user->id,
                            'amount' => -$product->price,
                            'type' => 'purchase',
                            'description' => 'Mua game: ' . $product->name,
                            'status' => 'completed',
                            'reference_id' => 'ORD' . time() . $gameKey->id
                        ]);
                        
                        $boughtItems[] = $product->name;
                    } else {
                        throw new \Exception('Sản phẩm ' . $product->name . ' tạm thời hết hàng trong kho đối tác.');
                    }
                }
            }

            // Affiliate
            if ($user->referred_by) {
                $referrer = \App\Modules\Client\Models\User::find($user->referred_by);
                if ($referrer) {
                    $commissionPercent = \App\Modules\Client\Models\Setting::where('key', 'affiliate_commission')->value('value') ?? 5;
                    $commission = round($total * ($commissionPercent / 100));
                    
                    if ($commission > 0) {
                        $referrer->balance += $commission;
                        $referrer->save();

                        \App\Modules\Client\Models\Transaction::create([
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
            session()->forget('cart');
            return redirect()->route('dashboard')->with('success', 'Thanh toán thành công ' . count($boughtItems) . ' sản phẩm! Bạn có thể xem Key trong Lịch sử mua hàng.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Lỗi thanh toán: ' . $e->getMessage());
        }
    }
}
