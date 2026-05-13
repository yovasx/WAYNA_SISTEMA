<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $usuario = auth()->user();

        $categorias = Categoria::query()
            ->where('activa', true)
            ->withCount('productos')
            ->orderByDesc('productos_count')
            ->orderBy('nombre')
            ->take(8)
            ->get();

        $emprendedores = PerfilEmprendedor::query()
            ->with(['categoria:id,nombre', 'usuario:id,name'])
            ->withCount('productos')
            ->where('estado_aprobacion', 'aprobado')
            ->latest()
            ->take(8)
            ->get();

        $productosPopulares = Producto::query()
            ->with([
                'categoria:id,nombre',
                'perfilEmprendedor.usuario:id,name',
                'imagenes' => fn ($query) => $query->orderByDesc('es_principal')->orderBy('orden'),
            ])
            ->orderByDesc('destacado')
            ->orderByDesc(DB::raw('COALESCE(publicado_at, created_at)'))
            ->take(8)
            ->get();

        $periodoRanking = DB::table('rankings_donadores')
            ->orderByDesc('periodo')
            ->value('periodo');

        $topDonadores = collect();

        if ($periodoRanking) {
            $topDonadores = DB::table('rankings_donadores')
                ->join('users', 'users.id', '=', 'rankings_donadores.donador_id')
                ->where('rankings_donadores.periodo', $periodoRanking)
                ->orderBy('rankings_donadores.posicion')
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'rankings_donadores.total_puntos',
                    'rankings_donadores.posicion',
                    'rankings_donadores.anonimo',
                ])
                ->take(5)
                ->get();
        }

        $impactoDonaciones = [
            'total' => (float) DB::table('donaciones')->sum('monto'),
            'cantidad' => (int) DB::table('donaciones')->count(),
        ];

        $carritoCantidad = 0;
        $notificacionesCantidad = 0;

        if ($usuario) {
            $carritoCantidad = (int) DB::table('items_carrito')
                ->join('carritos', 'carritos.id', '=', 'items_carrito.carrito_id')
                ->where('carritos.usuario_id', $usuario->id)
                ->where('carritos.estado', 'activo')
                ->sum('items_carrito.cantidad');

            $notificacionesCantidad = (int) DB::table('registros_notificaciones')
                ->where('destinatario_usuario_id', $usuario->id)
                ->where('estado_envio', 'pendiente')
                ->count();
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
