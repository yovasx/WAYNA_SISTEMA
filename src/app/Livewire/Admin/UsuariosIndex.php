<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class UsuariosIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public string $filtroRol = '';

    public bool $mostrarModal = false;

    public ?int $usuarioIdEditando = null;

    public string $nombre = '';

    public string $email = '';

    public string $telefono = '';

    public string $password = '';

    public string $passwordConfirmacion = '';

    public string $nuevaPassword = '';

    public string $nuevaPasswordConfirmacion = '';

    public mixed $fotoPerfilNueva = null;

    public bool $eliminarFotoPerfil = false;

    public bool $mostrarBloqueEmprendedor = false;

    public string $emprendedorNombreNegocio = '';

    public string $emprendedorDescripcion = '';

    public ?string $emprendedorNit = '';

    public int|string|null $emprendedorCategoriaId = null;

    public mixed $emprendedorFotoPortadaNueva = null;

    public bool $eliminarEmprendedorFotoPortada = false;

    public bool $mostrarModalRoles = false;

    public ?int $usuarioIdRoles = null;

    /** @var array<int, bool> */
    public array $rolesSeleccionados = [];

    public ?int $usuarioIdSuspender = null;

    public function updatingBusqueda(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroRol(): void { $this->resetPage(); }

    public function seleccionarFiltroEstado(string $estado = ''): void
    {
        $this->filtroEstado = $estado;
        $this->resetPage();
    }

    public function abrirModal(?int $usuarioId = null): void
    {
        $this->resetFormulario();
        $this->usuarioIdEditando = $usuarioId;

        if ($usuarioId) {
            $usuario = User::query()->findOrFail($usuarioId);
            $this->nombre = $usuario->nombre_completo;
            $this->email = $usuario->email;
            $this->telefono = $usuario->telefono ?? '';

            $this->mostrarBloqueEmprendedor = $usuario->tieneRol('emprendedor') || $usuario->perfilEmprendedor !== null;

            if ($this->mostrarBloqueEmprendedor) {
                $perfil = $usuario->perfilEmprendedor;
                $this->emprendedorNombreNegocio = $perfil?->nombre_negocio ?? '';
                $this->emprendedorDescripcion = $perfil?->descripcion ?? '';
                $this->emprendedorNit = $perfil?->nit ?? '';
                $this->emprendedorCategoriaId = $perfil?->categoria_id;
            }
        }

        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($this->usuarioIdEditando)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'fotoPerfilNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if (! $this->usuarioIdEditando) {
            $rules['password'] = ['required', 'string', 'min:8', 'same:passwordConfirmacion'];
        } else {
            $rules['nuevaPassword'] = ['nullable', 'string', 'min:8', 'same:nuevaPasswordConfirmacion'];
        }

        if ($this->mostrarBloqueEmprendedor && $this->usuarioIdEditando) {
            $rules['emprendedorNombreNegocio'] = ['required', 'string', 'max:180'];
            $rules['emprendedorDescripcion'] = ['nullable', 'string'];
            $rules['emprendedorNit'] = ['nullable', 'string', 'max:20'];
            $rules['emprendedorCategoriaId'] = ['nullable', 'integer', 'exists:categorias,id'];
            $rules['emprendedorFotoPortadaNueva'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        $datos = $this->validate($rules, [], [
            'nombre' => 'nombre completo',
            'email' => 'correo electronico',
            'telefono' => 'telefono',
            'fotoPerfilNueva' => 'foto de perfil',
            'nuevaPassword' => 'nueva contrasena',
            'emprendedorNombreNegocio' => 'nombre del negocio',
            'emprendedorDescripcion' => 'descripcion',
            'emprendedorNit' => 'NIT',
            'emprendedorCategoriaId' => 'categoria del emprendedor',
            'emprendedorFotoPortadaNueva' => 'portada del emprendimiento',
        ]);

        if (! $this->usuarioIdEditando) {
            $payload = [
                'nombre_completo' => $datos['nombre'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'] !== '' ? $datos['telefono'] : null,
            ];
            $payload['password_hash'] = Hash::make($this->password);
            $payload['estado'] = 'activo';
            $payload['email_verified_at'] = now();
            $payload['foto_perfil'] = $this->fotoPerfilNueva?->store('usuarios/perfil', 'public');

            $usuario = User::query()->create($payload);
            $usuario->asignarRol('COMPRADOR');
            session()->flash('admin_status', 'Usuario creado correctamente.');
        } else {
            $usuario = User::query()->with('perfilEmprendedor')->findOrFail($this->usuarioIdEditando);

            $payload = [
                'nombre_completo' => $datos['nombre'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'] !== '' ? $datos['telefono'] : null,
                'foto_perfil' => $this->guardarArchivoPublico(
                    $usuario->foto_perfil,
                    $this->fotoPerfilNueva,
                    'usuarios/perfil',
                    $this->eliminarFotoPerfil
                ),
            ];

            if ($this->nuevaPassword !== '') {
                $payload['password_hash'] = Hash::make($this->nuevaPassword);
            }

            $usuario->update($payload);

            if ($this->mostrarBloqueEmprendedor) {
                $perfil = $usuario->perfilEmprendedor;

                if (! $perfil) {
                    $perfil = PerfilEmprendedor::query()->create([
                        'usuario_id' => $usuario->id,
                        'nombre_negocio' => $datos['emprendedorNombreNegocio'],
                        'estado' => 'pendiente',
                    ]);
                }

                $perfil->update([
                    'nombre_negocio' => $datos['emprendedorNombreNegocio'],
                    'descripcion' => $datos['emprendedorDescripcion'] !== '' ? $datos['emprendedorDescripcion'] : null,
                    'nit' => $datos['emprendedorNit'] !== '' ? $datos['emprendedorNit'] : null,
                    'categoria_id' => $datos['emprendedorCategoriaId'] ? (int) $datos['emprendedorCategoriaId'] : null,
                    'foto_portada' => $this->guardarArchivoPublico(
                        $perfil->foto_portada,
                        $this->emprendedorFotoPortadaNueva,
                        'emprendedores/portadas',
                        $this->eliminarEmprendedorFotoPortada
                    ),
                ]);
            }

            session()->flash('admin_status', 'Usuario actualizado correctamente.');
        }

        $this->cerrarModal();
    }

    public function abrirModalRoles(int $usuarioId): void
    {
        $this->usuarioIdRoles = $usuarioId;
        $usuario = User::query()->findOrFail($usuarioId);
        $rolesActuales = $usuario->roles()->pluck('roles.id')->toArray();
        $this->rolesSeleccionados = [];
        foreach (Role::query()->pluck('id') as $rolId) {
            $this->rolesSeleccionados[(int) $rolId] = in_array($rolId, $rolesActuales, true);
        }
        $this->mostrarModalRoles = true;
    }

    public function guardarRoles(): void
    {
        $usuario = User::query()->findOrFail($this->usuarioIdRoles);
        $rolesActivos = collect($this->rolesSeleccionados)
            ->filter(fn ($activo) => $activo)
            ->keys()
            ->toArray();
        $usuario->roles()->sync($rolesActivos);
        session()->flash('admin_status', 'Roles del usuario actualizados correctamente.');
        $this->cerrarModalRoles();
    }

    public function suspender(int $usuarioId): void
    {
        $usuario = User::query()->findOrFail($usuarioId);

        if ($usuario->email === 'admin@admin.gmail.com') {
            session()->flash('admin_error', 'No puedes suspender al administrador principal.');
            return;
        }

        $usuario->update(['estado' => 'suspendido']);
        $usuario->perfilEmprendedor?->update(['estado' => 'suspendido']);
        session()->flash('admin_status', 'Usuario suspendido correctamente.');
    }

    public function activar(int $usuarioId): void
    {
        $usuario = User::query()->findOrFail($usuarioId);
        $usuario->update(['estado' => 'activo']);
        session()->flash('admin_status', 'Usuario activado correctamente.');
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->resetFormulario();
    }

    public function cerrarModalRoles(): void
    {
        $this->mostrarModalRoles = false;
        $this->usuarioIdRoles = null;
        $this->rolesSeleccionados = [];
    }

    public function render(): View
    {
        $usuarios = User::query()
            ->with('roles')
            ->when($this->busqueda !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('nombre_completo', 'like', '%'.$this->busqueda.'%')
                        ->orWhere('email', 'like', '%'.$this->busqueda.'%');
                });
            })
            ->when($this->filtroEstado !== '', fn ($query) => $query->where('estado', $this->filtroEstado))
            ->when($this->filtroRol !== '', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('nombre', $this->filtroRol);
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.usuarios-index', [
            'usuarios' => $usuarios,
            'categorias' => Categoria::query()->orderBy('nombre')->get(['id', 'nombre']),
            'roles' => Role::query()->orderBy('nombre')->get(),
            'resumen' => [
                'totales' => User::query()->count(),
                'activos' => User::query()->where('estado', 'activo')->count(),
                'suspendidos' => User::query()->where('estado', 'suspendido')->count(),
                'pendientes' => User::query()->where('estado', 'pendiente')->count(),
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Usuarios',
        ]);
    }

    private function resetFormulario(): void
    {
        $this->resetValidation();
        $this->usuarioIdEditando = null;
        $this->nombre = '';
        $this->email = '';
        $this->telefono = '';
        $this->password = '';
        $this->passwordConfirmacion = '';
        $this->nuevaPassword = '';
        $this->nuevaPasswordConfirmacion = '';
        $this->fotoPerfilNueva = null;
        $this->eliminarFotoPerfil = false;
        $this->mostrarBloqueEmprendedor = false;
        $this->emprendedorNombreNegocio = '';
        $this->emprendedorDescripcion = '';
        $this->emprendedorNit = '';
        $this->emprendedorCategoriaId = null;
        $this->emprendedorFotoPortadaNueva = null;
        $this->eliminarEmprendedorFotoPortada = false;
    }

    public function fotoPerfilActualUrl(): ?string
    {
        if (! $this->usuarioIdEditando) {
            return null;
        }

        $ruta = User::query()->whereKey($this->usuarioIdEditando)->value('foto_perfil');

        return $this->resolverUrlArchivo($ruta);
    }

    public function fotoPortadaActualUrl(): ?string
    {
        if (! $this->usuarioIdEditando || ! $this->mostrarBloqueEmprendedor) {
            return null;
        }

        $ruta = PerfilEmprendedor::query()->where('usuario_id', $this->usuarioIdEditando)->value('foto_portada');

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
