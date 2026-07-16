<?php

namespace App\Modules\Shop\Controllers\Theme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Theme\Models\Coupon;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index()
    {
        $now = now();
        $coupons = Coupon::where('is_public', true)
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>', $now);
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('used_count < usage_limit');
            })
            ->orderBy('id', 'desc')
            ->get();

        $savedCouponIds = [];
        if (Auth::check()) {
            $savedCouponIds = Auth::user()->coupons()->pluck('coupons.id')->toArray();
        }

        return view('shop::theme.coupons.index', compact('coupons', 'savedCouponIds'));
    }

    public function myCoupons()
    {
        $user = Auth::user();
        $coupons = $user->coupons()->orderByPivot('created_at', 'desc')->get();
        return view('shop::theme.coupons.my_coupons', compact('coupons'));
    }

    public function save(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để lưu mã.']);
        }

        $coupon = Coupon::findOrFail($id);
        
        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá này đã hết hạn hoặc hết lượt sử dụng.']);
        }

        $user = Auth::user();
        
        if ($user->coupons()->where('coupon_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Bạn đã lưu mã này rồi!']);
        }

        $user->coupons()->attach($id, ['status' => 'saved', 'created_at' => now(), 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Lưu mã thành công! Bạn có thể sử dụng ở phần Thanh toán.']);
    }
}
