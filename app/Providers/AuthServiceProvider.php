<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ✅ Define gates for permissions
        Gate::define('access-asset-menu', function ($user) {
            return true;
        });

        Gate::define('access-attendance-menu', function ($user) {
            return true;
        });

        Gate::define('access-asset-booking-menu', function ($user) {
            return true;
        });
    }
}