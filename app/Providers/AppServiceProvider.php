<?php

namespace App\Providers;

use App\Services\QueueService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind QueueService as singleton — one instance for entire request lifecycle
        $this->app->singleton(QueueService::class, fn() => new QueueService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale for Carbon (Indonesian date formatting)
        \Carbon\Carbon::setLocale('id');
    }
}
