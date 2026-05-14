<?php

namespace App\Livewire\Admin;

use App\Models\Donacion;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class DonacionesIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $donaciones = Donacion::query()
            ->with(['usuario:id,nombre_completo', 'emprendedor:id,nombre_negocio'])
            ->when($this->busqueda !== '', function ($query) {
                $query->whereHas('usuario', fn ($q) => $q->where('nombre_completo', 'like', '%'.$this->busqueda.'%'));
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.donaciones-index', [
            'donaciones' => $donaciones,
            'totalMonto' => Donacion::query()->sum('monto'),
            'totalDonaciones' => Donacion::query()->count(),
        ])->layout('layouts.admin', [
            'pageTitle' => 'Donaciones',
        ]);
    }
}
