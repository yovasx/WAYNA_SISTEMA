<?php

namespace App\Livewire\Admin;

use App\Models\PuntosDonador;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PuntosDonadoresIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroNivel = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroNivel(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $puntosDonadores = PuntosDonador::query()
            ->with('usuario:id,nombre_completo,email')
            ->when($this->busqueda !== '', function ($query) {
                $query->whereHas('usuario', function ($userQuery) {
                    $userQuery
                        ->where('nombre_completo', 'like', '%'.$this->busqueda.'%')
                        ->orWhere('email', 'like', '%'.$this->busqueda.'%');
                });
            })
            ->when($this->filtroNivel !== '', fn ($query) => $query->where('nivel', $this->filtroNivel))
            ->orderByDesc('puntos_total')
            ->orderBy('usuario_id')
            ->paginate(15);

        return view('livewire.admin.puntos-donadores-index', [
            'puntosDonadores' => $puntosDonadores,
            'resumen' => [
                'totales' => PuntosDonador::query()->count(),
                'puntos' => PuntosDonador::query()->sum('puntos_total'),
                'oro' => PuntosDonador::query()->where('nivel', 'oro')->count(),
                'maximo' => PuntosDonador::query()->max('puntos_total') ?? 0,
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Puntos donador',
        ]);
    }
}
