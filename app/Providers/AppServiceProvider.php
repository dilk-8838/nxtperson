<?php

namespace App\Providers;

use App\Events\ProduceInventoryPublished;
use App\Listeners\NotifyEligibleBuyers;
use App\Services\Advisory\CropAdvisoryEngineInterface;
use App\Services\Advisory\SampleAiCropDiagnosticEngine;
use App\Services\Grading\ProduceGradingServiceInterface;
use App\Services\Grading\SampleProduceGradingProvider;
use App\Services\Mandi\GujaratApmcSeededProvider;
use App\Services\Mandi\MandiPriceServiceInterface;
use App\Services\Pricing\ProducePricingServiceInterface;
use App\Services\Pricing\SampleProducePricingProvider;
use App\Services\Weather\GujaratSampleWeatherProvider;
use App\Services\Weather\WeatherServiceInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Modular agricultural service bindings
        $this->app->singleton(WeatherServiceInterface::class, GujaratSampleWeatherProvider::class);
        $this->app->singleton(CropAdvisoryEngineInterface::class, SampleAiCropDiagnosticEngine::class);
        $this->app->singleton(MandiPriceServiceInterface::class, GujaratApmcSeededProvider::class);
        $this->app->singleton(ProduceGradingServiceInterface::class, SampleProduceGradingProvider::class);
        $this->app->singleton(ProducePricingServiceInterface::class, SampleProducePricingProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            ProduceInventoryPublished::class,
            NotifyEligibleBuyers::class,
        );
    }
}
