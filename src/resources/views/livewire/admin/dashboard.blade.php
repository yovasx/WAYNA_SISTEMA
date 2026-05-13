<div class="space-y-8">
    <x-admin.page-header eyebrow="Portal admin" title="Dashboard administrador" description="Resumen operativo real de usuarios, emprendedores, categorias y productos dentro de WAYNA.">
        <x-slot name="actions">
            <a href="{{ route('admin.emprendedores.index') }}" wire:navigate class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Revisar emprendedores</a>
            <a href="{{ route('admin.productos.index') }}" wire:navigate class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Gestionar productos</a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <x-admin.kpi-card label="Usuarios activos" :value="$metricas['usuarios_activos']" icon="groups" tone="primary" />
        <x-admin.kpi-card label="Pendientes" :value="$metricas['emprendedores_pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Aprobados" :value="$metricas['emprendedores_aprobados']" icon="verified" tone="tertiary" />
        <x-admin.kpi-card label="Categorias" :value="$metricas['categorias_activas']" icon="category" tone="neutral" />
        <x-admin.kpi-card label="Productos activos" :value="$metricas['productos_totales']" icon="inventory_2" tone="primary" />
        <x-admin.kpi-card label="Stock critico" :value="$metricas['stock_critico']" icon="warning" tone="secondary" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <x-admin.panel-card title="Ultimos productos" description="Vista rapida de las ultimas publicaciones y su estado comercial.">
            @if ($ultimosProductos->isEmpty())
                <x-admin.empty-state title="Aun no hay productos" description="Cuando se creen o importen productos, esta mesa mostrara el pulso operativo del catalogo." icon="inventory_2" />
            @else
                <div class="space-y-4">
                    @foreach ($ultimosProductos as $producto)
                        <article class="rounded-3xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">{{ $producto->categoria?->nombre ?? 'Sin categoria' }}</p>
                                    <h3 class="mt-2 font-medium text-slate-900">{{ $producto->nombre }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $producto->perfilEmprendedor?->nombre_emprendimiento ?? 'Sin emprendedor asignado' }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <x-admin.status-badge :tone="$producto->estado_disponibilidad === 'agotado' ? 'red' : ($producto->estado_disponibilidad === 'ultimas_unidades' ? 'amber' : 'violet')">
                                        {{ str_replace('_', ' ', $producto->estado_disponibilidad) }}
                                    </x-admin.status-badge>
                                    <x-admin.status-badge :tone="$producto->activo ? 'green' : 'gray'">{{ $producto->activo ? 'Activo' : 'Inactivo' }}</x-admin.status-badge>
                                    <span class="font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $producto->precio, 2) }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </x-admin.panel-card>

        <div class="space-y-6">
            <x-admin.panel-card title="Estados de emprendedores" description="Distribucion actual del flujo de aprobacion.">
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-2xl bg-[#fcfbfe] px-4 py-3">
                        <span class="text-sm text-slate-600">Pendientes</span>
                        <x-admin.status-badge tone="amber">{{ $estadoEmprendedores['pendiente'] ?? 0 }}</x-admin.status-badge>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-[#fcfbfe] px-4 py-3">
                        <span class="text-sm text-slate-600">Aprobados</span>
                        <x-admin.status-badge tone="green">{{ $estadoEmprendedores['activo'] ?? 0 }}</x-admin.status-badge>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-[#fcfbfe] px-4 py-3">
                        <span class="text-sm text-slate-600">Suspendidos</span>
                        <x-admin.status-badge tone="red">{{ $estadoEmprendedores['suspendido'] ?? 0 }}</x-admin.status-badge>
                    </div>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Categorias con mas productos" description="Prioriza donde hay mas volumen para moderacion y curaduria.">
                @if ($topCategorias->isEmpty())
                    <x-admin.empty-state title="Sin categorias" description="El admin podra crear categorias aqui mismo en cuanto se necesiten nuevas familias de productos." icon="category" class="px-4 py-8" />
                @else
                    <div class="space-y-3">
                        @foreach ($topCategorias as $categoria)
                            <div class="flex items-center justify-between rounded-2xl bg-[#fcfbfe] px-4 py-3">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $categoria->nombre }}</p>
                                    <p class="text-xs text-slate-500">{{ $categoria->productos_count }} producto(s)</p>
                                </div>
                                <span class="font-mono-data text-sm text-[#5f4cae]">{{ $categoria->productos_count }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.panel-card>
        </div>
    </div>

    <x-admin.panel-card title="Emprendedores recientes" description="Altas mas recientes para seguimiento del equipo admin.">
        @if ($ultimosEmprendedores->isEmpty())
            <x-admin.empty-state title="No hay emprendedores registrados" description="Cuando entren nuevas cuentas emprendedoras, este tablero mostrara el historial mas reciente." icon="storefront" />
        @else
            <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($ultimosEmprendedores as $perfil)
                    <article class="rounded-3xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $perfil->nombre_emprendimiento }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $perfil->usuario?->name ?? 'Sin usuario asociado' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $perfil->categoria?->nombre ?? 'Sin categoria' }}</p>
                            </div>

                            <x-admin.status-badge :tone="$perfil->estado_aprobacion === 'aprobado' ? 'green' : ($perfil->estado_aprobacion === 'pendiente' ? 'amber' : 'red')">
                                {{ $perfil->estado_aprobacion }}
                            </x-admin.status-badge>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </x-admin.panel-card>
</div>
