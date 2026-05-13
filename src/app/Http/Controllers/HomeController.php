<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $usuario = auth()->user();

        $categorias = Schema::hasTable('categorias')
            ? Categoria::query()
                ->withCount('productos')
                ->orderByDesc('productos_count')
                ->orderBy('nombre')
                ->take(8)
                ->get()
            : collect();

        $emprendedores = Schema::hasTable('emprendedores')
            ? PerfilEmprendedor::query()
                ->with(['categoria:id,nombre', 'usuario:id,nombre_completo'])
                ->withCount('productos')
                ->where('estado', 'activo')
                ->latest()
                ->take(8)
                ->get()
            : collect();

        $productosPopulares = collect();

        if (Schema::hasTable('productos')) {
            $with = [
                'categoria:id,nombre',
                'perfilEmprendedor.usuario:id,nombre_completo',
            ];

            if (Schema::hasTable('producto_fotos')) {
                $with['imagenes'] = fn ($query) => $query->orderBy('orden');
            }

            $productosPopulares = Producto::query()
                ->with($with)
                ->where('activo', true)
                ->orderByDesc('created_at')
                ->take(8)
                ->get();
        }

        $periodoRanking = 'Acumulado';

        $topDonadores = collect();

        if (Schema::hasTable('puntos_donador')) {
            $topDonadores = DB::table('puntos_donador')
                ->join('usuarios', 'usuarios.id', '=', 'puntos_donador.usuario_id')
                ->orderByDesc('puntos_donador.puntos_total')
                ->select([
                    'usuarios.id',
                    'usuarios.nombre_completo as name',
                    'usuarios.email',
                    'puntos_donador.puntos_total as total_puntos',
                    DB::raw('row_number() over (order by puntos_donador.puntos_total desc, usuarios.id asc) as posicion'),
                ])
                ->take(5)
                ->get()
                ->map(fn ($donador) => tap($donador, fn ($item) => $item->anonimo = false));
        }

        $impactoDonaciones = Schema::hasTable('donaciones')
            ? [
                'total' => (float) DB::table('donaciones')->sum('monto'),
                'cantidad' => (int) DB::table('donaciones')->count(),
            ]
            : ['total' => 0.0, 'cantidad' => 0];

        $carritoCantidad = 0;
        $notificacionesCantidad = 0;

        if ($usuario) {
            if (Schema::hasTable('items_carrito') && Schema::hasTable('carritos')) {
                $carritoCantidad = (int) DB::table('items_carrito')
                    ->join('carritos', 'carritos.id', '=', 'items_carrito.carrito_id')
                    ->where('carritos.usuario_id', $usuario->id)
                    ->where('carritos.estado', 'activo')
                    ->sum('items_carrito.cantidad');
            }

            if (Schema::hasTable('registros_notificaciones')) {
                $notificacionesCantidad = (int) DB::table('registros_notificaciones')
                    ->where('destinatario_usuario_id', $usuario->id)
                    ->where('estado_envio', 'pendiente')
                    ->count();
            }
        }

        return view('welcome', [
            'categorias' => $categorias,
            'emprendedores' => $emprendedores,
            'productosPopulares' => $productosPopulares,
            'topDonadores' => $topDonadores,
            'periodoRanking' => $periodoRanking,
            'impactoDonaciones' => $impactoDonaciones,
            'carritoCantidad' => $carritoCantidad,
            'notificacionesCantidad' => $notificacionesCantidad,
        ]);
    }
}
