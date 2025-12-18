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
     * The path to your application's "home" route.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Tambahkan macro untuk group per role
        Route::macro('roleGroup', function (string $role, array $options = [], \Closure $routes) {
            $middleware = array_merge(['auth', "checkRole:{$role}"], $options['middleware'] ?? []);
            $prefix = $options['prefix'] ?? $role;
            $as = $options['as'] ?? "{$role}.";
            Route::middleware($middleware)->prefix($prefix)->as($as)->group($routes);
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Semua route web pakai web middleware
            Route::middleware('web')->group(function () {
                require base_path('routes/web.php');
                require base_path('routes/admin.php');
                require base_path('routes/dekan.php');
                require base_path('routes/kaprodi.php');
                require base_path('routes/lecturer.php');
                require base_path('routes/student.php');
                require base_path('routes/staff.php');
                require base_path('routes/edom.php');
                require base_path('routes/remedial.php');
                require base_path('routes/monitoring.php');
                require base_path('routes/letter.php');
                require base_path('routes/skripsi.php');
            });
        });
    }
}
