<?php

namespace App\Providers;

use App\Models\Donacion;
use App\Models\Pedido;
use App\Models\Reserva;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::enforceMorphMap([
            'pedido' => Pedido::class,
            'donacion' => Donacion::class,
            'reserva' => Reserva::class,
        ]);
    }
}
