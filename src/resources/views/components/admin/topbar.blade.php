<header class="fixed inset-x-0 top-0 z-50 border-b border-[#d8d2de] bg-[#fdf8ff]/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-[1600px] items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button type="button" @click="mobileSidebarOpen = true" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-[#d8d2de] bg-white text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae] md:hidden" aria-label="Abrir menu">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <button type="button" @click="toggleSidebar()" class="hidden h-10 w-10 items-center justify-center rounded-2xl border border-[#d8d2de] bg-white text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae] md:inline-flex" aria-label="Alternar sidebar">
                <span class="material-symbols-outlined" x-text="sidebarExpanded ? 'left_panel_close' : 'left_panel_open'"></span>
            </button>

            <a href="{{ route('dashboard.admin') }}" wire:navigate class="font-display text-2xl text-[#5f4cae]">WAYNA</a>
        </div>

        <div class="flex items-center gap-3 text-sm">
            <span class="hidden rounded-full bg-[#e6deff] px-3 py-1 font-mono-data text-[11px] uppercase tracking-[0.24em] text-[#4a3597] sm:inline-flex">
                Administracion
            </span>

            <a href="{{ route('profile') }}" wire:navigate class="hidden rounded-full border border-[#d8d2de] bg-white px-4 py-2 text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae] sm:inline-flex">
                Perfil
            </a>

            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#5f4cae] font-medium text-white">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#d8d2de] bg-white text-slate-600 transition hover:border-[#a03f29] hover:text-[#a03f29]" aria-label="Cerrar sesion">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
