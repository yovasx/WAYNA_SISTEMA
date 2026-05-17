<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioManana = Carbon::tomorrow();
        $salesStates = config('reporting.sales_states', ['confirmado', 'entregado', 'completado']);

        $ventasMensuales = [];
        $donacionesMensuales = [];

        if (Schema::hasTable('pedidos')) {
            $ventasMensuales = DB::table('pedidos')
                ->select(DB::raw('COALESCE(SUM(total), 0) as total'), DB::raw("to_char(created_at, 'YYYY-MM') as mes"))
                ->whereIn('estado', $salesStates)
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

        $metricas = [
            'emprendedores_activos' => PerfilEmprendedor::query()->where('estado', 'activo')->count(),
            'ventas_mes' => Schema::hasTable('pedidos')
                ? (float) DB::table('pedidos')
                    ->whereIn('estado', $salesStates)
                    ->where('created_at', '>=', $inicioMes)
                    ->sum('total')
                : 0,
            'donaciones_mes' => Schema::hasTable('donaciones')
                ? (float) DB::table('donaciones')
                    ->where('created_at', '>=', $inicioMes)
                    ->sum('monto')
                : 0,
            'usuarios_registrados' => User::query()->count(),
            'reservas_activas' => Schema::hasTable('reservas')
                ? (int) DB::table('reservas')
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->count()
                : 0,
            'transacciones_hoy' => Schema::hasTable('transacciones')
                ? (int) DB::table('transacciones')
                    ->where('created_at', '>=', today())
                    ->where('created_at', '<', $inicioManana)
                    ->count()
                : 0,
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

        $meses = collect();
        for ($i = 5; $i >= 0; $i--) {
            $meses->push(now()->subMonths($i)->format('Y-m'));
        }

        $chartData = $meses->map(function ($mes) use ($ventasMensuales, $donacionesMensuales) {
            return [
                'key' => $mes,
                'label' => Carbon::createFromFormat('Y-m', $mes)->format('M'),
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
