<div class="space-y-8">
    @php
        $ventasChartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $ventasChartData->pluck('label')->values()->all(),
                'datasets' => [[
                    'label' => 'Ventas',
                    'data' => $ventasChartData->pluck('ventas')->values()->all(),
                    'backgroundColor' => 'rgba(95, 76, 174, 0.88)',
                    'borderRadius' => 10,
                    'barThickness' => 28,
                    'maxBarThickness' => 28,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['display' => false],
                ],
                'scales' => [
                    'x' => [
                        'grid' => ['display' => false],
                        'ticks' => ['color' => '#64748b'],
                    ],
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => ['color' => 'rgba(216, 210, 222, 0.45)'],
                        'ticks' => ['color' => '#64748b'],
                    ],
                ],
            ],
        ];

        $pedidosEstadoChartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $pedidosPorEstado->pluck('label')->values()->all(),
                'datasets' => [[
                    'data' => $pedidosPorEstado->pluck('total')->values()->all(),
                    'backgroundColor' => [
                        'rgba(212, 163, 64, 0.92)',
                        'rgba(95, 76, 174, 0.88)',
                        'rgba(63, 122, 92, 0.88)',
                        'rgba(41, 31, 88, 0.88)',
                        'rgba(161, 78, 75, 0.88)',
                    ],
                    'borderWidth' => 0,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => ['usePointStyle' => true, 'color' => '#64748b'],
                    ],
                ],
            ],
        ];

        $topProductosChartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $topProductos->pluck('nombre')->values()->all(),
                'datasets' => [[
                    'label' => 'Ventas por producto',
                    'data' => $topProductos->pluck('ventas')->map(fn ($value) => (float) $value)->values()->all(),
                    'backgroundColor' => 'rgba(212, 163, 64, 0.88)',
                    'borderRadius' => 10,
                ]],
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['display' => false],
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        'grid' => ['color' => 'rgba(216, 210, 222, 0.45)'],
                        'ticks' => ['color' => '#64748b'],
                    ],
                    'y' => [
                        'grid' => ['display' => false],
                        'ticks' => ['color' => '#64748b'],
                    ],
                ],
            ],
        ];

        $inventarioChartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $inventarioChartData->pluck('label')->values()->all(),
                'datasets' => [[
                    'data' => $inventarioChartData->pluck('total')->values()->all(),
                    'backgroundColor' => [
                        'rgba(95, 76, 174, 0.88)',
                        'rgba(212, 163, 64, 0.92)',
                        'rgba(161, 78, 75, 0.88)',
                    ],
                    'borderWidth' => 0,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => ['usePointStyle' => true, 'color' => '#64748b'],
                    ],
                ],
            ],
        ];
    @endphp

    <x-admin.page-header eyebrow="Panel emprendedor" :title="'Bienvenido, '.$perfil->nombre_emprendimiento" description="Lee el pulso comercial del negocio con ventas, pedidos, stock y productos que realmente estan moviendo tu operacion.">
        <x-slot name="actions">
            <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="rounded-2xl bg-[#a03f29] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                Ver pedidos
            </a>
            <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                Gestionar productos
            </a>
            <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                Cuenta empresaria
            </a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="flex items-center gap-4 rounded-3xl border border-[#efc2bb] bg-[#ffdad6] px-5 py-4 text-[#93000a]">
            <span class="material-symbols-outlined">warning</span>
            <span class="text-sm">{{ $metricas['stock_bajo'] }} producto(s) con riesgo de stock y {{ $metricas['pedidos_pendientes'] }} pedido(s) pendientes por atender.</span>
        </div>
        <div class="flex items-center gap-4 rounded-3xl border border-[#d8d2de] bg-[#e6deff] px-5 py-4 text-[#4a3597]">
            <span class="material-symbols-outlined">storefront</span>
            <span class="text-sm">Tu negocio esta {{ $metricas['estado_negocio'] }}. Mantener la cuenta empresaria actualizada mejora la visibilidad y la confianza comercial.</span>
        </div>
        <div class="flex items-center gap-4 rounded-3xl border border-[#d6e6dc] bg-[#eef7f2] px-5 py-4 text-[#1f6b52]">
            <span class="material-symbols-outlined">payments</span>
            <span class="text-sm">Tu ticket promedio actual es de Bs {{ number_format((float) $metricas['ticket_promedio'], 2) }} por pedido validado.</span>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-admin.kpi-card label="Ventas del mes" :value="'Bs '.number_format($metricas['ventas_mes'], 2)" icon="payments" tone="primary" />
        <x-admin.kpi-card label="Pedidos pendientes" :value="$metricas['pedidos_pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Pedidos atendidos" :value="$metricas['pedidos_atendidos']" icon="task_alt" tone="green" />
        <x-admin.kpi-card label="Productos activos" :value="$metricas['productos']" icon="inventory_2" tone="amber" />
        <x-admin.kpi-card label="Ticket promedio" :value="'Bs '.number_format((float) $metricas['ticket_promedio'], 2)" icon="monitoring" tone="neutral" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.panel-card title="Tendencia de ventas" description="Lectura mensual de ventas validadas para entender si tu negocio esta creciendo o estancandose.">
                @if ($ventasChartData->every(fn ($item) => $item['ventas'] === 0.0))
                    <x-admin.empty-state title="Sin ventas registradas" description="Cuando existan pedidos confirmados, entregados o completados, este grafico mostrara tu tendencia comercial de los ultimos meses." icon="bar_chart" />
                @else
                    <div class="relative h-72 min-w-[420px]">
                        <canvas data-admin-chart='@json($ventasChartConfig)' class="h-full w-full"></canvas>
                    </div>
                @endif
            </x-admin.panel-card>
        </div>

        <x-admin.panel-card title="Pedidos por estado" description="Mide tu carga operativa actual y donde se concentra el trabajo pendiente.">
            @if ($pedidosPorEstado->sum('total') === 0)
                <x-admin.empty-state title="Sin pedidos para clasificar" description="Este bloque se llenara automaticamente cuando empieces a recibir pedidos reales." icon="pie_chart" />
            @else
                <div class="relative h-72">
                    <canvas data-admin-chart='@json($pedidosEstadoChartConfig)' class="h-full w-full"></canvas>
                </div>
            @endif
        </x-admin.panel-card>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-admin.panel-card title="Top productos vendidos" description="Tus productos con mayor traccion comercial segun ventas acumuladas en pedidos validos.">
            @if ($topProductos->isEmpty())
                <x-admin.empty-state title="Aun no hay productos vendidos" description="Cuando tus pedidos empiecen a cerrarse, aqui veras que piezas realmente generan mas movimiento." icon="bar_chart_4_bars" />
            @else
                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_220px] xl:items-start">
                    <div class="relative h-72 min-w-[420px]">
                        <canvas data-admin-chart='@json($topProductosChartConfig)' class="h-full w-full"></canvas>
                    </div>
                    <div class="space-y-3">
                        @foreach ($topProductos as $producto)
                            <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700">
                                <p class="font-medium text-slate-900">{{ $producto->nombre }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ (int) $producto->unidades }} unidad(es) vendidas</p>
                                <p class="mt-2 font-mono-data text-primary-600">Bs {{ number_format((float) $producto->ventas, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-admin.panel-card>

        <x-admin.panel-card title="Salud del inventario" description="Distribucion actual del stock para anticiparte a quiebres o reposiciones urgentes.">
            @if ($inventarioChartData->sum('total') === 0)
                <x-admin.empty-state title="Sin inventario cargado" description="Cuando tengas productos activos, aqui se resumira su estado de disponibilidad." icon="inventory" />
            @else
                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_220px] xl:items-start">
                    <div class="relative h-72">
                        <canvas data-admin-chart='@json($inventarioChartConfig)' class="h-full w-full"></canvas>
                    </div>
                    <div class="space-y-3">
                        @foreach ($inventarioChartData as $bloque)
                            <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700">
                                <p class="font-medium text-slate-900">{{ $bloque['label'] }}</p>
                                <p class="mt-2 font-display text-2xl text-ink">{{ $bloque['total'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-admin.panel-card>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.75fr)]">
        <x-admin.panel-card title="Pedidos recientes" description="Ultimo movimiento comercial del negocio con foco en cliente, monto y estado del pedido.">
            <x-slot name="actions">
                <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="text-sm font-medium text-primary-600 hover:underline">Ver todos</a>
            </x-slot>

            <div class="space-y-4">
                @forelse ($pedidosRecientes as $pedido)
                    <article class="rounded-3xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">{{ $pedido->codigo }}</p>
                                <h3 class="mt-2 font-display text-xl text-slate-900">{{ $pedido->usuario?->nombre_completo ?? 'Cliente sin nombre' }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $pedido->notas ?: 'Sin notas del cliente para este pedido.' }}</p>
                            </div>
                            <div class="text-right md:min-w-[180px]">
                                <p class="font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $pedido->total, 2) }}</p>
                                <div class="mt-2 flex justify-end">
                                    <x-admin.status-badge :tone="match($pedido->estado) { 'cancelado' => 'red', 'pendiente' => 'amber', 'confirmado', 'entregado', 'completado' => 'green', default => 'gray' }">
                                        {{ $pedido->estado }}
                                    </x-admin.status-badge>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-[#d8d2de] px-6 py-10 text-center text-slate-500">
                        Todavia no hay pedidos recientes para mostrar.
                    </div>
                @endforelse
            </div>
        </x-admin.panel-card>

        <aside class="space-y-6">
            <x-admin.panel-card title="Acciones rapidas" description="Accesos directos para seguir operando sin salir de la vista principal.">
                <div class="space-y-3">
                    <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Atender pedidos</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Administrar productos</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Cuenta empresaria</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('emprendedor.cuenta.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Mi cuenta</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Productos con atencion inmediata" description="Inventario que puede afectar ventas o experiencia del cliente si no lo corriges a tiempo.">
                <div class="space-y-3">
                    @forelse ($productosStockBajo as $producto)
                        <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $producto->nombre }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $producto->categoria?->nombre ?? 'Sin categoria' }}</p>
                                </div>
                                <p class="font-mono-data text-[#a03f29]">Stock {{ $producto->stock }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-[#f7f2fb] px-4 py-4 text-sm text-slate-600">
                            No hay productos con urgencia de stock en este momento.
                        </div>
                    @endforelse
                </div>
            </x-admin.panel-card>
        </aside>
    </div>
</div>
