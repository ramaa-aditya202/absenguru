<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Socialite\Providers\SsoProvider;

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
        Socialite::extend('sso', function ($app) {
            $config = $app['config']['services.sso'];
            return Socialite::buildProvider(SsoProvider::class, $config);
        });
    }
}
