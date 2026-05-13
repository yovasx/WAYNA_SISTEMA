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
        <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-slate-500">Acceso</p>
        <h1 class="mt-3 font-display text-4xl text-slate-900">Iniciar sesion</h1>
        <p class="mt-3 text-slate-600">Ingresa con tu cuenta de admin, emprendedor o usuario para continuar en WAYNA.</p>
    </div>

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Correo</label>
            <input wire:model="form.email" id="email" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" type="email" name="email" required autofocus autocomplete="username" placeholder="nombre@ejemplo.com" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Contrasena</label>
            <input wire:model="form.password" id="password" class="mt-2 block w-full rounded-2xl border border-[#d9d1e5] bg-[#fcfbfe] px-4 py-3 text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0" type="password" name="password" required autocomplete="current-password" placeholder="********" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-[#cfc5dc] text-[#5f4cae] focus:ring-[#5f4cae]" name="remember">
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-[#5f4cae] hover:underline" href="{{ route('password.request') }}" wire:navigate>
                    Olvide mi contrasena
                </a>
            @endif
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
            Iniciar sesion
        </button>
    </form>

    <div class="mt-8 rounded-[1.5rem] border border-[#e7e0f1] bg-[#f7f2fb] p-5 text-sm text-slate-600">
        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Credenciales seed</p>
        <p class="mt-3"><strong>Admin:</strong> admin@admin.gmail.com / admin123</p>
        <p class="mt-1"><strong>Emprendedor:</strong> emprendedor@wayna.bo / admin123</p>
        <p class="mt-1"><strong>Usuario:</strong> usuario@wayna.bo / admin123</p>
    </div>

    <p class="mt-8 text-center text-sm text-slate-600">
        No tienes cuenta?
        <a href="{{ route('register') }}" wire:navigate class="font-medium text-[#5f4cae] hover:underline">Registrate</a>
    </p>
</div>
