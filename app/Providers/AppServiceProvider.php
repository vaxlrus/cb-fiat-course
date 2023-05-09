<?php

namespace App\Providers;

use App\Domain\ExchangeRateSources\CBRSource;
use App\Domain\Interfaces\CurrencyRateSourceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CurrencyRateSourceInterface::class, CBRSource::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
