<div class="space-y-8">
    <x-admin.page-header eyebrow="Portal admin" title="Dashboard administrador" description="Resumen operativo real de usuarios, emprendedores, categorias y productos dentro de WAYNA.">
        <x-slot name="actions">
            <a href="{{ route('admin.emprendedores.index') }}" wire:navigate class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Revisar emprendedores</a>
            <a href="{{ route('admin.productos.index') }}" wire:navigate class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Gestionar productos</a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.panel-card title="Resumen operativo" description="Pulso directo del ecosistema para seguimiento comercial, comunidad y actividad diaria.">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
            <x-admin.kpi-card label="Emprendedores activos" :value="$metricas['emprendedores_activos']" icon="storefront" tone="primary" />
            <x-admin.kpi-card label="Ventas del mes" :value="'Bs '.number_format($metricas['ventas_mes'], 2)" icon="shopping_bag" tone="secondary" />
            <x-admin.kpi-card label="Donaciones del mes" :value="'Bs '.number_format($metricas['donaciones_mes'], 2)" icon="volunteer_activism" tone="tertiary" />
            <x-admin.kpi-card label="Usuarios registrados" :value="$metricas['usuarios_registrados']" icon="groups" tone="neutral" />
            <x-admin.kpi-card label="Reservas activas" :value="$metricas['reservas_activas']" icon="calendar_month" tone="primary" />
            <x-admin.kpi-card label="Transacciones hoy" :value="$metricas['transacciones_hoy']" icon="payments" tone="secondary" />
        </div>
    </x-admin.panel-card>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.panel-card title="Tendencia de ventas vs donaciones" description="Comparativa de los ultimos 6 meses con datos reales del sistema.">
                <div class="grid gap-6 xl:grid-cols-[220px_minmax(0,1fr)] xl:items-start">
                    <div class="rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Guia visual</p>
                        <div class="mt-5 space-y-4">
                            <div class="flex items-start gap-3 rounded-2xl border border-[#ebe6ef] bg-white px-4 py-3">
                                <span class="mt-1 inline-block h-3 w-3 shrink-0 rounded-full bg-[#5f4cae]"></span>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Ventas</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Movimiento comercial confirmado del marketplace por mes.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl border border-[#ebe6ef] bg-white px-4 py-3">
                                <span class="mt-1 inline-block h-3 w-3 shrink-0 rounded-full bg-[#a03f29]"></span>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Donaciones</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Aporte solidario registrado dentro del mismo periodo.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl bg-white px-4 py-3 text-xs leading-5 text-slate-500">
                            Las barras comparan los ultimos 6 meses y te ayudan a leer rapido donde crece el flujo comercial frente al impacto social.
                        </div>
                    </div>

                    @if ($chartData->isEmpty() || $chartData->every(fn ($item) => $item['ventas'] === 0 && $item['donaciones'] === 0))
                        <x-admin.empty-state title="Sin datos suficientes" description="Cuando existan pedidos y donaciones registrados, este grafico mostrara la tendencia real del ecosistema." icon="bar_chart" />
                    @else
                        <div class="relative overflow-x-auto">
                            <div class="min-w-[420px]">
                                <div class="relative flex h-64 items-end gap-4 border-b border-l border-[#d8d2de] pb-2 pl-2">
                                    @foreach ($chartData as $item)
                                        <div class="flex flex-1 flex-col items-center gap-1">
                                            <div class="flex w-full flex-col items-center justify-end gap-0.5" style="height: 220px;">
                                                <div class="w-full max-w-[32px] rounded-t-sm bg-[#a03f29]/20 transition-all hover:bg-[#a03f29]/40"
                                                     style="height: {{ max(($item['donaciones'] / $maxValor) * 180, 2) }}px;"
                                                     title="Donaciones: Bs {{ number_format($item['donaciones'], 2) }}">
                                                </div>
                                                <div class="w-full max-w-[32px] rounded-t-sm bg-[#5f4cae]/20 transition-all hover:bg-[#5f4cae]/40"
                                                     style="height: {{ max(($item['ventas'] / $maxValor) * 180, 2) }}px;"
                                                     title="Ventas: Bs {{ number_format($item['ventas'], 2) }}">
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-medium text-slate-500">{{ $item['mes'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.panel-card>
        </div>

        <div class="space-y-6">
            <x-admin.panel-card title="Impacto del ecosistema" description="Indicadores clave del estado actual del sistema.">
                <div class="space-y-5">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Emprendedores aprobados</span>
                            <span class="font-mono-data text-xs text-[#5f4cae]">{{ $porcentajeAprobados }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-[#e6e1ea]">
                            <div class="h-full rounded-full bg-[#5f4cae] transition-all" style="width: {{ $porcentajeAprobados }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Productos activos</span>
                            <span class="font-mono-data text-xs text-[#a03f29]">{{ $porcentajeProductosActivos }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-[#e6e1ea]">
                            <div class="h-full rounded-full bg-[#a03f29] transition-all" style="width: {{ $porcentajeProductosActivos }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Categorias con productos</span>
                            <span class="font-mono-data text-xs text-[#745800]">{{ $porcentajeCategoriasConProductos }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-[#e6e1ea]">
                            <div class="h-full rounded-full bg-[#745800] transition-all" style="width: {{ $porcentajeCategoriasConProductos }}%"></div>
                        </div>
                    </div>
                </div>
            </x-admin.panel-card>

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
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
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

        <x-admin.panel-card title="Emprendedores recientes" description="Altas mas recientes para seguimiento del equipo admin.">
            @if ($ultimosEmprendedores->isEmpty())
                <x-admin.empty-state title="No hay emprendedores registrados" description="Cuando entren nuevas cuentas emprendedoras, este tablero mostrara el historial mas reciente." icon="storefront" />
            @else
                <div class="grid gap-4 sm:grid-cols-2">
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
</div>
