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
            return $user->hasPermissionTo('access_asset_menu');
        });

        Gate::define('access-attendance-menu', function ($user) {
            return $user->hasPermissionTo('access_attendance_menu');
        });
    }
}