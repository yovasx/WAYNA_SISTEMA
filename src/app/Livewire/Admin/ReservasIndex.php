<?php

namespace App\Livewire\Admin;

use App\Models\Reserva;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ReservasIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $reservas = Reserva::query()
            ->with(['usuario:id,nombre_completo', 'emprendedor:id,nombre_negocio', 'producto:id,nombre'])
            ->when($this->busqueda !== '', function ($query) {
                $query->where('codigo', 'like', '%'.$this->busqueda.'%');
            })
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado', $this->filtroEstado))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.reservas-index', [
            'reservas' => $reservas,
            'resumen' => [
                'totales' => Reserva::query()->count(),
                'pendientes' => Reserva::query()->where('estado', 'pendiente')->count(),
                'confirmadas' => Reserva::query()->where('estado', 'confirmada')->count(),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Reservas',
        ]);
    }
}
