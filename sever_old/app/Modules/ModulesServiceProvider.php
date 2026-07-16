<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ModulesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $modulesPath = app_path('Modules');

        if (!File::isDirectory($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $moduleName = basename($modulePath);
            $moduleLower = strtolower($moduleName);

            // Register web routes
            $routeFile = $modulePath . "/routes/web.php";
            if (File::exists($routeFile)) {
                Route::middleware('web')->group($routeFile);
            }

            // Register admin routes
            $adminRouteFile = $modulePath . "/routes/admin.php";
            if (File::exists($adminRouteFile)) {
                Route::middleware(['web'])->group($adminRouteFile);
            }

            // Register views
            if (File::isDirectory($modulePath . '/views')) {
                $this->loadViewsFrom($modulePath . '/views', $moduleLower);
            }
        }
    }

    public function register()
    {
        //
    }
}
