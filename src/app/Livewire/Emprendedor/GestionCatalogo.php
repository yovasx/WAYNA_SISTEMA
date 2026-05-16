<?php

namespace App\Livewire\Emprendedor;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class GestionCatalogo extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroCategoria = '';

    public string $filtroEstado = '';

    public string $graficoPreset = '30d';

    public string $graficoMetrica = 'unidades';

    public bool $mostrarModalProducto = false;

    public bool $mostrarModalCategoria = false;

    public ?int $productoIdEditando = null;

    public ?int $categoriaIdEditando = null;

    public ?int $productoIdEliminar = null;

    public ?int $categoriaIdEliminar = null;

    public string $productoNombre = '';

    public string $productoDescripcion = '';

    public int|string|null $productoCategoriaId = null;

    public string $productoPrecio = '';

    public int $productoStock = 0;

    public string $productoEstadoDisponibilidad = 'disponible';

    public string $categoriaNombre = '';

    public string $categoriaDescripcion = '';

    public bool $categoriaActiva = true;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function abrirModalProducto(?int $productoId = null): void
    {
        $this->resetFormularioProducto();
        $this->productoIdEditando = $productoId;

        if ($productoId) {
            $producto = $this->productoDelPerfil($productoId);
            $this->productoNombre = $producto->nombre;
            $this->productoDescripcion = $producto->descripcion ?? '';
            $this->productoCategoriaId = $producto->categoria_id;
            $this->productoPrecio = number_format((float) $producto->precio, 2, '.', '');
            $this->productoStock = $producto->stock;
            $this->productoEstadoDisponibilidad = $producto->estado_disponibilidad;
        }

        $this->mostrarModalProducto = true;
    }

    public function guardarProducto(): void
    {
        $datos = $this->validate($this->reglasProducto(), [], [
            'productoNombre' => 'nombre del producto',
            'productoDescripcion' => 'descripcion',
            'productoCategoriaId' => 'categoria',
            'productoPrecio' => 'precio',
            'productoStock' => 'stock',
            'productoEstadoDisponibilidad' => 'estado de disponibilidad',
        ]);

        $perfil = $this->asegurarPerfilEmprendedor();
        $payload = [
            'emprendedor_id' => $perfil->id,
            'categoria_id' => $datos['productoCategoriaId'] ? (int) $datos['productoCategoriaId'] : null,
            'nombre' => $datos['productoNombre'],
            'descripcion' => $datos['productoDescripcion'] !== '' ? $datos['productoDescripcion'] : null,
            'precio' => $datos['productoPrecio'],
            'stock' => $datos['productoStock'],
            'estado_stock' => $datos['productoEstadoDisponibilidad'],
            'activo' => true,
        ];

        if ($this->productoIdEditando) {
            $this->productoDelPerfil($this->productoIdEditando)->update($payload);
            session()->flash('catalogo_estado', 'Producto actualizado correctamente.');
        } else {
            Producto::create($payload);
            session()->flash('catalogo_estado', 'Producto creado correctamente.');
        }

        $this->cerrarModalProducto();
    }

    public function confirmarEliminarProducto(int $productoId): void
    {
        $this->productoDelPerfil($productoId);
        $this->productoIdEliminar = $productoId;
    }

    public function eliminarProducto(): void
    {
        if (! $this->productoIdEliminar) {
            return;
        }

        $this->productoDelPerfil($this->productoIdEliminar)->delete();
        $this->productoIdEliminar = null;
        session()->flash('catalogo_estado', 'Producto eliminado correctamente.');
        $this->resetPage();
    }

    public function abrirModalCategoria(?int $categoriaId = null): void
    {
        abort(403, 'Las categorias globales se administran desde el panel admin.');
    }

    public function guardarCategoria(): void
    {
        abort(403, 'Las categorias globales se administran desde el panel admin.');
    }

    public function alternarCategoria(int $categoriaId): void
    {
        abort(403, 'Las categorias globales se administran desde el panel admin.');
    }

    public function confirmarEliminarCategoria(int $categoriaId): void
    {
        abort(403, 'Las categorias globales se administran desde el panel admin.');
    }

    public function eliminarCategoria(): void
    {
        abort(403, 'Las categorias globales se administran desde el panel admin.');
    }

    public function cerrarModalProducto(): void
    {
        $this->mostrarModalProducto = false;
        $this->resetFormularioProducto();
    }

    public function cerrarModalCategoria(): void
    {
        $this->mostrarModalCategoria = false;
        $this->resetFormularioCategoria();
    }

    public function render(): View
    {
        $perfil = $this->asegurarPerfilEmprendedor();
        $salesStates = config('reporting.sales_states', ['confirmado', 'entregado', 'completado']);

        $ventasPorProducto = DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->where('pedidos.emprendedor_id', $perfil->id)
            ->whereIn('pedidos.estado', $salesStates)
            ->groupBy('pedido_items.producto_id')
            ->select([
                'pedido_items.producto_id',
                DB::raw('SUM(pedido_items.cantidad) as unidades_vendidas'),
                DB::raw('SUM(pedido_items.subtotal) as ventas_generadas'),
                DB::raw('MAX(pedidos.created_at) as ultima_venta'),
            ]);

        [$graficoDesde, $graficoHasta] = $this->rangoGrafico();

        $topProductosGrafico = DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'productos.id', '=', 'pedido_items.producto_id')
            ->where('pedidos.emprendedor_id', $perfil->id)
            ->whereIn('pedidos.estado', $salesStates)
            ->when($graficoDesde && $graficoHasta, fn ($query) => $query->whereBetween('pedidos.created_at', [$graficoDesde, $graficoHasta]))
            ->when($this->filtroCategoria !== '', fn ($query) => $query->where('productos.categoria_id', (int) $this->filtroCategoria))
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('productos.estado_stock', $this->filtroEstado))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc($this->graficoMetrica === 'ventas' ? DB::raw('SUM(pedido_items.subtotal)') : DB::raw('SUM(pedido_items.cantidad)'))
            ->orderByDesc(DB::raw('SUM(pedido_items.subtotal)'))
            ->limit(5)
            ->get([
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(pedido_items.cantidad) as unidades'),
                DB::raw('SUM(pedido_items.subtotal) as ventas'),
            ]);

        $graficoCampo = $this->graficoMetrica === 'ventas' ? 'ventas' : 'unidades';
        $graficoMax = max((float) ($topProductosGrafico->max($graficoCampo) ?? 0), 1);

        $productos = Producto::query()
            ->where('productos.emprendedor_id', $perfil->id)
            ->leftJoinSub($ventasPorProducto, 'ventas_producto', function ($join) {
                $join->on('ventas_producto.producto_id', '=', 'productos.id');
            })
            ->with('categoria:id,nombre')
            ->select([
                'productos.*',
                DB::raw('COALESCE(ventas_producto.unidades_vendidas, 0) as unidades_vendidas'),
                DB::raw('COALESCE(ventas_producto.ventas_generadas, 0) as ventas_generadas'),
                DB::raw('ventas_producto.ultima_venta as ultima_venta'),
            ])
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('productos.nombre', 'like', '%'.$this->busqueda.'%')
                        ->orWhere('productos.descripcion', 'like', '%'.$this->busqueda.'%');
                });
            })
            ->when($this->filtroCategoria !== '', fn ($query) => $query->where('productos.categoria_id', (int) $this->filtroCategoria))
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('productos.estado_stock', $this->filtroEstado))
            ->orderByDesc('productos.created_at')
            ->paginate(8);

        $resumen = [
            'activos' => Producto::query()->where('emprendedor_id', $perfil->id)->where('activo', true)->count(),
            'agotados' => Producto::query()->where('emprendedor_id', $perfil->id)->where('estado_stock', 'agotado')->count(),
            'stock_critico' => Producto::query()->where('emprendedor_id', $perfil->id)->where(function ($query) {
                $query->where('stock', '<=', 5)->orWhere('estado_stock', 'ultimas_unidades');
            })->count(),
            'ventas_totales' => (float) DB::table('pedidos')
                ->where('emprendedor_id', $perfil->id)
                ->whereIn('estado', $salesStates)
                ->sum('total'),
            'unidades_vendidas' => (int) DB::table('pedido_items')
                ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
                ->where('pedidos.emprendedor_id', $perfil->id)
                ->whereIn('pedidos.estado', $salesStates)
                ->sum('pedido_items.cantidad'),
        ];

        $productosSinVentas = Producto::query()
            ->where('emprendedor_id', $perfil->id)
            ->leftJoinSub($ventasPorProducto, 'ventas_producto', function ($join) {
                $join->on('ventas_producto.producto_id', '=', 'productos.id');
            })
            ->whereRaw('COALESCE(ventas_producto.unidades_vendidas, 0) = 0')
            ->orderByDesc('productos.created_at')
            ->limit(3)
            ->get(['productos.id', 'productos.nombre', 'productos.stock']);

        $productosStockCritico = Producto::query()
            ->where('emprendedor_id', $perfil->id)
            ->where(function ($query) {
                $query->where('stock', '<=', 5)->orWhere('estado_stock', 'ultimas_unidades');
            })
            ->orderBy('stock')
            ->limit(3)
            ->get(['id', 'nombre', 'stock', 'estado_stock']);

        return view('livewire.emprendedor.gestion-catalogo', [
            'categorias' => Categoria::query()->withCount('productos')->orderBy('nombre')->get(),
            'productos' => $productos,
            'perfil' => $perfil,
            'resumen' => $resumen,
            'topProductosGrafico' => $topProductosGrafico,
            'graficoMax' => $graficoMax,
            'productosSinVentas' => $productosSinVentas,
            'productosStockCritico' => $productosStockCritico,
        ])->layout('layouts.emprendedor', [
            'pageTitle' => 'Productos',
        ]);
    }

    private function rangoGrafico(): array
    {
        $hasta = now()->endOfDay();

        return match ($this->graficoPreset) {
            '7d' => [now()->subDays(6)->startOfDay(), $hasta],
            '90d' => [now()->subDays(89)->startOfDay(), $hasta],
            'all' => [null, null],
            default => [now()->subDays(29)->startOfDay(), $hasta],
        };
    }

    private function reglasProducto(): array
    {
        return [
            'productoNombre' => ['required', 'string', 'max:180'],
            'productoDescripcion' => ['nullable', 'string'],
            'productoCategoriaId' => ['nullable', 'integer', 'exists:categorias,id'],
            'productoPrecio' => ['required', 'numeric', 'min:0'],
            'productoStock' => ['required', 'integer', 'min:0'],
            'productoEstadoDisponibilidad' => ['required', Rule::in(['disponible', 'ultimas_unidades', 'agotado'])],
        ];
    }

    private function reglasCategoria(): array
    {
        return [
            'categoriaNombre' => ['required', 'string', 'max:120', Rule::unique('categorias', 'nombre')->ignore($this->categoriaIdEditando)],
            'categoriaDescripcion' => ['nullable', 'string'],
            'categoriaActiva' => ['boolean'],
        ];
    }

    private function resetFormularioProducto(): void
    {
        $this->resetValidation();
        $this->productoIdEditando = null;
        $this->productoNombre = '';
        $this->productoDescripcion = '';
        $this->productoCategoriaId = null;
        $this->productoPrecio = '';
        $this->productoStock = 0;
        $this->productoEstadoDisponibilidad = 'disponible';
    }

    private function resetFormularioCategoria(): void
    {
        $this->resetValidation();
        $this->categoriaIdEditando = null;
        $this->categoriaNombre = '';
        $this->categoriaDescripcion = '';
        $this->categoriaActiva = true;
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

    private function productoDelPerfil(int $productoId): Producto
    {
        $perfil = $this->asegurarPerfilEmprendedor();

        return Producto::query()
            ->where('emprendedor_id', $perfil->id)
            ->findOrFail($productoId);
    }
}
