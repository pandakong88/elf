<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\User;

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
        // Paksa HTTPS jika di production atau jika APP_URL menggunakan https
        if ($this->app->environment('production') || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        // Register layout view component
        \Illuminate\Support\Facades\Blade::component('layouts.app', 'app-layout');

        // Bypass all checks for super-admin
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
