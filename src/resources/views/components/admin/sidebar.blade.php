<aside x-cloak class="fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-[260px] border-r border-[#d8d2de] bg-white transition-all duration-300 ease-out" :class="[mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0', sidebarExpanded ? 'md:w-[260px]' : 'md:w-[88px]']">
    <div class="flex h-full flex-col overflow-y-auto px-3 py-4">
        <div class="flex items-start gap-3 rounded-[1.5rem] border border-[#e7e0f1] bg-[#f7f2fb] px-3 py-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5f4cae] text-white">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>

            <div x-show="sidebarExpanded" x-transition.opacity.duration.150ms class="min-w-0">
                <p class="font-medium text-slate-900">Portal admin</p>
                <p class="mt-1 text-xs text-slate-500">Control del ecosistema WAYNA</p>
            </div>
        </div>

        <nav class="mt-6 space-y-2">
            <x-admin.sidebar-item :href="route('dashboard.admin')" icon="dashboard" :active="request()->routeIs('dashboard.admin')" wire:navigate>
                Dashboard
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
        </nav>

        <div class="mt-auto rounded-[1.5rem] border border-[#efc2bb] bg-[#ffdad6] px-4 py-4 text-[#812914]">
            <p x-show="sidebarExpanded" x-transition.opacity.duration.150ms class="text-sm">
                La fase 1 prioriza control operativo real sin saturar el panel con modulos vacios.
            </p>
            <span x-show="! sidebarExpanded" x-transition.opacity.duration.150ms class="flex justify-center">
                <span class="material-symbols-outlined">tips_and_updates</span>
            </span>
        </div>
    </div>
</aside>
