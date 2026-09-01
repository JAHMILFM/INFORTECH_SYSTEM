<?php

namespace App\Providers;

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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Bloqueo de Fuerza Bruta en Login (3 intentos por minuto)
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
        });

        // Anti-Scraping / Extracción Masiva (Max 5 revelados por minuto por usuario)
        \Illuminate\Support\Facades\RateLimiter::for('reveals', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        \App\Models\Company::observe(\App\Observers\CompanyObserver::class);
        \App\Models\ServiceRecord::observe(\App\Observers\ServiceRecordObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }
}
