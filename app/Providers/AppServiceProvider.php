<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

use App\Contracts\{InsuranceScannerInterface,RawTextAIScannerInterface};
use App\Services\{GroqService,ScannerManager};
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('production') || env('RAILWAY_ENVIRONMENT')) {
            URL::forceScheme('https');
        }
        $this->app->bind(RawTextAIScannerInterface::class, GroqService::class);
        $this->app->singleton(ScannerManager::class, fn($app) => new ScannerManager($app));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
