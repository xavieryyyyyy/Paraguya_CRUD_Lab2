<?php

// Startup extension point registered in bootstrap/providers.php. register() can bind services;
// boot() runs after registration. Both are empty here because the labs use Laravel defaults.


namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No additional code is needed in this generated section for the CRUD lab.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // No additional code is needed in this generated section for the CRUD lab.
    }
}
