<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EmprendedoresIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public string $filtroCategoria = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function aprobar(int $perfilId): void
    {
        $perfil = $this->obtenerPerfil($perfilId);
        $perfil->update(['estado' => 'activo']);
        $perfil->usuario?->update(['estado' => 'activo']);

        session()->flash('admin_status', 'Emprendedor aprobado correctamente.');
    }

    public function suspender(int $perfilId): void
    {
        $perfil = $this->obtenerPerfil($perfilId);
        $perfil->update(['estado' => 'suspendido']);
        $perfil->usuario?->update(['estado' => 'suspendido']);

        session()->flash('admin_status', 'Emprendedor suspendido correctamente.');
    }

    public function reactivar(int $perfilId): void
    {
        $perfil = $this->obtenerPerfil($perfilId);
        $perfil->update(['estado' => 'activo']);
        $perfil->usuario?->update(['estado' => 'activo']);

        session()->flash('admin_status', 'Emprendedor reactivado correctamente.');
    }

    public function render(): View
    {
        $emprendedores = PerfilEmprendedor::query()
            ->with(['usuario:id,nombre_completo,email,estado,created_at', 'categoria:id,nombre'])
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('nombre_negocio', 'like', '%'.$this->busqueda.'%')
                        ->orWhereHas('usuario', function ($userQuery) {
                            $userQuery
                                ->where('nombre_completo', 'like', '%'.$this->busqueda.'%')
                                ->orWhere('email', 'like', '%'.$this->busqueda.'%');
                        });
                });
            })
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado', $this->filtroEstado === 'aprobado' ? 'activo' : $this->filtroEstado))
            ->when($this->filtroCategoria !== '', fn ($query) => $query->where('categoria_id', (int) $this->filtroCategoria))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.emprendedores-index', [
            'emprendedores' => $emprendedores,
            'categorias' => Categoria::query()->orderBy('nombre')->get(['id', 'nombre']),
            'resumen' => [
                'pendientes' => PerfilEmprendedor::query()->where('estado', 'pendiente')->count(),
                'aprobados' => PerfilEmprendedor::query()->where('estado', 'activo')->count(),
                'suspendidos' => PerfilEmprendedor::query()->where('estado', 'suspendido')->count(),
                'con_nit' => PerfilEmprendedor::query()->whereNotNull('nit')->count(),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Emprendedores',
        ]);
    }

    private function obtenerPerfil(int $perfilId): PerfilEmprendedor
    {
        return PerfilEmprendedor::query()->with('usuario')->findOrFail($perfilId);
    }
}
