<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ProductosIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroCategoria = '';

    public string $filtroEmprendedor = '';

    public string $filtroEstado = '';

    public bool $mostrarModal = false;

    public ?int $productoIdEditando = null;

    public ?int $productoIdEliminar = null;

    public int|string|null $perfilEmprendedorId = null;

    public int|string|null $categoriaId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public string $precio = '';

    public int $stock = 0;

    public string $estadoDisponibilidad = 'disponible';

    public bool $activo = true;

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEmprendedor(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function abrirModal(?int $productoId = null): void
    {
        if (! $productoId && ! PerfilEmprendedor::query()->exists()) {
            session()->flash('admin_error', 'Necesitas al menos un perfil emprendedor antes de crear productos.');

            return;
        }

        $this->resetFormulario();
        $this->productoIdEditando = $productoId;

        if ($productoId) {
            $producto = Producto::query()->findOrFail($productoId);
            $this->perfilEmprendedorId = $producto->emprendedor_id;
            $this->categoriaId = $producto->categoria_id;
            $this->nombre = $producto->nombre;
            $this->descripcion = $producto->descripcion ?? '';
            $this->precio = number_format((float) $producto->precio, 2, '.', '');
            $this->stock = $producto->stock;
            $this->estadoDisponibilidad = $producto->estado_disponibilidad;
            $this->activo = $producto->activo;
        }

        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'perfilEmprendedorId' => ['required', 'integer', 'exists:emprendedores,id'],
            'categoriaId' => ['nullable', 'integer', 'exists:categorias,id'],
            'nombre' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'estadoDisponibilidad' => ['required', Rule::in(['disponible', 'ultimas_unidades', 'agotado'])],
            'activo' => ['boolean'],
        ], [], [
            'perfilEmprendedorId' => 'emprendedor',
            'categoriaId' => 'categoria',
        ]);

        $payload = [
            'emprendedor_id' => (int) $datos['perfilEmprendedorId'],
            'categoria_id' => $datos['categoriaId'] ? (int) $datos['categoriaId'] : null,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
            'precio' => $datos['precio'],
            'stock' => $datos['stock'],
            'estado_stock' => $datos['estadoDisponibilidad'],
            'activo' => $datos['activo'],
        ];

        if ($this->productoIdEditando) {
            Producto::query()->findOrFail($this->productoIdEditando)->update($payload);
            session()->flash('admin_status', 'Producto actualizado correctamente.');
        } else {
            Producto::query()->create($payload);
            session()->flash('admin_status', 'Producto creado correctamente.');
        }

        $this->cerrarModal();
    }

    public function alternarActivo(int $productoId): void
    {
        $producto = Producto::query()->findOrFail($productoId);
        $producto->update(['activo' => ! $producto->activo]);

        session()->flash('admin_status', 'Estado activo del producto actualizado.');
    }

    public function actualizarEstado(int $productoId, string $estado): void
    {
        abort_unless(in_array($estado, ['disponible', 'ultimas_unidades', 'agotado'], true), 422);

        $producto = Producto::query()->findOrFail($productoId);
        $producto->update(['estado_stock' => $estado]);

        session()->flash('admin_status', 'Disponibilidad del producto actualizada.');
    }

    public function confirmarEliminar(int $productoId): void
    {
        $this->productoIdEliminar = $productoId;
    }

    public function eliminar(): void
    {
        if (! $this->productoIdEliminar) {
            return;
        }

        Producto::query()->findOrFail($this->productoIdEliminar)->delete();
        $this->productoIdEliminar = null;
        session()->flash('admin_status', 'Producto eliminado correctamente.');
        $this->resetPage();
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->resetFormulario();
    }

    public function render(): View
    {
        $productos = Producto::query()
            ->with(['categoria:id,nombre', 'perfilEmprendedor:id,nombre_negocio,usuario_id', 'perfilEmprendedor.usuario:id,nombre_completo'])
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('nombre', 'like', '%'.$this->busqueda.'%')
                        ->orWhere('descripcion', 'like', '%'.$this->busqueda.'%')
                        ->orWhereHas('perfilEmprendedor', function ($perfilQuery) {
                            $perfilQuery->where('nombre_negocio', 'like', '%'.$this->busqueda.'%');
                        });
                });
            })
            ->when($this->filtroCategoria !== '', fn ($query) => $query->where('categoria_id', (int) $this->filtroCategoria))
            ->when($this->filtroEmprendedor !== '', fn ($query) => $query->where('emprendedor_id', (int) $this->filtroEmprendedor))
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado_stock', $this->filtroEstado))
            ->latest()
            ->paginate(12);

        return view('livewire.admin.productos-index', [
            'productos' => $productos,
            'categorias' => Categoria::query()->orderBy('nombre')->get(['id', 'nombre']),
            'perfiles' => PerfilEmprendedor::query()->with('usuario:id,nombre_completo')->orderBy('nombre_negocio')->get(['id', 'nombre_negocio', 'usuario_id']),
            'resumen' => [
                'totales' => Producto::query()->count(),
                'activos' => Producto::query()->where('activo', true)->count(),
                'agotados' => Producto::query()->where('estado_stock', 'agotado')->count(),
                'stock_critico' => Producto::query()->where('stock', '<=', 5)->count(),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Productos',
        ]);
    }

    private function resetFormulario(): void
    {
        $this->resetValidation();
        $this->productoIdEditando = null;
        $this->perfilEmprendedorId = null;
        $this->categoriaId = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->precio = '';
        $this->stock = 0;
        $this->estadoDisponibilidad = 'disponible';
        $this->activo = true;
    }
}
