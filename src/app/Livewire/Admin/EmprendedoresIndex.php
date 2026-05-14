<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class EmprendedoresIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public string $filtroCategoria = '';

    public bool $mostrarModalEdicion = false;

    public ?int $emprendedorEditandoId = null;

    public string $editNombreUsuario = '';

    public string $editEmail = '';

    public string $editTelefono = '';

    public string $editPassword = '';

    public string $editPasswordConfirmacion = '';

    public string $editNuevaPassword = '';

    public string $editNuevaPasswordConfirmacion = '';

    public mixed $editFotoPerfilNueva = null;

    public bool $editEliminarFotoPerfil = false;

    public mixed $editFotoPortadaNueva = null;

    public bool $editEliminarFotoPortada = false;

    public string $editNombreNegocio = '';

    public string $editDescripcion = '';

    public ?string $editNit = '';

    public int|string|null $editCategoriaId = null;

    public function updatingBusqueda(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroCategoria(): void { $this->resetPage(); }

    public function seleccionarFiltroEstado(string $estado = ''): void
    {
        $this->filtroEstado = $estado;
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

    public function abrirModalEdicion(?int $perfilId = null): void
    {
        $this->resetFormulario();

        if (! $perfilId) {
            $this->mostrarModalEdicion = true;

            return;
        }

        $perfil = $this->obtenerPerfil($perfilId);
        $this->emprendedorEditandoId = $perfilId;
        $this->editNombreUsuario = $perfil->usuario?->nombre_completo ?? '';
        $this->editEmail = $perfil->usuario?->email ?? '';
        $this->editTelefono = $perfil->usuario?->telefono ?? '';
        $this->editNombreNegocio = $perfil->nombre_negocio;
        $this->editDescripcion = $perfil->descripcion ?? '';
        $this->editNit = $perfil->nit ?? '';
        $this->editCategoriaId = $perfil->categoria_id;
        $this->mostrarModalEdicion = true;
    }

    public function guardarEdicion(): void
    {
        if (! $this->emprendedorEditandoId) {
            $datos = $this->validate([
                'editNombreUsuario' => ['required', 'string', 'max:255'],
                'editEmail' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')],
                'editTelefono' => ['nullable', 'string', 'max:20'],
                'editPassword' => ['required', 'string', 'min:8', 'same:editPasswordConfirmacion'],
                'editFotoPerfilNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'editFotoPortadaNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'editNombreNegocio' => ['required', 'string', 'max:180'],
                'editDescripcion' => ['nullable', 'string'],
                'editNit' => ['nullable', 'string', 'max:20'],
                'editCategoriaId' => ['nullable', 'integer', 'exists:categorias,id'],
            ], [], [
                'editNombreUsuario' => 'nombre del responsable',
                'editEmail' => 'correo electronico',
                'editPassword' => 'contrasena',
                'editNombreNegocio' => 'nombre del negocio',
                'editDescripcion' => 'descripcion',
                'editNit' => 'NIT',
                'editCategoriaId' => 'categoria',
            ]);

            DB::transaction(function () use ($datos) {
                $usuario = User::query()->create([
                    'nombre_completo' => $datos['editNombreUsuario'],
                    'email' => $datos['editEmail'],
                    'telefono' => $datos['editTelefono'] !== '' ? $datos['editTelefono'] : null,
                    'password_hash' => Hash::make($datos['editPassword']),
                    'foto_perfil' => $this->editFotoPerfilNueva?->store('usuarios/perfil', 'public'),
                    'estado' => 'activo',
                    'email_verified_at' => now(),
                ]);

                $usuario->asignarRol('EMPRENDEDOR');

                PerfilEmprendedor::query()->create([
                    'usuario_id' => $usuario->id,
                    'nombre_negocio' => $datos['editNombreNegocio'],
                    'descripcion' => $datos['editDescripcion'] !== '' ? $datos['editDescripcion'] : null,
                    'nit' => $datos['editNit'] !== '' ? $datos['editNit'] : null,
                    'categoria_id' => $datos['editCategoriaId'] ? (int) $datos['editCategoriaId'] : null,
                    'foto_portada' => $this->editFotoPortadaNueva?->store('emprendedores/portadas', 'public'),
                    'estado' => 'pendiente',
                ]);
            });

            $this->cerrarModalEdicion();
            session()->flash('admin_status', 'Emprendedor creado correctamente y enviado a revision.');

            return;
        }

        $perfil = $this->obtenerPerfil($this->emprendedorEditandoId);
        $usuario = $perfil->usuario;

        $datos = $this->validate([
            'editNombreUsuario' => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario?->id)],
            'editTelefono' => ['nullable', 'string', 'max:20'],
            'editFotoPerfilNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'editNuevaPassword' => ['nullable', 'string', 'min:8', 'same:editNuevaPasswordConfirmacion'],
            'editFotoPortadaNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'editNombreNegocio' => ['required', 'string', 'max:180'],
            'editDescripcion' => ['nullable', 'string'],
            'editNit' => ['nullable', 'string', 'max:20'],
            'editCategoriaId' => ['nullable', 'integer', 'exists:categorias,id'],
        ], [], [
            'editNombreUsuario' => 'nombre del responsable',
            'editEmail' => 'correo electronico',
            'editTelefono' => 'telefono',
            'editFotoPerfilNueva' => 'foto de perfil del responsable',
            'editNuevaPassword' => 'nueva contrasena',
            'editFotoPortadaNueva' => 'portada del emprendimiento',
            'editNombreNegocio' => 'nombre del negocio',
            'editDescripcion' => 'descripcion',
            'editNit' => 'NIT',
            'editCategoriaId' => 'categoria',
        ]);

        DB::transaction(function () use ($datos, $perfil, $usuario) {
            $userPayload = [
                'nombre_completo' => $datos['editNombreUsuario'],
                'email' => $datos['editEmail'],
                'telefono' => $datos['editTelefono'] !== '' ? $datos['editTelefono'] : null,
                'foto_perfil' => $this->guardarArchivoPublico(
                    $usuario?->foto_perfil,
                    $this->editFotoPerfilNueva,
                    'usuarios/perfil',
                    $this->editEliminarFotoPerfil
                ),
            ];

            if ($datos['editNuevaPassword'] !== '') {
                $userPayload['password_hash'] = Hash::make($datos['editNuevaPassword']);
            }

            $usuario?->update($userPayload);

            $perfil->update([
                'nombre_negocio' => $datos['editNombreNegocio'],
                'descripcion' => $datos['editDescripcion'] !== '' ? $datos['editDescripcion'] : null,
                'nit' => $datos['editNit'] !== '' ? $datos['editNit'] : null,
                'categoria_id' => $datos['editCategoriaId'] ? (int) $datos['editCategoriaId'] : null,
                'foto_portada' => $this->guardarArchivoPublico(
                    $perfil->foto_portada,
                    $this->editFotoPortadaNueva,
                    'emprendedores/portadas',
                    $this->editEliminarFotoPortada
                ),
            ]);
        });

        $this->cerrarModalEdicion();
        session()->flash('admin_status', 'Datos del emprendedor actualizados correctamente.');
    }

    public function cerrarModalEdicion(): void
    {
        $this->mostrarModalEdicion = false;
        $this->resetFormulario();
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
                'totales' => PerfilEmprendedor::query()->count(),
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

    private function resetFormulario(): void
    {
        $this->resetValidation();
        $this->emprendedorEditandoId = null;
        $this->editNombreUsuario = '';
        $this->editEmail = '';
        $this->editTelefono = '';
        $this->editPassword = '';
        $this->editPasswordConfirmacion = '';
        $this->editNuevaPassword = '';
        $this->editNuevaPasswordConfirmacion = '';
        $this->editFotoPerfilNueva = null;
        $this->editEliminarFotoPerfil = false;
        $this->editFotoPortadaNueva = null;
        $this->editEliminarFotoPortada = false;
        $this->editNombreNegocio = '';
        $this->editDescripcion = '';
        $this->editNit = '';
        $this->editCategoriaId = null;
    }

    public function editFotoPerfilActualUrl(): ?string
    {
        if (! $this->emprendedorEditandoId) {
            return null;
        }

        $ruta = PerfilEmprendedor::query()
            ->whereKey($this->emprendedorEditandoId)
            ->join('usuarios', 'usuarios.id', '=', 'emprendedores.usuario_id')
            ->value('usuarios.foto_perfil');

        return $this->resolverUrlArchivo($ruta);
    }

    public function editFotoPortadaActualUrl(): ?string
    {
        if (! $this->emprendedorEditandoId) {
            return null;
        }

        $ruta = PerfilEmprendedor::query()->whereKey($this->emprendedorEditandoId)->value('foto_portada');

        return $this->resolverUrlArchivo($ruta);
    }

    private function guardarArchivoPublico(?string $actual, mixed $nuevoArchivo, string $carpeta, bool $eliminar): ?string
    {
        if ($nuevoArchivo) {
            $this->eliminarArchivoPublico($actual);

            return $nuevoArchivo->store($carpeta, 'public');
        }

        if ($eliminar) {
            $this->eliminarArchivoPublico($actual);

            return null;
        }

        return $actual;
    }

    private function eliminarArchivoPublico(?string $ruta): void
    {
        if (! $ruta || filter_var($ruta, FILTER_VALIDATE_URL)) {
            return;
        }

        if (Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }
    }

    private function resolverUrlArchivo(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }

        if (filter_var($ruta, FILTER_VALIDATE_URL)) {
            return $ruta;
        }

        return Storage::disk('public')->url($ruta);
    }
}
