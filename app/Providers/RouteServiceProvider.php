<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = "/";

    public function boot(): void
    {
        RateLimiter::for("api", function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for("public-submit", function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for("public-query", function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for("admin", function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware("web")
                ->group(base_path("routes/publico.php"));

            Route::middleware(["web", "auth", "admin"])
                ->prefix("admin")
                ->name("admin.")
                ->group(base_path("routes/admin.php"));

            Route::middleware(["web", "auth", "super.admin"])
                ->prefix("super-admin")
                ->name("super-admin.")
                ->group(base_path("routes/super-admin.php"));
        });
    }
}
