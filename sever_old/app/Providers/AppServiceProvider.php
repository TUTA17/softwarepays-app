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
        Schema::defaultStringLength(191);
        
        // Dynamically load mail settings from the database
        try {
            if (Schema::hasTable('settings')) {
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
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore during setup/migrations
        }
    }
}
