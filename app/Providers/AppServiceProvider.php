<?php

namespace App\Providers;

use App\Models\Sorteo;
use Illuminate\Support\Facades\View;
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
        // Datos del menú: se consultan una sola vez por página aunque varias vistas los usen
        View::composer(['layouts.navigation', 'dashboard'], function ($view) {
            $view->with('haySorteoEnVivo', once(fn () => Sorteo::where('estado', 'pendiente')->whereNotNull('numeros_en_vivo')->exists()));
            $view->with('notificacionesSinLeer', once(fn () => auth()->user()?->cliente?->incidenciasNoLeidas() ?? 0));
        });
    }
}
