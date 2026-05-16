<?php

namespace App\Livewire\Emprendedor;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class CuentaPersonal extends Component
{
    use WithFileUploads;

    public string $nombreCompleto = '';

    public string $email = '';

    public string $telefono = '';

    public mixed $fotoPerfilNueva = null;

    public bool $eliminarFotoPerfil = false;

    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(): void
    {
        $this->cargarFormulario();
    }

    public function guardarDatosPersonales(): void
    {
        $usuario = auth()->user();

        $datos = $this->validate([
            'nombreCompleto' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'fotoPerfilNueva' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [], [
            'nombreCompleto' => 'nombre completo',
            'email' => 'correo',
            'telefono' => 'telefono',
            'fotoPerfilNueva' => 'foto de perfil',
        ]);

        $usuario->fill([
            'name' => $datos['nombreCompleto'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] !== '' ? $datos['telefono'] : null,
            'foto_perfil' => $this->guardarArchivoPublico(
                $usuario->foto_perfil,
                $this->fotoPerfilNueva,
                'usuarios/perfil',
                $this->eliminarFotoPerfil
            ),
        ]);

        if ($usuario->isDirty('email')) {
            $usuario->email_verified_at = null;
        }

        $usuario->save();

        $this->dispatch('profile-updated', name: $usuario->name);
        $this->fotoPerfilNueva = null;
        $this->eliminarFotoPerfil = false;
        $this->cargarFormulario();

        session()->flash('cuenta_estado', 'Datos personales actualizados correctamente.');
    }

    public function guardarPassword(): void
    {
        try {
            $datos = $this->validate([
                'currentPassword' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', 'same:passwordConfirmation', 'min:8'],
            ], [], [
                'currentPassword' => 'contrasena actual',
                'password' => 'nueva contrasena',
            ]);
        } catch (ValidationException $exception) {
            $this->reset('currentPassword', 'password', 'passwordConfirmation');

            throw $exception;
        }

        auth()->user()->update([
            'password' => Hash::make($datos['password']),
        ]);

        $this->reset('currentPassword', 'password', 'passwordConfirmation');
        session()->flash('password_estado', 'Contrasena actualizada correctamente.');
    }

    public function render(): View
    {
        return view('livewire.emprendedor.cuenta-personal')
            ->layout('layouts.emprendedor', [
                'pageTitle' => 'Mi cuenta',
            ]);
    }

    public function fotoPerfilActualUrl(): ?string
    {
        return $this->resolverUrlArchivo(auth()->user()->foto_perfil);
    }

    private function cargarFormulario(): void
    {
        $usuario = auth()->user();

        $this->resetValidation();
        $this->nombreCompleto = $usuario->name ?? '';
        $this->email = $usuario->email ?? '';
        $this->telefono = $usuario->telefono ?? '';
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
