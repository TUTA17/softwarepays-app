<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ép Laravel sử dụng đúng APP_URL, không chèn thêm /public/
        if (config('app.url')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            if (str_contains(config('app.url'), 'https://')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        Schema::defaultStringLength(191);
        
        // Dynamically load mail settings from the database
        try {
            if (Schema::hasTable('settings')) {
                // Dynamically load mail settings
                $mailDriver = \App\Modules\Core\Models\Setting::where('name', 'driver')->where('type', 'mail')->value('value');
                if ($mailDriver) {
                    config([
                        'mail.default' => $mailDriver,
                        'mail.mailers.smtp.host' => \App\Modules\Core\Models\Setting::where('name', 'smtp_host')->value('value'),
                        'mail.mailers.smtp.port' => \App\Modules\Core\Models\Setting::where('name', 'smtp_port')->value('value'),
                        'mail.mailers.smtp.encryption' => \App\Modules\Core\Models\Setting::where('name', 'smtp_encryption')->value('value'),
                        'mail.mailers.smtp.username' => \App\Modules\Core\Models\Setting::where('name', 'smtp_username')->value('value'),
                        'mail.mailers.smtp.password' => \App\Modules\Core\Models\Setting::where('name', 'smtp_password')->value('value'),
                        'mail.from.address' => \App\Modules\Core\Models\Setting::where('name', 'smtp_username')->value('value'),
                        'mail.from.name' => \App\Modules\Core\Models\Setting::where('name', 'mail_name')->value('value'),
                        'services.resend.key' => \App\Modules\Core\Models\Setting::where('name', 'resend_api_key')->value('value'),
                        'services.brevo.key' => \App\Modules\Core\Models\Setting::where('name', 'brevo_api_key')->value('value'),
                    ]);
                    
                    // Bắt buộc Laravel xóa cache mailer cũ và nạp lại cấu hình mới từ DB
                    \Illuminate\Support\Facades\Mail::purge();
                }

                // Dynamically load Social Login settings
                config([
                    'services.google' => [
                        'client_id' => \App\Modules\Core\Models\Setting::where('name', 'google_client_id')->value('value') ?: config('services.google.client_id'),
                        'client_secret' => \App\Modules\Core\Models\Setting::where('name', 'google_client_secret')->value('value') ?: config('services.google.client_secret'),
                        'redirect' => rtrim(config('app.url'), '/') . '/auth/google/callback',
                    ],
                    'services.github' => [
                        'client_id' => \App\Modules\Core\Models\Setting::where('name', 'github_client_id')->value('value') ?: config('services.github.client_id'),
                        'client_secret' => \App\Modules\Core\Models\Setting::where('name', 'github_client_secret')->value('value') ?: config('services.github.client_secret'),
                        'redirect' => rtrim(config('app.url'), '/') . '/auth/github/callback',
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore during setup/migrations or if class is missing
        }

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogSuccessfulLogin::class
        );

        \Illuminate\Support\Facades\Mail::extend('brevo', function () {
            $apiKey = config('services.brevo.key') ?: env('BREVO_KEY');
            if (empty($apiKey)) {
                $apiKey = 'dummy-key-to-prevent-error';
            }
            return new \Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport($apiKey);
        });
    }
}
