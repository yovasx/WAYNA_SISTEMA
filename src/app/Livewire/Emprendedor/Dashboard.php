<?php

namespace App\Livewire\Emprendedor;

use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $usuario = auth()->user();
        $perfil = $usuario->perfilEmprendedor ?? PerfilEmprendedor::create([
            'usuario_id' => $usuario->id,
            'nombre_negocio' => $usuario->name,
            'estado' => 'pendiente',
        ]);

        $productos = Producto::query()
            ->when($perfil, fn ($query) => $query->where('emprendedor_id', $perfil->id), fn ($query) => $query->whereRaw('1 = 0'))
            ->with('categoria:id,nombre')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.emprendedor.dashboard', [
            'usuario' => $usuario,
            'productos' => $productos,
            'perfil' => $perfil,
            'metricas' => [
                'productos' => $perfil ? Producto::where('emprendedor_id', $perfil->id)->count() : 0,
                'stock_bajo' => $perfil ? Producto::where('emprendedor_id', $perfil->id)->where(function ($query) {
                    $query->where('stock', '<=', 5)->orWhere('estado_stock', 'ultimas_unidades');
                })->count() : 0,
                'inventario_total' => $perfil ? Producto::where('emprendedor_id', $perfil->id)->sum('stock') : 0,
                'valor_catalogo' => $perfil ? Producto::where('emprendedor_id', $perfil->id)->sum('precio') : 0,
            ],
        ])->layout('layouts.emprendedor');
    }
}
