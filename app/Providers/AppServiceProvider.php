<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PhilSMSService;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(PhilSMSService::class, function ($app) {
            return new PhilSMSService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('prefect.*', function ($view) {
            $notificationCount = 0;
            $newViolationsCount = 0;
            $newStudentsCount = 0;
            $newParentsCount = 0;
            $newComplaintsCount = 0;

            $view->with([
                'notificationCount' => $notificationCount,
                'newViolationsCount' => $newViolationsCount,
                'newStudentsCount' => $newStudentsCount,
                'newParentsCount' => $newParentsCount,
                'newComplaintsCount' => $newComplaintsCount,
            ]);
        });
    }
}
