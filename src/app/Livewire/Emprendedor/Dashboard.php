<?php

namespace App\Livewire\Emprendedor;

use App\Models\Pedido;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $perfil = $this->asegurarPerfilEmprendedor();
        $salesStates = config('reporting.sales_states', ['confirmado', 'entregado', 'completado']);
        $productosQuery = Producto::query()->where('emprendedor_id', $perfil->id);
        $pedidosQuery = Pedido::query()->where('emprendedor_id', $perfil->id);
        $inicioMes = now()->startOfMonth();

        $ventasHistoricas = (clone $pedidosQuery)
            ->whereIn('estado', $salesStates)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['created_at', 'total']);

        $meses = collect();
        for ($i = 5; $i >= 0; $i--) {
            $meses->push(now()->subMonths($i)->format('Y-m'));
        }

        $ventasPorMes = $ventasHistoricas
            ->groupBy(fn (Pedido $pedido) => optional($pedido->created_at)->format('Y-m'))
            ->map(fn ($pedidos) => (float) $pedidos->sum('total'));

        $ventasChartData = $meses->map(function (string $mes) use ($ventasPorMes) {
            return [
                'key' => $mes,
                'label' => Carbon::createFromFormat('Y-m', $mes)->translatedFormat('M'),
                'ventas' => (float) ($ventasPorMes[$mes] ?? 0),
            ];
        });

        $pedidosPorEstado = collect(['pendiente', 'confirmado', 'entregado', 'completado', 'cancelado'])
            ->map(fn (string $estado) => [
                'label' => ucfirst($estado),
                'estado' => $estado,
                'total' => (clone $pedidosQuery)->where('estado', $estado)->count(),
            ]);

        $topProductos = DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'productos.id', '=', 'pedido_items.producto_id')
            ->where('pedidos.emprendedor_id', $perfil->id)
            ->whereIn('pedidos.estado', $salesStates)
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc(DB::raw('SUM(pedido_items.subtotal)'))
            ->limit(5)
            ->get([
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(pedido_items.cantidad) as unidades'),
                DB::raw('SUM(pedido_items.subtotal) as ventas'),
            ]);

        $inventarioChartData = collect([
            ['label' => 'Disponibles', 'total' => (clone $productosQuery)->where('estado_stock', 'disponible')->count()],
            ['label' => 'Ultimas unidades', 'total' => (clone $productosQuery)->where('estado_stock', 'ultimas_unidades')->count()],
            ['label' => 'Agotados', 'total' => (clone $productosQuery)->where('estado_stock', 'agotado')->count()],
        ]);

        $pedidosValidadosCount = (clone $pedidosQuery)->whereIn('estado', $salesStates)->count();
        $ventasValidadas = (float) (clone $pedidosQuery)->whereIn('estado', $salesStates)->sum('total');

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

        return view('livewire.emprendedor.dashboard', [
            'productos' => $productos,
            'perfil' => $perfil,
            'pedidosRecientes' => $pedidosRecientes,
            'productosStockBajo' => $productosStockBajo,
            'ventasChartData' => $ventasChartData,
            'pedidosPorEstado' => $pedidosPorEstado,
            'topProductos' => $topProductos,
            'inventarioChartData' => $inventarioChartData,
            'metricas' => [
                'ventas_mes' => (float) (clone $pedidosQuery)
                    ->whereIn('estado', $salesStates)
                    ->where('created_at', '>=', $inicioMes)
                    ->sum('total'),
                'pedidos_pendientes' => (clone $pedidosQuery)->where('estado', 'pendiente')->count(),
                'pedidos_atendidos' => $pedidosValidadosCount,
                'productos' => (clone $productosQuery)->where('activo', true)->count(),
                'ticket_promedio' => $pedidosValidadosCount > 0 ? $ventasValidadas / $pedidosValidadosCount : 0,
                'stock_bajo' => (clone $productosQuery)->where(function ($query) {
                    $query->where('stock', '<=', 5)->orWhere('estado_stock', 'ultimas_unidades');
                })->count(),
                'cancelados' => (clone $pedidosQuery)->where('estado', 'cancelado')->count(),
                'estado_negocio' => $perfil->estado_aprobacion,
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
