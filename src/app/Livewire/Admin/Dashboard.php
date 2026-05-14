<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $metricas = [
            'usuarios_activos' => User::query()->where('estado', 'activo')->count(),
            'emprendedores_pendientes' => PerfilEmprendedor::query()->where('estado', 'pendiente')->count(),
            'emprendedores_aprobados' => PerfilEmprendedor::query()->where('estado', 'activo')->count(),
            'categorias_activas' => Categoria::query()->count(),
            'productos_totales' => Producto::query()->where('activo', true)->count(),
            'stock_critico' => Producto::query()
                ->where(function ($query) {
                    $query
                        ->where('stock', '<=', 5)
                        ->orWhereIn('estado_stock', ['ultimas_unidades', 'agotado']);
                })
                ->count(),
        ];

        $estadoEmprendedores = PerfilEmprendedor::query()
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $topCategorias = Categoria::query()
            ->withCount('productos')
            ->orderByDesc('productos_count')
            ->orderBy('nombre')
            ->take(5)
            ->get();

        $ultimosProductos = Producto::query()
            ->with(['categoria:id,nombre', 'perfilEmprendedor:id,nombre_negocio'])
            ->latest()
            ->take(6)
            ->get();

        $ultimosEmprendedores = PerfilEmprendedor::query()
            ->with(['usuario:id,nombre_completo,email,created_at', 'categoria:id,nombre'])
            ->latest()
            ->take(6)
            ->get();

        $ventasMensuales = [];
        $donacionesMensuales = [];

        if (Schema::hasTable('pedidos')) {
            $ventasMensuales = DB::table('pedidos')
                ->select(DB::raw('COALESCE(SUM(total), 0) as total'), DB::raw("to_char(created_at, 'YYYY-MM') as mes"))
                ->whereIn('estado', ['confirmado', 'entregado', 'completado'])
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy(DB::raw("to_char(created_at, 'YYYY-MM')"))
                ->orderBy('mes')
                ->pluck('total', 'mes')
                ->toArray();
        }

        if (Schema::hasTable('donaciones')) {
            $donacionesMensuales = DB::table('donaciones')
                ->select(DB::raw('COALESCE(SUM(monto), 0) as total'), DB::raw("to_char(created_at, 'YYYY-MM') as mes"))
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy(DB::raw("to_char(created_at, 'YYYY-MM')"))
                ->orderBy('mes')
                ->pluck('total', 'mes')
                ->toArray();
        }

        $meses = collect();
        for ($i = 5; $i >= 0; $i--) {
            $meses->push(now()->subMonths($i)->format('Y-m'));
        }

        $chartData = $meses->map(function ($mes) use ($ventasMensuales, $donacionesMensuales) {
            return [
                'mes' => \Carbon\Carbon::createFromFormat('Y-m', $mes)->format('M'),
                'ventas' => (float) ($ventasMensuales[$mes] ?? 0),
                'donaciones' => (float) ($donacionesMensuales[$mes] ?? 0),
            ];
        });

        $maxValor = max(
            $chartData->max('ventas') ?? 1,
            $chartData->max('donaciones') ?? 1,
            1
        );

        $totalEmprendedores = max(PerfilEmprendedor::query()->count(), 1);
        $totalCategorias = max(Categoria::query()->count(), 1);
        $totalProductos = max(Producto::query()->count(), 1);

        $porcentajeAprobados = round((PerfilEmprendedor::query()->where('estado', 'activo')->count() / $totalEmprendedores) * 100);
        $porcentajeProductosActivos = round((Producto::query()->where('activo', true)->count() / $totalProductos) * 100);
        $porcentajeCategoriasConProductos = round((Categoria::query()->has('productos')->count() / $totalCategorias) * 100);

        return view('livewire.admin.dashboard', [
            'metricas' => $metricas,
            'estadoEmprendedores' => $estadoEmprendedores,
            'topCategorias' => $topCategorias,
            'ultimosProductos' => $ultimosProductos,
            'ultimosEmprendedores' => $ultimosEmprendedores,
            'chartData' => $chartData,
            'maxValor' => $maxValor,
            'porcentajeAprobados' => $porcentajeAprobados,
            'porcentajeProductosActivos' => $porcentajeProductosActivos,
            'porcentajeCategoriasConProductos' => $porcentajeCategoriasConProductos,
        ])->layout('layouts.admin', [
            'pageTitle' => 'Dashboard administrador',
        ]);
    }
}
