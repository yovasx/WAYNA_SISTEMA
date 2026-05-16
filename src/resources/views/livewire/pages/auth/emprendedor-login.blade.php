<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-ink-muted">Acceso emprendedor</p>
        <h1 class="mt-3 font-display text-4xl text-ink">Ingresa a tu negocio</h1>
        <p class="mt-3 text-ink-soft">Administra pedidos, productos, perfil comercial y el estado operativo de tu emprendimiento desde un acceso pensado para vender dentro de WAYNA.</p>
    </div>

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="wayna-label">Correo</label>
            <input wire:model="form.email" id="email" class="wayna-input mt-2 block w-full" type="email" name="email" required autofocus autocomplete="username" placeholder="tu-negocio@ejemplo.com" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="wayna-label">Contrasena</label>
            <input wire:model="form.password" id="password" class="wayna-input mt-2 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="********" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_emprendedor" class="inline-flex items-center gap-2 text-sm text-ink-soft">
                <input wire:model="form.remember" id="remember_emprendedor" type="checkbox" class="wayna-checkbox" name="remember">
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 hover:underline" href="{{ route('password.request') }}" wire:navigate>
                    Olvide mi contrasena
                </a>
            @endif
        </div>

        <button type="submit" class="wayna-btn-primary w-full">
            Entrar al panel emprendedor
        </button>
    </form>

    <div class="mt-8 rounded-[1.5rem] border border-stroke-soft bg-surface-soft p-5 text-sm text-ink-soft">
        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-ink-muted">Emprendedor demo</p>
        <p class="mt-3"><strong>Correo:</strong> emprendedor@wayna.bo</p>
        <p class="mt-1"><strong>Contrasena:</strong> admin123</p>
    </div>

    <div class="mt-6 rounded-[1.5rem] border border-primary-100 bg-primary-50/70 p-5 text-sm text-ink-soft">
        <p class="font-medium text-ink">Aun no tienes cuenta emprendedora?</p>
        <div class="mt-3 flex flex-wrap gap-3">
            <a href="{{ route('emprendedor.register') }}" wire:navigate class="wayna-btn-primary px-4 py-2">Crear cuenta emprendedora</a>
            <a href="{{ route('login') }}" wire:navigate class="wayna-btn-secondary px-4 py-2">Acceso general</a>
        </div>
    </div>
</div>
