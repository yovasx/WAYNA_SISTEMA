<div class="space-y-8">
    <x-admin.page-header eyebrow="Panel emprendedor" :title="'Bienvenido, '.$perfil->nombre_emprendimiento" description="Controla el pulso de tu negocio, revisa tus productos activos y prepara el perfil de tu taller para la siguiente fase.">
        <x-slot name="actions">
            <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                Gestionar productos
            </a>
            <a href="{{ route('profile') }}" class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                Mi cuenta
            </a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="flex items-center gap-4 rounded-3xl border border-[#efc2bb] bg-[#ffdad6] px-5 py-4 text-[#93000a]">
            <span class="material-symbols-outlined">warning</span>
            <span class="text-sm">{{ $metricas['stock_bajo'] }} producto(s) con stock critico o ultimas unidades.</span>
        </div>
        <div class="flex items-center gap-4 rounded-3xl border border-[#d8d2de] bg-[#e6deff] px-5 py-4 text-[#4a3597]">
            <span class="material-symbols-outlined">info</span>
            <span class="text-sm">Tu perfil emprendedor esta {{ $perfil->estado_aprobacion }} y listo para crecer con categorias y productos.</span>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Ventas referenciales</p>
            <p class="mt-4 font-display text-3xl text-[#5f4cae]">Bs {{ number_format($metricas['valor_catalogo'], 2) }}</p>
            <p class="mt-3 text-xs text-slate-500">Suma del valor actual publicado en tu catalogo.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Productos activos</p>
            <p class="mt-4 font-display text-3xl text-[#a03f29]">{{ $metricas['productos'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Piezas listas para consulta o compra.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Inventario total</p>
            <p class="mt-4 font-display text-3xl text-slate-900">{{ $metricas['inventario_total'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Unidades acumuladas en tu panel actual.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Stock bajo</p>
            <p class="mt-4 font-display text-3xl text-[#745800]">{{ $metricas['stock_bajo'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Productos que necesitan reposicion pronta.</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,0.8fr)] 2xl:grid-cols-[minmax(0,1.7fr)_minmax(340px,0.8fr)]">
            <x-admin.panel-card title="Ultimos movimientos del taller" description="Resumen rapido de los productos mas recientes dentro de tu catalogo.">
                <x-slot name="actions">
                    <span class="rounded-full bg-[#f7f2fb] px-3 py-1 text-xs font-medium text-[#5f4cae]">Livewire</span>
                </x-slot>

                <div class="space-y-4">
                    @forelse ($productos as $producto)
                        <article class="rounded-3xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">{{ $producto->categoria?->nombre ?? 'Sin categoria' }}</p>
                                    <h3 class="mt-2 font-display text-xl text-slate-900">{{ $producto->nombre }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $producto->descripcion ?: 'Producto listo para ser enriquecido con mas historia visual.' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $producto->precio, 2) }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Stock: {{ $producto->stock }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-[#d8d2de] px-6 py-10 text-center text-slate-500">
                            Todavia no hay movimientos recientes en tu catalogo.
                        </div>
                    @endforelse
                </div>
            </x-admin.panel-card>

            <aside class="space-y-6">
                <x-admin.panel-card title="Perfil de taller" description="Estado actual del negocio dentro del ecosistema WAYNA.">
                    <h2 class="font-display text-2xl text-slate-900">{{ $perfil->nombre_emprendimiento }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $perfil->historia ?: 'Tu perfil ya esta listo para editarse y contar mejor el origen de cada pieza.' }}</p>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <div class="rounded-2xl bg-[#f7f2fb] px-4 py-3">Descripcion: {{ $perfil->descripcion ?: 'Por definir' }}</div>
                        <div class="rounded-2xl bg-[#f7f2fb] px-4 py-3">NIT: {{ $perfil->nit ?: 'No registrado' }}</div>
                        <div class="rounded-2xl bg-[#f7f2fb] px-4 py-3">Aprobacion: {{ ucfirst($perfil->estado_aprobacion) }}</div>
                    </div>
                </x-admin.panel-card>

                <x-admin.panel-card title="Siguiente fase" description="La siguiente iteracion separara perfil del negocio, video por URL y gestion de pedidos en secciones propias.">
                    <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="inline-flex rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">
                        Ir a productos
                    </a>
                </x-admin.panel-card>
            </aside>
        </div>
</div>
