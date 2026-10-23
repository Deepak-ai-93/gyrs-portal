<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Services\Sms\LogSmsSender;
use App\Services\Sms\TwilioSmsSender;
use App\Support\PhoneNumber;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsSender::class, function (): SmsSender {
            return match ((string) config('otp.sms_driver', 'twilio')) {
                'twilio' => new TwilioSmsSender,
                'log' => new LogSmsSender,
                default => throw new InvalidArgumentException('Unsupported SMS driver ['.config('otp.sms_driver').'].'),
            };
        });
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

        RateLimiter::for('otp-request', function (Request $request): array {
            $phone = PhoneNumber::national((string) $request->input('phone')) ?? 'invalid';
            $response = function (Request $request, array $headers) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Too many code requests. Please wait before trying again.'], 429, $headers);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Too many code requests. Please wait a few minutes before trying again.');
            };

            return [
                Limit::perHour(max(1, (int) config('otp.request_per_hour', 5)))
                    ->by('otp-request:phone:'.$phone)
                    ->response($response),
                Limit::perHour(15)
                    ->by('otp-request:ip:'.$request->ip())
                    ->response($response),
            ];
        });

        RateLimiter::for('otp-verify', function (Request $request): array {
            $phone = PhoneNumber::national((string) $request->input('phone')) ?? 'invalid';
            $response = function (Request $request, array $headers) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Too many verification attempts. Please wait and try again.'], 429, $headers);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Too many verification attempts. Please wait a few minutes before trying again.');
            };

            return [
                Limit::perMinutes(10, max(1, (int) config('otp.verify_per_ten_minutes', 10)))
                    ->by('otp-verify:'.$phone.':'.$request->ip())
                    ->response($response),
            ];
        });
    }
}
