<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UsuariosIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    public string $filtroRol = '';

    public bool $mostrarModal = false;

    public ?int $usuarioIdEditando = null;

    public string $nombre = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmacion = '';

    public bool $mostrarModalRoles = false;

    public ?int $usuarioIdRoles = null;

    /** @var array<int, bool> */
    public array $rolesSeleccionados = [];

    public ?int $usuarioIdSuspender = null;

    public function updatingBusqueda(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroRol(): void { $this->resetPage(); }

    public function abrirModal(?int $usuarioId = null): void
    {
        $this->resetFormulario();
        $this->usuarioIdEditando = $usuarioId;

        if ($usuarioId) {
            $usuario = User::query()->findOrFail($usuarioId);
            $this->nombre = $usuario->nombre_completo;
            $this->email = $usuario->email;
        }

        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($this->usuarioIdEditando)],
        ];

        if (! $this->usuarioIdEditando) {
            $rules['password'] = ['required', 'string', 'min:8', 'same:passwordConfirmacion'];
        }

        $datos = $this->validate($rules, [], [
            'nombre' => 'nombre completo',
            'email' => 'correo electronico',
        ]);

        $payload = [
            'nombre_completo' => $datos['nombre'],
            'email' => $datos['email'],
        ];

        if (! $this->usuarioIdEditando) {
            $payload['password_hash'] = Hash::make($this->password);
            $payload['estado'] = 'activo';
            $payload['email_verified_at'] = now();
        }

        if ($this->usuarioIdEditando) {
            User::query()->findOrFail($this->usuarioIdEditando)->update($payload);
            session()->flash('admin_status', 'Usuario actualizado correctamente.');
        } else {
            $usuario = User::query()->create($payload);
            $usuario->asignarRol('COMPRADOR');
            session()->flash('admin_status', 'Usuario creado correctamente.');
        }

        $this->cerrarModal();
    }

    public function abrirModalRoles(int $usuarioId): void
    {
        $this->usuarioIdRoles = $usuarioId;
        $usuario = User::query()->findOrFail($usuarioId);
        $rolesActuales = $usuario->roles()->pluck('rol_id')->toArray();
        $this->rolesSeleccionados = [];
        foreach (Role::query()->pluck('id') as $rolId) {
            $this->rolesSeleccionados[(int) $rolId] = in_array($rolId, $rolesActuales, true) ? true : true;
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
            ->latest()
            ->paginate(15);

        if ($this->filtroRol !== '') {
            $rolId = Role::query()->where('nombre', $this->filtroRol)->value('id');
            $usuarioIds = \DB::table('usuario_roles')->where('rol_id', $rolId)->pluck('usuario_id');
            $usuarios = User::query()->whereIn('id', $usuarioIds)
                ->with('roles')
                ->when($this->busqueda !== '', function ($query) {
                    $query->where(function ($subquery) {
                        $subquery->where('nombre_completo', 'like', '%'.$this->busqueda.'%')
                            ->orWhere('email', 'like', '%'.$this->busqueda.'%');
                    });
                })
                ->latest()
                ->paginate(15);
        }

        return view('livewire.admin.usuarios-index', [
            'usuarios' => $usuarios,
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
        $this->password = '';
        $this->passwordConfirmacion = '';
    }
}
