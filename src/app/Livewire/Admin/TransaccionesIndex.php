<?php

namespace App\Livewire\Admin;

use App\Models\Transaccion;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TransaccionesIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public string $filtroMetodo = '';

    public string $filtroTipo = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroMetodo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $busqueda = trim($this->busqueda);

        $transacciones = Transaccion::query()
            ->with(['usuario:id,nombre_completo,email', 'referencia'])
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery
                        ->where('referencia_tipo', 'like', '%'.$busqueda.'%')
                        ->orWhere('codigo_qr', 'like', '%'.$busqueda.'%')
                        ->orWhereHas('usuario', function ($userQuery) use ($busqueda) {
                            $userQuery
                                ->where('nombre_completo', 'like', '%'.$busqueda.'%')
                                ->orWhere('email', 'like', '%'.$busqueda.'%');
                        });

                    if (ctype_digit($busqueda)) {
                        $subquery
                            ->orWhere('referencia_id', (int) $busqueda)
                            ->orWhere('id', (int) $busqueda);
                    }
                });
            })
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado', $this->filtroEstado))
            ->when($this->filtroMetodo !== '', fn ($query) => $query->where('metodo_pago', $this->filtroMetodo))
            ->when($this->filtroTipo !== '', fn ($query) => $query->where('referencia_tipo', $this->filtroTipo))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.transacciones-index', [
            'transacciones' => $transacciones,
            'resumen' => [
                'totales' => Transaccion::query()->count(),
                'pendientes' => Transaccion::query()->where('estado', 'pendiente')->count(),
                'completadas' => Transaccion::query()->where('estado', 'completada')->count(),
                'fallidas' => Transaccion::query()->where('estado', 'fallida')->count(),
                'monto_total' => Transaccion::query()->sum('monto'),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Transacciones',
        ]);
    }
}
