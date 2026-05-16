<div class="space-y-8">
    <x-admin.page-header eyebrow="Panel emprendedor" :title="'Bienvenido, '.$perfil->nombre_emprendimiento" description="Controla el pulso de tu negocio, revisa tus productos activos y manten separado el trabajo operativo del perfil del negocio.">
        <x-slot name="actions">
            <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                Gestionar productos
            </a>
            <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                Perfil del negocio
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
            <x-admin.panel-card title="Acciones rapidas" description="Mantiene separado el trabajo operativo del cuidado del perfil del negocio.">
                <div class="space-y-3">
                    <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Administrar productos</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>

                    <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Editar perfil del negocio</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Lectura del panel" description="El dashboard ahora queda reservado para datos operativos del catalogo y accesos rapidos.">
                <div class="rounded-2xl bg-[#f7f2fb] px-4 py-4 text-sm text-slate-600">
                    La historia, imagenes, logo, video y redes del emprendimiento ahora se gestionan desde la seccion propia de perfil.
                </div>
            </x-admin.panel-card>
        </aside>
    </div>
</div>
