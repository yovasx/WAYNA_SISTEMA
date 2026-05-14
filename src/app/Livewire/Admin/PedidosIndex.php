<?php

namespace App\Livewire\Admin;

use App\Models\Pedido;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PedidosIndex extends Component
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
        $pedidos = Pedido::query()
            ->with(['usuario:id,nombre_completo', 'emprendedor:id,nombre_negocio'])
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('codigo', 'like', '%'.$this->busqueda.'%')
                        ->orWhereHas('usuario', fn ($q) => $q->where('nombre_completo', 'like', '%'.$this->busqueda.'%'));
                });
            })
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado', $this->filtroEstado))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.pedidos-index', [
            'pedidos' => $pedidos,
            'resumen' => [
                'totales' => Pedido::query()->count(),
                'pendientes' => Pedido::query()->where('estado', 'pendiente')->count(),
                'confirmados' => Pedido::query()->whereIn('estado', ['confirmado', 'entregado'])->count(),
                'ingresos' => Pedido::query()->sum('total'),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Pedidos',
        ]);
    }
}
