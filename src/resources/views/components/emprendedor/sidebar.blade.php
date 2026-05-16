<aside x-cloak
       @mouseenter="handleSidebarEnter()"
       @mouseleave="handleSidebarLeave()"
       class="fixed inset-y-0 left-0 z-40 w-[260px] overflow-hidden border-r border-stroke bg-surface-raised pt-20 transition-transform duration-300 ease-out md:static md:z-auto md:w-auto md:pt-0 md:transition-none"
       :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
    <div class="flex h-full flex-col overflow-hidden px-3 py-4 md:py-6">
        <div class="flex items-start gap-3 rounded-[1.5rem] border border-stroke-soft bg-primary-50/70 px-3 py-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-500 text-white shadow-wayna">
                <span class="material-symbols-outlined">storefront</span>
            </div>

            <div x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms class="min-w-0">
                <p class="font-medium text-ink">Panel emprendedor</p>
                <p class="mt-1 text-xs text-ink-muted">Gestiona tu negocio</p>
            </div>
        </div>

        <nav class="mt-6 flex-1 space-y-2">
            <x-admin.sidebar-item :href="route('dashboard.emprendedor')" icon="dashboard" :active="request()->routeIs('dashboard.emprendedor')" wire:navigate>
                Dashboard
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('emprendedor.pedidos.index')" icon="receipt_long" :active="request()->routeIs('emprendedor.pedidos.*')" wire:navigate>
                Pedidos
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('emprendedor.productos.index')" icon="inventory_2" :active="request()->routeIs('emprendedor.productos.*')" wire:navigate>
                Productos
            </x-admin.sidebar-item>

            <x-admin.sidebar-item :href="route('emprendedor.perfil.index')" icon="store" :active="request()->routeIs('emprendedor.perfil.*')" wire:navigate>
                Perfil del negocio
            </x-admin.sidebar-item>
        </nav>

        <div class="mt-6 border-t border-stroke pt-4">
            <div x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms class="rounded-[1.5rem] border border-accent-100 bg-accent-50 px-4 py-4 text-sm text-accent-700 shadow-wayna-soft">
                Tu panel ahora conecta pedidos, catalogo y perfil del negocio para operar tu emprendimiento desde un mismo espacio.
            </div>
        </div>
    </div>
</aside>
