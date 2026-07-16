<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Theme\Models\Coupon;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendCouponEmail;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('id', 'desc')->paginate(20);
        return view('shop::admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'is_public' => $request->has('is_public'),
            'is_active' => $request->has('is_active'),
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        if ($request->has('send_email') && $request->is_public) {
            // Push to queue
            SendCouponEmail::dispatch($coupon);
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Mã giảm giá đã được tạo thành công!');
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:coupons,code,'.$id,
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'is_public' => $request->has('is_public'),
            'is_active' => $request->has('is_active'),
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Mã giảm giá đã được cập nhật!');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Đã xóa mã giảm giá!');
    }
}
