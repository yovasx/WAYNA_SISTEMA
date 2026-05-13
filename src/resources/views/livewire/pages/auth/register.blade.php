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
        $validated['password'] = Hash::make($validated['password']);
        $validated['estado'] = 'activo';
        $validated['email_verified_at'] = now();

        event(new Registered($user = User::create($validated)));

        if ($validated['rol'] === 'emprendedor') {
            PerfilEmprendedor::firstOrCreate([
                'usuario_id' => $user->id,
            ], [
                'nombre_emprendimiento' => $user->name,
                'pais' => 'Bolivia',
                'estado_aprobacion' => 'aprobado',
                'acepta_donaciones' => true,
            ]);
        }

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-slate-500">Registro</p>
        <h1 class="mt-3 font-display text-4xl text-slate-900">Crear cuenta</h1>
        <p class="mt-3 text-slate-600">Registra un usuario o emprendedor para empezar a explorar o publicar dentro de WAYNA.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <div>
            <label for="name" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Nombre</label>
            <input wire:model="name" id="name" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" type="text" name="name" required autofocus autocomplete="name" placeholder="Tu nombre o tu marca" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Correo</label>
            <input wire:model="email" id="email" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" type="email" name="email" required autocomplete="username" placeholder="nombre@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="rol" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Tipo de cuenta</label>
            <select wire:model="rol" id="rol" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" name="rol">
                <option value="usuario">Usuario</option>
                <option value="emprendedor">Emprendedor</option>
            </select>
            <x-input-error :messages="$errors->get('rol')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Contrasena</label>
            <input wire:model="password" id="password" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" type="password" name="password" required autocomplete="new-password" placeholder="Minimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Confirmar contrasena</label>
            <input wire:model="password_confirmation" id="password_confirmation" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contrasena" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
            Crear cuenta
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600">
        Ya tienes una cuenta?
        <a href="{{ route('login') }}" wire:navigate class="font-medium text-[#5f4cae] hover:underline">Inicia sesion</a>
    </p>
</div>
