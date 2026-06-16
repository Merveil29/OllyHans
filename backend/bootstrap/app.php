<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Configure rate limiters...
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id_user ?: $request->ip());
            });

            RateLimiter::for('auth', function (Request $request) {
                return Limit::perMinute(5)->by($request->ip());
            });

            RateLimiter::for('public', function (Request $request) {
                return Limit::perMinute(100)->by($request->ip());
            });

            RateLimiter::for('client', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id_client ?: $request->ip());
            });

            RateLimiter::for('admin', function (Request $request) {
                return Limit::perMinute(100)->by($request->user()?->id_user ?: $request->ip());
            });

            // IA Assistant : 10 requêtes / minute par IP (version gratuite Groq)
            RateLimiter::for('ai', function (Request $request) {
                return Limit::perMinute(config('groq.rate_limit.requests_per_minute', 10))
                    ->by($request->user()?->id_client ?: $request->ip())
                    ->response(function () {
                        return response()->json([
                            'error' => 'Trop de requêtes. Vous pouvez poser au maximum 10 questions par minute. Veuillez patienter.',
                            'retry_after' => 60,
                        ], 429);
                    });
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Headers de sécurité (XSS, clickjacking, CSP, etc.)
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Pour API avec Bearer tokens uniquement (pas de cookies de session)
        // Pas besoin de statefulApi ni de EnsureFrontendRequestsAreStateful
        
        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
