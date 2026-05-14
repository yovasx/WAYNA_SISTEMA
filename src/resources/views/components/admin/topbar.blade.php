<header class="fixed inset-x-0 top-0 z-50 border-b border-[#d8d2de] bg-[#fdf8ff]/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-[1600px] items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button type="button" @click="mobileSidebarOpen = true" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-[#d8d2de] bg-white text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae] md:hidden" aria-label="Abrir menu">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <button type="button" @click="toggleSidebar()" class="hidden h-10 w-10 items-center justify-center rounded-2xl border border-[#d8d2de] bg-white text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae] md:inline-flex" aria-label="Alternar sidebar">
                <span class="material-symbols-outlined" x-text="sidebarState === 'expanded' ? 'left_panel_close' : (sidebarState === 'icons' ? 'push_pin' : 'left_panel_open')"></span>
            </button>

            <a href="{{ route('dashboard.admin') }}" wire:navigate class="font-display text-2xl text-[#5f4cae]">WAYNA</a>
        </div>

        <div class="hidden items-center rounded-full border border-[#d8d2de] bg-[#f7f2fb] px-4 py-1 md:flex">
            <span class="material-symbols-outlined mr-2 text-[20px] text-slate-400">search</span>
            <input type="text" placeholder="Buscar en el panel..." class="w-64 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400">
        </div>

        <div class="flex items-center gap-3 text-sm">
            <button class="hidden h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-[#f7f2fb] hover:text-[#5f4cae] md:inline-flex" aria-label="Notificaciones">
                <span class="material-symbols-outlined">notifications</span>
            </button>

            <button class="hidden h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-[#f7f2fb] hover:text-[#5f4cae] md:inline-flex" aria-label="Carrito">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>

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
