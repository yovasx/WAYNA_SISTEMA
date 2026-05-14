<aside x-cloak
       @mouseenter="if (sidebarState === 'icons') sidebarHover = true"
       @mouseleave="sidebarHover = false"
       class="sticky top-16 h-[calc(100vh-4rem)] shrink-0 border-r border-[#d8d2de] bg-white transition-all duration-300 ease-out"
       :class="[mobileSidebarOpen ? 'translate-x-0 w-[260px]' : '-translate-x-full md:translate-x-0', sidebarEffective === 'expanded' ? 'w-[260px]' : (sidebarState === 'icons' ? 'w-[88px]' : 'w-0 overflow-hidden')]">
    <div class="flex h-full flex-col overflow-y-auto px-3 py-4">
        <div class="flex items-start gap-3 rounded-[1.5rem] border border-[#e7e0f1] bg-[#f7f2fb] px-3 py-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5f4cae] text-white">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>

            <div x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms class="min-w-0">
                <p class="font-medium text-slate-900">Portal admin</p>
                <p class="mt-1 text-xs text-slate-500">Control del ecosistema</p>
            </div>
        </div>

        <nav class="mt-6 space-y-2">
            <x-admin.sidebar-item :href="route('dashboard.admin')" icon="dashboard" :active="request()->routeIs('dashboard.admin')" wire:navigate>
                Dashboard
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.usuarios.index')" icon="group" :active="request()->routeIs('admin.usuarios.*')" wire:navigate>
                Usuarios
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.emprendedores.index')" icon="storefront" :active="request()->routeIs('admin.emprendedores.*')" wire:navigate>
                Emprendedores
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.categorias.index')" icon="category" :active="request()->routeIs('admin.categorias.*')" wire:navigate>
                Categorias
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.productos.index')" icon="inventory_2" :active="request()->routeIs('admin.productos.*')" wire:navigate>
                Productos
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.pedidos.index')" icon="shopping_bag" :active="request()->routeIs('admin.pedidos.*')" wire:navigate>
                Pedidos
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.donaciones.index')" icon="volunteer_activism" :active="request()->routeIs('admin.donaciones.*')" wire:navigate>
                Donaciones
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.transacciones.index')" icon="payments" :active="request()->routeIs('admin.transacciones.*')" wire:navigate>
                Transacciones
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.puntos-donadores.index')" icon="military_tech" :active="request()->routeIs('admin.puntos-donadores.*')" wire:navigate>
                Puntos donador
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.historial-puntos.index')" icon="history" :active="request()->routeIs('admin.historial-puntos.*')" wire:navigate>
                Historial puntos
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.insignias.index')" icon="workspace_premium" :active="request()->routeIs('admin.insignias.*')" wire:navigate>
                Insignias
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('admin.reservas.index')" icon="calendar_month" :active="request()->routeIs('admin.reservas.*')" wire:navigate>
                Reservas
            </x-admin.sidebar-item>
        </nav>

        <div class="mt-auto border-t border-[#d8d2de] pt-4">
            <div x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms>
                <button class="mb-4 w-full rounded-2xl bg-[#a03f29] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">
                    <span class="material-symbols-outlined align-middle text-[20px]">volunteer_activism</span>
                    <span class="ml-2 align-middle">Donar ahora</span>
                </button>
            </div>

            <nav class="space-y-1">
                <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-2 text-sm text-slate-600 transition hover:bg-[#f7f2fb] hover:text-[#5f4cae]" :class="sidebarEffective !== 'expanded' ? 'justify-center px-2' : ''">
                    <span class="material-symbols-outlined">settings</span>
                    <span x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms>Configuracion</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-2 text-sm text-slate-600 transition hover:bg-[#f7f2fb] hover:text-[#5f4cae]" :class="sidebarEffective !== 'expanded' ? 'justify-center px-2' : ''">
                    <span class="material-symbols-outlined">help_outline</span>
                    <span x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms>Ayuda</span>
                </a>
            </nav>
        </div>
    </div>
</aside>
