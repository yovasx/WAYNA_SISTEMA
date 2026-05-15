<header class="sticky top-0 z-50 border-b border-stroke bg-surface/95 backdrop-blur wayna-shell">
    <div class="mx-auto flex h-16 max-w-[1600px] items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button type="button" @click="openMobileSidebar()" class="wayna-icon-btn border-stroke text-ink-soft hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700 md:hidden" aria-label="Abrir menu">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <a href="{{ route('dashboard.admin') }}" wire:navigate class="font-display text-2xl text-primary-600">WAYNA</a>
        </div>

        <div class="hidden items-center rounded-full border border-stroke bg-surface-raised px-4 py-1 shadow-sm md:flex">
            <span class="material-symbols-outlined mr-2 text-[20px] text-ink-muted">search</span>
            <input type="text" placeholder="Buscar en el panel..." class="w-64 border-0 bg-transparent px-0 py-0 text-sm text-ink outline-none ring-0 placeholder:text-ink-muted focus:ring-0">
        </div>

        <div class="flex items-center gap-3 text-sm">
            <button class="hidden h-10 w-10 items-center justify-center rounded-full text-ink-soft transition hover:bg-primary-50 hover:text-primary-700 md:inline-flex" aria-label="Notificaciones">
                <span class="material-symbols-outlined">notifications</span>
            </button>

            <button class="hidden h-10 w-10 items-center justify-center rounded-full text-ink-soft transition hover:bg-primary-50 hover:text-primary-700 md:inline-flex" aria-label="Carrito">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>

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
