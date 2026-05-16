<?php

use App\Models\PerfilEmprendedor;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $payload = [
            'nombre_completo' => $validated['name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'estado' => 'activo',
            'email_verified_at' => now(),
        ];

        event(new Registered($user = User::create($payload)));
        $user->asignarRol('EMPRENDEDOR');

        PerfilEmprendedor::firstOrCreate([
            'usuario_id' => $user->id,
        ], [
            'nombre_negocio' => $user->name,
            'estado' => 'pendiente',
        ]);

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-ink-muted">Registro emprendedor</p>
        <h1 class="mt-3 font-display text-4xl text-ink">Crea tu espacio emprendedor</h1>
        <p class="mt-3 text-ink-soft">Registra tu negocio para publicar productos, recibir pedidos y preparar una vitrina publica mas completa dentro de WAYNA.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <div>
            <label for="emprendedor_name" class="wayna-label">Nombre del responsable o marca</label>
            <input wire:model="name" id="emprendedor_name" class="wayna-input mt-2 block w-full" type="text" name="name" required autofocus autocomplete="name" placeholder="Nombre de tu marca o responsable" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="emprendedor_email" class="wayna-label">Correo</label>
            <input wire:model="email" id="emprendedor_email" class="wayna-input mt-2 block w-full" type="email" name="email" required autocomplete="username" placeholder="negocio@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="emprendedor_password" class="wayna-label">Contrasena</label>
            <input wire:model="password" id="emprendedor_password" class="wayna-input mt-2 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="emprendedor_password_confirmation" class="wayna-label">Confirmar contrasena</label>
            <input wire:model="password_confirmation" id="emprendedor_password_confirmation" class="wayna-input mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contrasena" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="wayna-btn-primary w-full">
            Crear cuenta emprendedora
        </button>
    </form>

    <div class="mt-6 rounded-[1.5rem] border border-accent-100 bg-accent-50 p-5 text-sm text-ink-soft">
        <p class="font-medium text-ink">Que obtienes al registrarte?</p>
        <ul class="mt-3 space-y-2">
            <li>- Panel propio para pedidos, productos y perfil comercial.</li>
            <li>- Estado de aprobacion visible para preparar tu presencia publica.</li>
            <li>- Base lista para vitrina publica, ubicacion y catalogo real.</li>
        </ul>
    </div>

    <p class="mt-8 text-center text-sm text-ink-soft">
        Ya tienes una cuenta emprendedora?
        <a href="{{ route('emprendedor.login') }}" wire:navigate class="font-medium text-primary-600 hover:underline">Ingresa aqui</a>
    </p>

    <p class="mt-3 text-center text-sm text-ink-soft">
        Buscas comprar o explorar?
        <a href="{{ route('register') }}" wire:navigate class="font-medium text-primary-600 hover:underline">Registro general</a>
    </p>
</div>
