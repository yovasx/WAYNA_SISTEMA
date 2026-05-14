<?php

namespace App\Livewire\Admin;

use App\Models\HistorialPuntos;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialPuntosIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $busqueda = trim($this->busqueda);

        $historial = HistorialPuntos::query()
            ->with([
                'usuario:id,nombre_completo,email',
                'donacion:id,monto,emprendedor_id',
                'donacion.emprendedor:id,nombre_negocio',
            ])
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->whereHas('usuario', function ($userQuery) use ($busqueda) {
                        $userQuery
                            ->where('nombre_completo', 'like', '%'.$busqueda.'%')
                            ->orWhere('email', 'like', '%'.$busqueda.'%');
                    });

                    if (ctype_digit($busqueda)) {
                        $subquery
                            ->orWhere('donacion_id', (int) $busqueda)
                            ->orWhere('id', (int) $busqueda);
                    }
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.historial-puntos-index', [
            'historial' => $historial,
            'resumen' => [
                'registros' => HistorialPuntos::query()->count(),
                'puntos_otorgados' => HistorialPuntos::query()->sum('puntos_ganados'),
                'multiplicados' => HistorialPuntos::query()->where('multiplicador', '>', 1)->count(),
                'donaciones_vinculadas' => HistorialPuntos::query()->distinct()->count('donacion_id'),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Historial de puntos',
        ]);
    }
}
