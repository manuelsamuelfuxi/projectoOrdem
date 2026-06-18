<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Como estamos a usar Virtual Host no XAMPP (ordepdita.test),
        // forçamos o Laravel a gerar as URLs usando esse domínio.
        // Isso garante que links de email, assets e metadados estejam correctos.
        if (app()->environment('local')) {
            URL::forceRootUrl('http://ordepdita.test');
        }
    }
}