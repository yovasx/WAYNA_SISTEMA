@props(['pageTitle' => 'Panel emprendedor'])

<header class="sticky top-0 z-50 border-b border-stroke bg-surface/95 backdrop-blur wayna-shell">
    @php
        $perfil = auth()->user()->perfilEmprendedor;
        $estadoPerfil = $perfil?->estado_aprobacion ?? 'pendiente';
        $estadoTone = match ($estadoPerfil) {
            'aprobado' => 'green',
            'suspendido' => 'red',
            default => 'amber',
        };
    @endphp
    <div class="mx-auto flex h-16 max-w-[1600px] items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button type="button" @click="openMobileSidebar()" class="wayna-icon-btn border-stroke text-ink-soft hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700 md:hidden" aria-label="Abrir menu">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <button type="button" @click="toggleDesktopSidebar()" class="hidden md:inline-flex wayna-icon-btn border-stroke text-ink-soft hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700" aria-label="Alternar barra lateral">
                <span class="material-symbols-outlined" x-text="sidebarEffective === 'expanded' ? 'left_panel_close' : 'left_panel_open'">left_panel_open</span>
            </button>

            <a href="{{ route('dashboard.emprendedor') }}" wire:navigate class="font-display text-2xl text-primary-600">WAYNA</a>

            <div class="hidden md:block">
                <p class="text-xs uppercase tracking-[0.24em] text-ink-muted">Area emprendedora</p>
                <p class="text-sm font-medium text-ink">{{ $pageTitle }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 text-sm">
            <div class="hidden items-center gap-3 rounded-full border border-stroke bg-surface-raised px-4 py-2 md:inline-flex">
                <span class="text-xs uppercase tracking-[0.2em] text-ink-muted">Estado</span>
                <x-admin.status-badge :tone="$estadoTone">{{ $estadoPerfil }}</x-admin.status-badge>
            </div>

            <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="hidden rounded-full border border-stroke bg-surface-raised px-4 py-2 text-ink-soft transition hover:border-primary-300 hover:text-primary-700 lg:inline-flex">
                Mis pedidos
            </a>

            <a href="{{ route('emprendedor.cuenta.index') }}" wire:navigate class="hidden rounded-full border border-stroke bg-surface-raised px-4 py-2 text-ink-soft transition hover:border-primary-300 hover:text-primary-700 md:inline-flex">
                Mi cuenta
            </a>

            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 font-medium text-white shadow-wayna">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="wayna-icon-btn rounded-full border-stroke text-ink-soft hover:border-danger-300 hover:bg-danger-50 hover:text-danger-700" aria-label="Cerrar sesion">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
