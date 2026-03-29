<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(40)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('security-payment', function (Request $request) {
            return Limit::perMinute(8)->by('payment:' . ($request->user()?->id ?: $request->ip()));
        });

        RateLimiter::for('security-server-create', function (Request $request) {
            return Limit::perMinute(6)->by('server-create:' . ($request->user()?->id ?: $request->ip()));
        });

        RateLimiter::for('security-mass-notify', function (Request $request) {
            return Limit::perMinute(3)->by('mass-notify:' . ($request->user()?->id ?: $request->ip()));
        });

        RateLimiter::for('security-api-mass-notify', function (Request $request) {
            $token = $request->attributes->get('application_api_token');
            $tokenId = is_object($token) && isset($token->token)
                ? substr(hash('sha256', (string) $token->token), 0, 16)
                : $request->ip();

            return Limit::perMinute(10)->by('api-notify:' . $tokenId);
        });
    }
}
