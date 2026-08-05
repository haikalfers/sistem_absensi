<?php

namespace App\Providers;

use App\Services\{GeolocationService, AttendanceService, PayrollService};
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class AppServicesProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Bind services ke container
        $this->app->singleton(GeolocationService::class, function ($app) {
            return new GeolocationService();
        });

        $this->app->singleton(AttendanceService::class, function ($app) {
            return new AttendanceService($app->make(GeolocationService::class));
        });

        $this->app->singleton(PayrollService::class, function ($app) {
            return new PayrollService();
        });
    }

    public function boot(): void
    {
        //
    }
}
