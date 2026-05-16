<?php

namespace App\Livewire\Emprendedor;

use App\Models\Pedido;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $perfil = $this->asegurarPerfilEmprendedor();
        $salesStates = config('reporting.sales_states', ['confirmado', 'entregado', 'completado']);
        $productosQuery = Producto::query()->where('emprendedor_id', $perfil->id);
        $pedidosQuery = Pedido::query()->where('emprendedor_id', $perfil->id);

        $productos = (clone $productosQuery)
            ->with('categoria:id,nombre')
            ->latest()
            ->take(5)
            ->get();

        $pedidosRecientes = (clone $pedidosQuery)
            ->with(['usuario:id,nombre_completo,email'])
            ->latest()
            ->take(5)
            ->get();

        $productosStockBajo = (clone $productosQuery)
            ->with('categoria:id,nombre')
            ->where(function ($query) {
                $query->where('stock', '<=', 5)->orWhere('estado_stock', 'ultimas_unidades');
            })
            ->orderBy('stock')
            ->take(5)
            ->get();

        $checklist = collect([
            ['label' => 'Nombre del negocio definido', 'done' => filled($perfil->nombre_negocio)],
            ['label' => 'Categoria principal asignada', 'done' => $perfil->categoria_id !== null],
            ['label' => 'Descripcion del negocio cargada', 'done' => filled($perfil->descripcion)],
            ['label' => 'Historia del negocio cargada', 'done' => filled($perfil->historia)],
            ['label' => 'Portada o logo configurados', 'done' => filled($perfil->foto_portada) || filled($perfil->logo_url)],
            ['label' => 'Ubicacion comercial definida', 'done' => filled($perfil->ciudad) || ($perfil->latitud !== null && $perfil->longitud !== null)],
            ['label' => 'Primer producto creado', 'done' => (clone $productosQuery)->exists()],
            ['label' => 'Primer pedido recibido', 'done' => (clone $pedidosQuery)->exists()],
        ]);

        $tareasCompletadas = $checklist->where('done', true)->count();
        $porcentajePerfil = (int) round(($tareasCompletadas / max($checklist->count(), 1)) * 100);

        return view('livewire.emprendedor.dashboard', [
            'productos' => $productos,
            'perfil' => $perfil,
            'pedidosRecientes' => $pedidosRecientes,
            'productosStockBajo' => $productosStockBajo,
            'checklist' => $checklist,
            'metricas' => [
                'productos' => (clone $productosQuery)->count(),
                'stock_bajo' => (clone $productosQuery)->where(function ($query) {
                    $query->where('stock', '<=', 5)->orWhere('estado_stock', 'ultimas_unidades');
                })->count(),
                'inventario_total' => (clone $productosQuery)->sum('stock'),
                'pedidos_pendientes' => (clone $pedidosQuery)->where('estado', 'pendiente')->count(),
                'ventas_mes' => (float) (clone $pedidosQuery)
                    ->whereIn('estado', $salesStates)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('total'),
                'pedidos_atendidos' => (clone $pedidosQuery)->whereIn('estado', $salesStates)->count(),
                'cancelados' => (clone $pedidosQuery)->where('estado', 'cancelado')->count(),
                'porcentaje_perfil' => $porcentajePerfil,
                'tareas_completadas' => $tareasCompletadas,
                'tareas_total' => $checklist->count(),
            ],
        ])->layout('layouts.emprendedor', [
            'pageTitle' => 'Dashboard',
        ]);
    }

    private function asegurarPerfilEmprendedor(): PerfilEmprendedor
    {
        $usuario = auth()->user();

        return $usuario->perfilEmprendedor ?? PerfilEmprendedor::create([
            'usuario_id' => $usuario->id,
            'nombre_negocio' => $usuario->name,
            'estado' => 'pendiente',
        ]);
    }
}
