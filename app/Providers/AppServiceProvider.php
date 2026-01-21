<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\{InsuranceScannerInterface,RawTextAIScannerInterface};
use App\Services\{OcrSpaceScanner,GeminiScannerService,GroqService};
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InsuranceScannerInterface::class, OcrSpaceScanner::class);
        $this->app->bind(RawTextAIScannerInterface::class, GroqService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
