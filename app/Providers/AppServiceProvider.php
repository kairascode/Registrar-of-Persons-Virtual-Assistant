<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AiService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiService::class, fn ($app) => new AiService());
    }
    

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
