<?php

namespace App\Modules\Core\Controllers;

use App\Modules\Core\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; 


class SettingController extends Controller
{
    /**
     * Module config — giữ nguyên logic cũ
     */
    protected $module = [
        'code' => 'setting',
        'label' => 'Cấu hình chung',
        'tabs' => [
            'general_tab' => [
                'label' => 'Cấu hình chung',
                'icon' => 'settings',
                'td' => [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Tên website', 'required' => true],
                    ['name' => 'demo_mode', 'type' => 'select', 'label' => 'Bật chế độ Web Demo', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 12],
                    ['name' => 'hotline', 'type' => 'text', 'label' => 'Hotline', 'col' => 6],
                    ['name' => 'email', 'type' => 'text', 'label' => 'Email', 'col' => 6],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'Địa chỉ'],
                    ['name' => 'logo', 'type' => 'file_image', 'label' => 'Logo', 'col' => 6],
                    ['name' => 'favicon', 'type' => 'file_image', 'label' => 'Favicon', 'col' => 6],
                ]
            ],
            'social_tab' => [
                'label' => 'Mạng xã hội',
                'icon' => 'share',
                'td' => [
                    ['name' => 'facebook', 'type' => 'text', 'label' => 'Link Facebook', 'col' => 6],
                    ['name' => 'fanpage', 'type' => 'text', 'label' => 'Link Fanpage', 'col' => 6],
                    ['name' => 'zalo', 'type' => 'text', 'label' => 'Link Zalo', 'col' => 6],
                    ['name' => 'youtube', 'type' => 'text', 'label' => 'Link Youtube', 'col' => 6],
                    ['name' => 'instagram', 'type' => 'text', 'label' => 'Link Instagram', 'col' => 6],
                ]
            ],
            'social_login_tab' => [
                'label' => 'Đăng nhập MXH',
                'icon' => 'login',
                'td' => [
                    ['name' => 'google_login_enable', 'type' => 'select', 'label' => 'Đăng nhập bằng Google', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 12],
                    ['name' => 'google_client_id', 'type' => 'text', 'label' => 'Google Client ID', 'col' => 6],
                    ['name' => 'google_client_secret', 'type' => 'text', 'label' => 'Google Client Secret', 'col' => 6],
                    ['name' => 'github_login_enable', 'type' => 'select', 'label' => 'Đăng nhập bằng GitHub', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 12],
                    ['name' => 'github_client_id', 'type' => 'text', 'label' => 'GitHub Client ID', 'col' => 6],
                    ['name' => 'github_client_secret', 'type' => 'text', 'label' => 'GitHub Client Secret', 'col' => 6],
                ]
            ],
            'mail' => [
                'label' => 'Cấu hình gửi mail',
                'icon' => 'mail',
                'td' => [
                    ['name' => 'driver', 'type' => 'select', 'label' => 'Loại', 'options' => ['smtp' => 'SMTP', 'brevo' => 'Brevo API (Sendinblue)', 'resend' => 'Resend API']],
                    ['name' => 'brevo_api_key', 'type' => 'text', 'label' => 'Brevo API Key', 'des' => 'Nhập API Key từ brevo.com vào đây nếu bạn chọn loại là Brevo'],
                    ['name' => 'mail_name', 'type' => 'text', 'label' => 'Tên người gửi'],
                    ['name' => 'smtp_host', 'type' => 'text', 'label' => 'Máy chủ SMTP', 'col' => 6],
                    ['name' => 'smtp_port', 'type' => 'text', 'label' => 'Cổng', 'col' => 6],
                    ['name' => 'smtp_encryption', 'type' => 'select', 'label' => 'Mã hóa', 'options' => ['tls' => 'TLS', 'ssl' => 'SSL'], 'col' => 6],
                    ['name' => 'smtp_username', 'type' => 'text', 'label' => 'Tài khoản SMTP', 'col' => 6],
                    ['name' => 'smtp_password', 'type' => 'text', 'label' => 'Mật khẩu SMTP'],
                    ['name' => 'mailgun_domain', 'type' => 'text', 'label' => 'Domain Mailgun', 'col' => 6],
                    ['name' => 'mailgun_secret', 'type' => 'text', 'label' => 'Secret Key Mailgun', 'col' => 6],
                    ['name' => 'admin_emails', 'type' => 'textarea', 'label' => 'Email admin nhận thông báo', 'des' => 'Các mail cách nhau bởi dấu phẩy'],
                ]
            ],
            'email_notify_tab' => [
                'label' => 'Email Tự Động',
                'icon' => 'forward_to_inbox',
                'td' => [
                    ['name' => 'welcome_email_enable', 'type' => 'select', 'label' => 'Email chào mừng khi đăng ký', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 6],
                    ['name' => 'verify_email_enable', 'type' => 'select', 'label' => 'Bắt buộc xác minh OTP khi đăng ký', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 6, 'des' => 'Khi bật: khách phải nhập mã OTP gửi qua email mới hoàn tất đăng ký (giống xác thực khi thanh toán), tài khoản chỉ được tạo sau khi xác minh đúng mã.'],
                    ['name' => 'order_confirmation_email_enable', 'type' => 'select', 'label' => 'Email xác nhận đơn hàng', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 6],
                    ['name' => 'transaction_confirmation_email_enable', 'type' => 'select', 'label' => 'Email xác nhận giao dịch nạp tiền', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 6],
                ]
            ],
            'seo_tab' => [
                'label' => 'Cấu hình SEO',
                'icon' => 'travel_explore',
                'td' => [
                    ['name' => 'robots', 'type' => 'select', 'label' => 'Robots', 'options' => [
                        'noindex,nofollow' => 'noindex, nofollow',
                        'index,follow' => 'index, follow',
                        'index,nofollow' => 'index, nofollow',
                        'noindex,follow' => 'noindex, follow',
                    ]],
                    ['name' => 'default_meta_title', 'type' => 'text', 'label' => 'Meta Title mặc định'],
                    ['name' => 'default_meta_description', 'type' => 'textarea', 'label' => 'Meta Description mặc định'],
                    ['name' => 'default_meta_keywords', 'type' => 'text', 'label' => 'Meta Keywords mặc định'],
                    ['name' => 'google_analytics_id', 'type' => 'text', 'label' => 'Google Analytics ID', 'des' => 'Dạng G-XXXXXXXXXX, để trống nếu không dùng'],
                ]
            ],
            'admin_setting_tab' => [
                'label' => 'Cấu hình trang admin',
                'icon' => 'code',
                'td' => [
                    ['name' => 'admin_head_code', 'type' => 'textarea', 'label' => 'Code chèn vào head', 'rows' => 10],
                    ['name' => 'admin_footer_code', 'type' => 'textarea', 'label' => 'Code chèn vào footer', 'rows' => 10],
                ]
            ],
            'api_automation_tab' => [
                'label' => 'Tự động hóa & API',
                'icon' => 'smart_toy',
                'td' => [
                    ['name' => 'auto_fetch_games', 'type' => 'select', 'label' => 'Tự động lấy Game (Steam)', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'col' => 6],
                    ['name' => 'order_fulfillment_mode', 'type' => 'select', 'label' => 'Xử lý đơn hàng (Game/Giftcard/Subscription/Software/Thẻ nạp/eSIM/MXH)', 'options' => ['manual' => 'Thủ công (chờ Admin duyệt ở trang Đơn Hàng)', 'auto' => 'Tự động (gọi API mua ngay khi khách thanh toán)'], 'col' => 6, 'des' => 'Với Game/Giftcard/Subscription/Software: chỉ áp dụng khi kho Key trống (đã có sẵn Key thì luôn giao ngay). Với Thẻ nạp/eSIM/MXH: áp dụng cho mọi đơn vì các loại này không có kho sẵn. Nếu API đối tác lỗi ở chế độ Tự động, đơn cũng tự chuyển sang "chờ xử lý" thay vì huỷ, Admin/Staff xử lý tay ở trang Đơn Hàng.'],
                    ['name' => 'wholesale_mock_mode', 'type' => 'select', 'label' => 'Chế độ giả lập lấy Key', 'options' => ['1' => 'Bật (Sinh key giả)', '0' => 'Tắt (Gọi API thật)'], 'col' => 6],
                    ['name' => 'wholesale_profit_margin', 'type' => 'text', 'label' => 'Tỉ lệ lợi nhuận Game/Key (%)', 'col' => 6, 'des' => 'Hệ thống tự động lấy giá gốc nhập Key từ API cộng thêm % này để làm giá bán.'],
                    ['name' => 'wholesale_api_endpoint', 'type' => 'text', 'label' => 'API Endpoint (Wholesale)'],
                    ['name' => 'wholesale_api_key', 'type' => 'text', 'label' => 'API Key (Wholesale)'],
                ]
            ],
            'currency_tab' => [
                'label' => 'Tỷ giá quy đổi',
                'icon' => 'currency_exchange',
                'td' => [
                    ['name' => 'margin_percent_usd', 'type' => 'text', 'label' => 'Phần trăm cộng thêm khi quy đổi sang USD (%)', 'col' => 6, 'des' => 'Cộng thêm % vào tỷ giá thị trường thực khi khách xem giá/thanh toán bằng USD. Ví dụ nhập 5: khách trả bằng USD sẽ phải trả nhiều hơn 5% tương ứng cho cùng 1 sản phẩm giá VNĐ. Để trống hoặc 0 = dùng đúng tỷ giá thị trường, không cộng thêm.'],
                    ['name' => 'margin_percent_eur', 'type' => 'text', 'label' => 'Phần trăm cộng thêm khi quy đổi sang EUR (%)', 'col' => 6, 'des' => 'Cộng thêm % vào tỷ giá thị trường thực khi khách xem giá/thanh toán bằng EUR. Ví dụ nhập 5: khách trả bằng EUR sẽ phải trả nhiều hơn 5% tương ứng cho cùng 1 sản phẩm giá VNĐ. Để trống hoặc 0 = dùng đúng tỷ giá thị trường, không cộng thêm.'],
                ]
            ],
            'security_tab' => [
                'label' => 'Bảo mật & Captcha',
                'icon' => 'security',
                'td' => [
                    ['name' => 'recaptcha_site_key', 'type' => 'text', 'label' => 'Google reCAPTCHA Site Key', 'col' => 6, 'des' => 'Hoặc Cloudflare Turnstile Site Key'],
                    ['name' => 'recaptcha_secret_key', 'type' => 'text', 'label' => 'Google reCAPTCHA Secret Key', 'col' => 6, 'des' => 'Hoặc Cloudflare Turnstile Secret Key'],
                ]
            ],
        ]
    ];

    /**
     * GET /admin/settings — hiển thị form
     */
    public function index()
    {
        $tabs = Setting::getAllGrouped();

        // Tỷ giá thị trường thực (chưa cộng %) để trang Tỷ giá quy đổi hiển thị xem trước trực tiếp
        // khi admin gõ %, giúp cân đối số % cho hợp lý trước khi lưu.
        $liveRates = [
            'USD' => round(1 / \App\Helpers\CurrencyHelper::rateWithoutMargin('USD')),
            'EUR' => round(1 / \App\Helpers\CurrencyHelper::rateWithoutMargin('EUR')),
        ];

        return view('core::settings.index', [
            'liveRates' => $liveRates,
            'module' => $this->module,
            'tabs' => $tabs,
        ]);
    }

    /**
     * POST /admin/settings — lưu cấu hình
     */
    public function store(Request $request)
    {
        $rules = [
            'general_tab_email' => ['nullable', 'email:rfc,dns'],
            'general_tab_hotline' => ['nullable', 'regex:/^\d+$/'],
            'general_tab_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            'general_tab_favicon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg,ico', 'max:10240'],
            'general_tab_logo_delete' => ['nullable', 'in:0,1'],
            'general_tab_favicon_delete' => ['nullable', 'in:0,1'],
            'general_tab_logo_current' => ['nullable', 'string'],
            'general_tab_favicon_current' => ['nullable', 'string'],
            'mail_admin_emails' => ['nullable', 'string', function ($attribute, $value, $fail) {
                $emails = array_values(array_filter(array_map('trim', explode(',', (string) $value))));
                foreach ($emails as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $fail('Email admin không hợp lệ: ' . $email);
                        return;
                    }
                }
            }],
        ];

        foreach ($this->module['tabs'] as $type => $tab) {
            foreach ($tab['td'] as $field) {
                $inputName = $type . '_' . $field['name'];
        
                if ($field['type'] === 'text' || $field['type'] === 'textarea') {
                    $rules[$inputName] = $rules[$inputName] ?? ['nullable', 'string'];
                } elseif ($field['type'] === 'select') {
                    $allowed = array_keys($field['options'] ?? []);
                    $rules[$inputName] = $rules[$inputName] ?? (empty($allowed) ? ['nullable'] : ['nullable', Rule::in($allowed)]);
                } elseif ($field['type'] === 'file_image') {
                    $rules[$inputName] = $rules[$inputName] ?? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg,ico', 'max:10240'];
                    $rules[$inputName . '_delete'] = $rules[$inputName . '_delete'] ?? ['nullable', 'in:0,1'];
                    $rules[$inputName . '_current'] = $rules[$inputName . '_current'] ?? ['nullable', 'string'];
                }
            }
        }

        $validated = $request->validate($rules);

        $deleteExistingFile = function (string $publicPath): void {
            $publicPath = trim($publicPath);
            if ($publicPath === '') {
                return;
            }

            if (str_starts_with($publicPath, 'storage/')) {
                $relative = ltrim(substr($publicPath, strlen('storage/')), '/');
                Storage::disk('public')->delete($relative);
                return;
            }

            $absolute = public_path($publicPath);
            if (File::exists($absolute)) {
                File::delete($absolute);
            }
        };

        foreach ($this->module['tabs'] as $type => $tab) {
            foreach ($tab['td'] as $field) {
                $inputName = $type . '_' . $field['name'];

                if ($field['type'] === 'file_image') {
                    $deleteFlag = (string) ($validated[$inputName . '_delete'] ?? '0');
                    $currentValue = (string) ($validated[$inputName . '_current'] ?? '');

                    if ($request->hasFile($inputName)) {
                        $file = $request->file($inputName);
                        if ($file->isValid()) {
                            $deleteExistingFile($currentValue);

                            $filename = $field['name'] . '_' . now()->timestamp . '.' . $file->getClientOriginalExtension();
                            
                            // Use public_path() to ensure images are saved inside the public/ directory
                            $destinationPath = public_path('uploads/settings');
                            if (!File::exists($destinationPath)) {
                                File::makeDirectory($destinationPath, 0755, true);
                            }
                            $file->move($destinationPath, $filename);
                            
                            $value = 'uploads/settings/' . $filename;
                        } else {
                            $value = $currentValue;
                        }
                    } elseif ($deleteFlag === '1') {
                        $deleteExistingFile($currentValue);
                        $value = '';
                    } else {
                        $value = $currentValue;
                    }
                } else {
                    $value = (string) ($validated[$inputName] ?? '');
                }

                Setting::updateOrCreate(
                    ['name' => $field['name'], 'type' => $type],
                    ['value' => $value]
                );
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Cập nhật cấu hình thành công!');
    }
}
