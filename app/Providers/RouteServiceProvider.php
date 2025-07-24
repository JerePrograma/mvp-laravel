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
     * Este valor determina la ruta “home” tras el login.
     */
    public const HOME = '/home';

    /**
     * Define las rutas del aplicativo.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            // ↓ Aquí debe estar esta sección para cargar todas las rutas de `routes/api.php`
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // ↓ Y esta para `routes/web.php`
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configura el rate limiting para las rutas API.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
