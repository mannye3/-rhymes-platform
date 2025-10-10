<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserActivityService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserActivityService::class, function ($app) {
            return new UserActivityService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}