<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('ai-resume', function (Request $request): array {
            $key = (string) ($request->user()?->getKey() ?: $request->ip());
            $response = function (Request $request, array $headers) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Too many résumé analyses.'], 429, $headers);
                }

                return redirect()->route('candidate.career.index')->with('rate_limit', true);
            };

            return [
                Limit::perDay(max(1, (int) config('ai.daily_limit', 3)))
                    ->by('ai-resume:day:'.$key)
                    ->response($response),
                Limit::perMinutes(60 * 24 * 30, max(1, (int) config('ai.monthly_limit', 15)))
                    ->by('ai-resume:month:'.$key)
                    ->response($response),
            ];
        });
    }
}
