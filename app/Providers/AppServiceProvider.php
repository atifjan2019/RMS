<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Cashier defaults to App\Models\User. This app bills Tenants.
        Cashier::useCustomerModel(\App\Models\Tenant::class);
    }
}
