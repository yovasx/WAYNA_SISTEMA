<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
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

        return view('livewire.admin.dashboard', [
            'metricas' => $metricas,
            'estadoEmprendedores' => $estadoEmprendedores,
            'topCategorias' => $topCategorias,
            'ultimosProductos' => $ultimosProductos,
            'ultimosEmprendedores' => $ultimosEmprendedores,
        ])->layout('layouts.admin', [
            'pageTitle' => 'Dashboard administrador',
        ]);
    }
}
