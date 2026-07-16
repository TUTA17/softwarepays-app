<?php
namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Core\Models\Setting;

class SettingController extends Controller
{
public function paymentSettings()
    {
        $settings = \App\Modules\Core\Models\Setting::getAllGrouped();
        $paymentConfig = $settings['payment_tab'] ?? [];
        return view('core::admin.payment-settings', compact('paymentConfig'));
    }

public function savePaymentSettings(Request $request)
    {
        $request->validate([
            'sepay_logo' => 'nullable|string',
            'sepay_name' => 'required|string',
            'sepay_client_id' => 'required|string',
            'sepay_secret_key' => 'required|string',
            'bank_id' => 'required|string',
            'account_no' => 'required|string',
            'account_name' => 'required|string',
            'payment_notice' => 'nullable|string'
        ]);

        \App\Modules\Core\Models\Setting::setValue('sepay_logo', $request->sepay_logo);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_logo'], ['value' => $request->sepay_logo, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_name'], ['value' => $request->sepay_name, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_client_id'], ['value' => $request->sepay_client_id, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'sepay_secret_key'], ['value' => $request->sepay_secret_key, 'type' => 'payment_tab']);
        
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'bank_id'], ['value' => $request->bank_id, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'account_no'], ['value' => $request->account_no, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'account_name'], ['value' => $request->account_name, 'type' => 'payment_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'payment_notice'], ['value' => $request->payment_notice, 'type' => 'payment_tab']);

        return back()->with('success', 'Cập nhật cấu hình thanh toán thành công!');
    }

    // Danh sách phương thức nội địa dùng chung 1 mã VietQR (chỉ khác % phí hiển thị)
    public const DOMESTIC_FEE_METHODS = ['wallet', 'momo', 'zalopay', 'vnpay', 'vietqr', 'napas'];

    // Phương thức quốc tế: chỉ hiển thị phí ước tính, chưa xử lý thanh toán thật
    public const INTL_FEE_METHODS = ['paypal', 'bitcoin', 'ethereum', 'litecoin', 'usdt', 'solana'];

    public function savePaymentFeeSettings(Request $request)
    {
        foreach (self::DOMESTIC_FEE_METHODS as $method) {
            $val = (float) $request->input("fee_pct_{$method}", 0);
            \App\Modules\Core\Models\Setting::updateOrCreate(
                ['name' => "fee_pct_{$method}"],
                ['value' => $val, 'type' => 'payment_fee_tab']
            );
        }
        $fixedVnd = (float) $request->input('fee_fixed_vnd', 0);
        \App\Modules\Core\Models\Setting::updateOrCreate(
            ['name' => 'fee_fixed_vnd'],
            ['value' => $fixedVnd, 'type' => 'payment_fee_tab']
        );

        return back()->with('success', 'Cập nhật phí dịch vụ thành công!');
    }

    public function saveIntlPaymentFeeSettings(Request $request)
    {
        foreach (self::INTL_FEE_METHODS as $method) {
            foreach (['basic_pct', 'fixed_usd', 'intl_pct', 'fx_pct'] as $param) {
                $val = (float) $request->input("intl_{$method}_{$param}", 0);
                \App\Modules\Core\Models\Setting::updateOrCreate(
                    ['name' => "intl_{$method}_{$param}"],
                    ['value' => $val, 'type' => 'payment_fee_tab']
                );
            }
        }

        return back()->with('success', 'Cập nhật phí phương thức quốc tế thành công!');
    }

    public function affiliateSettings()
    {
        $settings = \App\Modules\Core\Models\Setting::getAllGrouped();
        $affiliateConfig = $settings['affiliate_tab'] ?? [];
        return view('core::admin.affiliate-settings', compact('affiliateConfig'));
    }

    public function saveAffiliateSettings(Request $request)
    {
        $request->validate([
            'referral_signup_bonus' => 'required|numeric|min:0',
            'affiliate_commission' => 'required|numeric|min:0|max:100',
        ]);

        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'referral_signup_bonus'], ['value' => $request->referral_signup_bonus, 'type' => 'affiliate_tab']);
        \App\Modules\Core\Models\Setting::updateOrCreate(['name' => 'affiliate_commission'], ['value' => $request->affiliate_commission, 'type' => 'affiliate_tab']);

        return back()->with('success', 'Cập nhật cấu hình Tiếp thị liên kết thành công!');
    }

}
