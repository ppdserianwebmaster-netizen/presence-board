<?php

namespace App\Providers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; //

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
        /**
         * Define the security gate for Admin access.
         * Using the Enum directly prevents "magic string" errors.
         */
        Gate::define('admin-access', static function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        //
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        //
    }
}
