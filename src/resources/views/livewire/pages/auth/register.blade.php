<?php

use App\Models\PerfilEmprendedor;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $rol = 'usuario';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', Rule::in(['usuario', 'emprendedor'])],
        ]);

        $validated['rol'] = $validated['rol'] === 'emprendedor' ? 'emprendedor' : 'comprador';
        $payload = [
            'nombre_completo' => $validated['name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'estado' => 'activo',
            'email_verified_at' => now(),
        ];

        event(new Registered($user = User::create($payload)));
        $user->asignarRol($validated['rol'] === 'emprendedor' ? 'EMPRENDEDOR' : 'COMPRADOR');

        if ($validated['rol'] === 'emprendedor') {
            PerfilEmprendedor::firstOrCreate([
                'usuario_id' => $user->id,
            ], [
                'nombre_negocio' => $user->name,
                'estado' => 'pendiente',
            ]);
        }

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-ink-muted">Registro</p>
        <h1 class="mt-3 font-display text-4xl text-ink">Crear cuenta</h1>
        <p class="mt-3 text-ink-soft">Registra un usuario o emprendedor para empezar a explorar o publicar dentro de WAYNA.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <div>
            <label for="name" class="wayna-label">Nombre</label>
            <input wire:model="name" id="name" class="wayna-input mt-2 block w-full" type="text" name="name" required autofocus autocomplete="name" placeholder="Tu nombre o tu marca" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="wayna-label">Correo</label>
            <input wire:model="email" id="email" class="wayna-input mt-2 block w-full" type="email" name="email" required autocomplete="username" placeholder="nombre@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="rol" class="wayna-label">Tipo de cuenta</label>
            <select wire:model="rol" id="rol" class="wayna-select mt-2 block w-full" name="rol">
                <option value="usuario">Usuario</option>
                <option value="emprendedor">Emprendedor</option>
            </select>
            <x-input-error :messages="$errors->get('rol')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="wayna-label">Contrasena</label>
            <input wire:model="password" id="password" class="wayna-input mt-2 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="wayna-label">Confirmar contrasena</label>
            <input wire:model="password_confirmation" id="password_confirmation" class="wayna-input mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contrasena" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="wayna-btn-primary w-full">
            Crear cuenta
        </button>
    </form>

    <div class="mt-6 rounded-[1.5rem] border border-primary-100 bg-primary-50/70 p-5 text-sm text-ink-soft">
        <p class="font-medium text-ink">Quieres vender en WAYNA?</p>
        <div class="mt-3 flex flex-wrap gap-3">
            <a href="{{ route('emprendedor.register') }}" wire:navigate class="wayna-btn-primary px-4 py-2">Registro emprendedor</a>
            <a href="{{ route('emprendedor.login') }}" wire:navigate class="wayna-btn-secondary px-4 py-2">Acceso emprendedor</a>
        </div>
    </div>

    <p class="mt-8 text-center text-sm text-ink-soft">
        Ya tienes una cuenta?
        <a href="{{ route('login') }}" wire:navigate class="font-medium text-primary-600 hover:underline">Inicia sesion</a>
    </p>
</div>
