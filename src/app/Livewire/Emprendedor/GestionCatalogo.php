<?php

namespace App\Livewire\Emprendedor;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class GestionCatalogo extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroCategoria = '';

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

    public bool $productoDestacado = false;

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
            $this->productoDestacado = $producto->destacado;
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
            'perfil_emprendedor_id' => $perfil->id,
            'categoria_id' => $datos['productoCategoriaId'] ? (int) $datos['productoCategoriaId'] : null,
            'nombre' => $datos['productoNombre'],
            'slug' => $this->generarSlugProducto($datos['productoNombre'], $this->productoIdEditando),
            'descripcion' => $datos['productoDescripcion'] !== '' ? $datos['productoDescripcion'] : null,
            'precio' => $datos['productoPrecio'],
            'stock' => $datos['productoStock'],
            'estado_disponibilidad' => $datos['productoEstadoDisponibilidad'],
            'destacado' => $datos['productoDestacado'],
            'publicado_at' => now(),
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
        $this->resetFormularioCategoria();
        $this->categoriaIdEditando = $categoriaId;

        if ($categoriaId) {
            $categoria = Categoria::findOrFail($categoriaId);
            $this->categoriaNombre = $categoria->nombre;
            $this->categoriaDescripcion = $categoria->descripcion ?? '';
            $this->categoriaActiva = $categoria->activa;
        }

        $this->mostrarModalCategoria = true;
    }

    public function guardarCategoria(): void
    {
        $datos = $this->validate($this->reglasCategoria(), [], [
            'categoriaNombre' => 'nombre de categoria',
            'categoriaDescripcion' => 'descripcion',
        ]);

        $payload = [
            'nombre' => $datos['categoriaNombre'],
            'slug' => $this->generarSlugCategoria($datos['categoriaNombre'], $this->categoriaIdEditando),
            'descripcion' => $datos['categoriaDescripcion'] !== '' ? $datos['categoriaDescripcion'] : null,
            'activa' => $datos['categoriaActiva'],
        ];

        if ($this->categoriaIdEditando) {
            Categoria::findOrFail($this->categoriaIdEditando)->update($payload);
            session()->flash('catalogo_estado', 'Categoria actualizada correctamente.');
        } else {
            Categoria::create($payload);
            session()->flash('catalogo_estado', 'Categoria creada correctamente.');
        }

        $this->cerrarModalCategoria();
    }

    public function alternarCategoria(int $categoriaId): void
    {
        $categoria = Categoria::findOrFail($categoriaId);
        $categoria->update([
            'activa' => ! $categoria->activa,
        ]);

        session()->flash('catalogo_estado', 'Estado de categoria actualizado.');
    }

    public function confirmarEliminarCategoria(int $categoriaId): void
    {
        $this->categoriaIdEliminar = $categoriaId;
    }

    public function eliminarCategoria(): void
    {
        if (! $this->categoriaIdEliminar) {
            return;
        }

        $categoria = Categoria::withCount('productos')->findOrFail($this->categoriaIdEliminar);

        if ($categoria->productos_count > 0) {
            session()->flash('catalogo_error', 'No puedes eliminar una categoria que ya tiene productos asociados.');
            $this->categoriaIdEliminar = null;

            return;
        }

        $categoria->delete();
        $this->categoriaIdEliminar = null;
        session()->flash('catalogo_estado', 'Categoria eliminada correctamente.');
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

        return view('livewire.emprendedor.gestion-catalogo', [
            'categorias' => Categoria::query()->withCount('productos')->orderBy('nombre')->get(),
            'productos' => Producto::query()
                ->where('perfil_emprendedor_id', $perfil->id)
                ->with('categoria:id,nombre')
                ->when($this->busqueda !== '', function ($query) {
                    $query->where(function ($subquery) {
                        $subquery
                            ->where('nombre', 'like', '%'.$this->busqueda.'%')
                            ->orWhere('descripcion', 'like', '%'.$this->busqueda.'%');
                    });
                })
                ->when($this->filtroCategoria !== '', fn ($query) => $query->where('categoria_id', (int) $this->filtroCategoria))
                ->latest()
                ->paginate(8),
            'perfil' => $perfil,
        ]);
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
            'productoDestacado' => ['boolean'],
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
        $this->productoDestacado = false;
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
            'nombre_emprendimiento' => $usuario->name,
            'pais' => 'Bolivia',
            'estado_aprobacion' => 'aprobado',
            'acepta_donaciones' => true,
        ]);
    }

    private function productoDelPerfil(int $productoId): Producto
    {
        $perfil = $this->asegurarPerfilEmprendedor();

        return Producto::query()
            ->where('perfil_emprendedor_id', $perfil->id)
            ->findOrFail($productoId);
    }

    private function generarSlugProducto(string $nombre, ?int $ignorarId = null): string
    {
        $base = Str::slug($nombre);
        $slug = $base;
        $contador = 1;

        while (Producto::query()->where('slug', $slug)->when($ignorarId, fn ($query) => $query->where('id', '!=', $ignorarId))->exists()) {
            $slug = $base.'-'.$contador;
            $contador++;
        }

        return $slug;
    }

    private function generarSlugCategoria(string $nombre, ?int $ignorarId = null): string
    {
        $base = Str::slug($nombre);
        $slug = $base;
        $contador = 1;

        while (Categoria::query()->where('slug', $slug)->when($ignorarId, fn ($query) => $query->where('id', '!=', $ignorarId))->exists()) {
            $slug = $base.'-'.$contador;
            $contador++;
        }

        return $slug;
    }
}
