<?php

namespace App\Livewire\Emprendedor;

use App\Models\Pedido;
use App\Models\PerfilEmprendedor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;

class PedidosIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public ?int $pedidoExpandidoId = null;

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function alternarDetalle(int $pedidoId): void
    {
        $this->pedidoDelPerfil($pedidoId);
        $this->pedidoExpandidoId = $this->pedidoExpandidoId === $pedidoId ? null : $pedidoId;
    }

    public function confirmarPedido(int $pedidoId): void
    {
        $this->cambiarEstado($pedidoId, 'confirmado', 'Pedido confirmado correctamente.');
    }

    public function marcarEntregado(int $pedidoId): void
    {
        $this->cambiarEstado($pedidoId, 'entregado', 'Pedido marcado como entregado.');
    }

    public function completarPedido(int $pedidoId): void
    {
        $this->cambiarEstado($pedidoId, 'completado', 'Pedido completado correctamente.');
    }

    public function cancelarPedido(int $pedidoId): void
    {
        $this->cambiarEstado($pedidoId, 'cancelado', 'Pedido cancelado correctamente.');
    }

    public function render(): View
    {
        $perfil = $this->asegurarPerfilEmprendedor();
        $pedidosQuery = Pedido::query()->where('emprendedor_id', $perfil->id);

        $pedidos = (clone $pedidosQuery)
            ->with([
                'usuario:id,nombre_completo,email',
                'items.producto:id,nombre',
            ])
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('codigo', 'like', '%'.$this->busqueda.'%')
                        ->orWhereHas('usuario', function ($userQuery) {
                            $userQuery
                                ->where('nombre_completo', 'like', '%'.$this->busqueda.'%')
                                ->orWhere('email', 'like', '%'.$this->busqueda.'%');
                        });
                });
            })
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado', $this->filtroEstado))
            ->latest()
            ->paginate(10);

        $salesStates = config('reporting.sales_states', ['confirmado', 'entregado', 'completado']);

        return view('livewire.emprendedor.pedidos-index', [
            'pedidos' => $pedidos,
            'perfil' => $perfil,
            'resumen' => [
                'totales' => (clone $pedidosQuery)->count(),
                'pendientes' => (clone $pedidosQuery)->where('estado', 'pendiente')->count(),
                'activos' => (clone $pedidosQuery)->whereIn('estado', ['confirmado', 'entregado'])->count(),
                'ventas' => (float) (clone $pedidosQuery)->whereIn('estado', $salesStates)->sum('total'),
            ],
        ])->layout('layouts.emprendedor', [
            'pageTitle' => 'Pedidos',
        ]);
    }

    private function cambiarEstado(int $pedidoId, string $estadoDestino, string $mensajeExito): void
    {
        $pedido = $this->pedidoDelPerfil($pedidoId);

        $transiciones = [
            'pendiente' => ['confirmado', 'cancelado'],
            'confirmado' => ['entregado', 'cancelado'],
            'entregado' => ['completado'],
            'completado' => [],
            'cancelado' => [],
        ];

        $permitidos = Arr::get($transiciones, $pedido->estado, []);

        if (! in_array($estadoDestino, $permitidos, true)) {
            session()->flash('pedidos_error', 'La accion no es valida para el estado actual del pedido.');

            return;
        }

        $pedido->update(['estado' => $estadoDestino]);
        session()->flash('pedidos_estado', $mensajeExito);
    }

    private function asegurarPerfilEmprendedor(): PerfilEmprendedor
    {
        $usuario = auth()->user();

        return PerfilEmprendedor::firstOrCreate([
            'usuario_id' => $usuario->id,
        ], [
            'nombre_negocio' => $usuario->name,
            'estado' => 'pendiente',
        ]);
    }

    private function pedidoDelPerfil(int $pedidoId): Pedido
    {
        $perfil = $this->asegurarPerfilEmprendedor();

        return Pedido::query()
            ->where('emprendedor_id', $perfil->id)
            ->findOrFail($pedidoId);
    }
}
