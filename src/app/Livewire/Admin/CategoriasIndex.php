<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriasIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public bool $mostrarModal = false;

    public ?int $categoriaIdEditando = null;

    public ?int $categoriaIdEliminar = null;

    public string $nombre = '';

    public string $descripcion = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function abrirModal(?int $categoriaId = null): void
    {
        $this->resetFormulario();
        $this->categoriaIdEditando = $categoriaId;

        if ($categoriaId) {
            $categoria = Categoria::query()->findOrFail($categoriaId);
            $this->nombre = $categoria->nombre;
            $this->descripcion = $categoria->descripcion ?? '';
        }

        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'nombre' => ['required', 'string', 'max:120', Rule::unique('categorias', 'nombre')->ignore($this->categoriaIdEditando)],
            'descripcion' => ['nullable', 'string'],
        ], [], [
            'nombre' => 'nombre de categoria',
            'descripcion' => 'descripcion',
        ]);

        $payload = [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
            'icono' => null,
        ];

        if ($this->categoriaIdEditando) {
            Categoria::query()->findOrFail($this->categoriaIdEditando)->update($payload);
            session()->flash('admin_status', 'Categoria actualizada correctamente.');
        } else {
            Categoria::query()->create($payload);
            session()->flash('admin_status', 'Categoria creada correctamente.');
        }

        $this->cerrarModal();
    }

    public function confirmarEliminar(int $categoriaId): void
    {
        $this->categoriaIdEliminar = $categoriaId;
    }

    public function eliminar(): void
    {
        if (! $this->categoriaIdEliminar) {
            return;
        }

        $categoria = Categoria::query()->withCount('productos')->findOrFail($this->categoriaIdEliminar);

        if ($categoria->productos_count > 0) {
            session()->flash('admin_error', 'No puedes eliminar una categoria que tiene productos asociados.');
            $this->categoriaIdEliminar = null;

            return;
        }

        $categoria->delete();
        $this->categoriaIdEliminar = null;
        session()->flash('admin_status', 'Categoria eliminada correctamente.');
        $this->resetPage();
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->resetFormulario();
    }

    public function render(): View
    {
        $categorias = Categoria::query()
            ->withCount('productos')
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('nombre', 'like', '%'.$this->busqueda.'%')
                        ->orWhere('descripcion', 'like', '%'.$this->busqueda.'%');
                });
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.categorias-index', [
            'categorias' => $categorias,
            'resumen' => [
                'totales' => Categoria::query()->count(),
                'con_productos' => Categoria::query()->has('productos')->count(),
                'sin_productos' => Categoria::query()->doesntHave('productos')->count(),
                'con_icono' => Categoria::query()->whereNotNull('icono')->count(),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Categorias',
        ]);
    }

    private function resetFormulario(): void
    {
        $this->resetValidation();
        $this->categoriaIdEditando = null;
        $this->nombre = '';
        $this->descripcion = '';
    }
}
