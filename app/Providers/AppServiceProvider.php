<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Vercel (and most PaaS) terminate TLS at the proxy, so Laravel must
        // generate HTTPS URLs in production. This prevents mixed-content
        // blocking of compiled CSS/JS assets.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
