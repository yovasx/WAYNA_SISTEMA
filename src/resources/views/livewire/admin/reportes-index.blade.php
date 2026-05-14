<div class="space-y-8">
    <x-admin.page-header eyebrow="Analitica de negocio" title="Reportes" description="Sigue ventas, donaciones e ingreso de plataforma por periodo con una lectura ejecutiva y operativa.">
        <x-slot name="actions">
            <a
                href="{{ route('admin.reportes.export', ['preset' => $preset, 'desde' => $desde ?: null, 'hasta' => $hasta ?: null]) }}"
                class="inline-flex items-center gap-2 rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]"
            >
                <span class="material-symbols-outlined text-[20px]">download</span>
                <span>Exportar CSV</span>
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.panel-card title="Controles de periodo" description="Funciona como una lectura tipo mercado: cambia de rango rapido o define un rango manual.">
        <div class="space-y-4">
            <div class="flex flex-wrap gap-2">
                @foreach ($presets as $presetKey => $presetLabel)
                    <button
                        type="button"
                        wire:click="aplicarPreset('{{ $presetKey }}')"
                        class="rounded-full border px-4 py-2 text-sm font-medium transition {{ $preset === $presetKey ? 'border-[#5f4cae] bg-[#f7f2fb] text-[#5f4cae]' : 'border-[#d8d2de] bg-white text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]' }}"
                    >
                        {{ $presetLabel }}
                    </button>
                @endforeach
            </div>

            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                <input wire:model.live="desde" type="date" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                <input wire:model.live="hasta" type="date" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                <div class="flex flex-wrap gap-2">
                    @foreach (['todo' => 'Todo', 'ventas' => 'Ventas', 'donaciones' => 'Donaciones', 'ingreso' => 'Ingreso plataforma'] as $serieKey => $serieLabel)
                        <button
                            type="button"
                            wire:click="$set('serie', '{{ $serieKey }}')"
                            class="rounded-full border px-4 py-2 text-sm font-medium transition {{ $serie === $serieKey ? 'border-[#1f6b52] bg-[#eef7f2] text-[#1f6b52]' : 'border-[#d8d2de] bg-white text-slate-600 hover:border-[#1f6b52] hover:text-[#1f6b52]' }}"
                        >
                            {{ $serieLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </x-admin.panel-card>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <x-admin.kpi-card label="Ventas brutas" :value="'Bs '.number_format($reporte['kpis']['ventas_brutas'], 2)" icon="shopping_bag" tone="primary" />
        <x-admin.kpi-card label="Donaciones" :value="'Bs '.number_format($reporte['kpis']['donaciones'], 2)" icon="volunteer_activism" tone="secondary" />
        <x-admin.kpi-card label="Ingreso plataforma" :value="'Bs '.number_format($reporte['kpis']['ingreso_plataforma'], 2)" icon="trending_up" tone="green" helper="Estimado" />
        <x-admin.kpi-card label="Flujo total" :value="'Bs '.number_format($reporte['kpis']['flujo_total'], 2)" icon="payments" tone="tertiary" />
        <x-admin.kpi-card label="Ticket promedio" :value="'Bs '.number_format($reporte['kpis']['ticket_promedio'], 2)" icon="receipt_long" tone="neutral" />
        <x-admin.kpi-card label="Tasa de exito" :value="$reporte['kpis']['tasa_exito'].'%'" icon="verified" tone="green" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(320px,0.8fr)]">
        <x-admin.panel-card title="Serie principal" description="Comparativa temporal de negocio segun el rango seleccionado.">
            @if ($reporte['series']->isEmpty())
                <x-admin.empty-state title="Sin datos para este periodo" description="Ajusta el rango o espera nuevos movimientos para construir la serie historica." icon="query_stats" />
            @else
                <div class="overflow-x-auto">
                    <div class="min-w-[760px]">
                        <div class="mb-5 flex flex-wrap gap-4 text-sm">
                            @if ($serie === 'todo' || $serie === 'ventas')
                                <div class="flex items-center gap-2 text-slate-600">
                                    <span class="h-3 w-3 rounded-full bg-[#5f4cae]"></span>
                                    <span>Ventas brutas</span>
                                </div>
                            @endif

                            @if ($serie === 'todo' || $serie === 'donaciones')
                                <div class="flex items-center gap-2 text-slate-600">
                                    <span class="h-3 w-3 rounded-full bg-[#a03f29]"></span>
                                    <span>Donaciones</span>
                                </div>
                            @endif

                            @if ($serie === 'todo' || $serie === 'ingreso')
                                <div class="flex items-center gap-2 text-slate-600">
                                    <span class="h-3 w-3 rounded-full bg-[#1f6b52]"></span>
                                    <span>Ingreso plataforma</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex h-80 items-end gap-4 border-b border-l border-[#d8d2de] pb-3 pl-3">
                            @foreach ($reporte['series'] as $fila)
                                <div class="flex flex-1 flex-col items-center gap-2">
                                    <div class="flex h-[240px] w-full items-end justify-center gap-2">
                                        @if ($serie === 'todo' || $serie === 'donaciones')
                                            <div
                                                class="w-[22px] shrink-0 rounded-t-md bg-[#a03f29]/70 transition hover:bg-[#a03f29]"
                                                style="height: {{ $fila['donaciones'] > 0 ? max(($fila['donaciones'] / $chartMax) * 210, 10) : 0 }}px;"
                                                title="Donaciones: Bs {{ number_format($fila['donaciones'], 2) }}"
                                            ></div>
                                        @endif

                                        @if ($serie === 'todo' || $serie === 'ventas')
                                            <div
                                                class="w-[22px] shrink-0 rounded-t-md bg-[#5f4cae]/80 transition hover:bg-[#5f4cae]"
                                                style="height: {{ $fila['ventas'] > 0 ? max(($fila['ventas'] / $chartMax) * 210, 10) : 0 }}px;"
                                                title="Ventas: Bs {{ number_format($fila['ventas'], 2) }}"
                                            ></div>
                                        @endif

                                        @if ($serie === 'todo' || $serie === 'ingreso')
                                            <div
                                                class="w-[22px] shrink-0 rounded-t-md bg-[#1f6b52]/80 transition hover:bg-[#1f6b52]"
                                                style="height: {{ $fila['ingreso'] > 0 ? max(($fila['ingreso'] / $chartMax) * 210, 10) : 0 }}px;"
                                                title="Ingreso plataforma: Bs {{ number_format($fila['ingreso'], 2) }}"
                                            ></div>
                                        @endif
                                    </div>

                                    <span class="text-[10px] font-medium text-slate-500">{{ $fila['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </x-admin.panel-card>

        <div class="space-y-6">
            <x-admin.panel-card title="Contexto del periodo" description="Lectura rapida del rango elegido y de la salud operativa de cobros.">
                <div class="space-y-4">
                    <div class="rounded-2xl bg-[#fcfbfe] px-4 py-4">
                        <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Rango activo</p>
                        <p class="mt-2 text-sm font-medium text-slate-900">{{ $reporte['rango']['desde']->format('d/m/Y') }} - {{ $reporte['rango']['hasta']->format('d/m/Y') }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#fcfbfe] px-4 py-4">
                        <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Pedidos validos</p>
                        <p class="mt-2 text-2xl font-display text-[#5f4cae]">{{ $reporte['kpis']['pedidos'] }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#fcfbfe] px-4 py-4">
                        <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Transacciones completadas</p>
                        <p class="mt-2 text-2xl font-display text-[#1f6b52]">{{ $reporte['kpis']['transacciones_completadas'] }}</p>
                    </div>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Metodos de pago" description="Distribucion actual de la operacion financiera.">
                <div class="space-y-3">
                    @forelse ($reporte['metodos_pago'] as $metodo)
                        <div class="flex items-center justify-between rounded-2xl bg-[#fcfbfe] px-4 py-3 text-sm">
                            <span class="text-slate-700">{{ str_replace('_', ' ', $metodo->metodo_pago) }}</span>
                            <span class="font-mono-data text-[#5f4cae]">{{ $metodo->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin metodos registrados en este rango.</p>
                    @endforelse
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Estados de transaccion" description="Mide calidad de procesamiento en el mismo periodo.">
                <div class="space-y-3">
                    @forelse ($reporte['estados_transaccion'] as $estado)
                        <div class="flex items-center justify-between rounded-2xl bg-[#fcfbfe] px-4 py-3 text-sm">
                            <span class="text-slate-700">{{ ucfirst($estado->estado) }}</span>
                            <x-admin.status-badge :tone="$estado->estado === 'completada' ? 'green' : ($estado->estado === 'pendiente' ? 'amber' : ($estado->estado === 'reembolsada' ? 'gray' : 'red'))">
                                {{ $estado->total }}
                            </x-admin.status-badge>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin transacciones para este rango.</p>
                    @endforelse
                </div>
            </x-admin.panel-card>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.panel-card title="Resumen por periodo" description="Tabla util para exportar y leer la evolucion de cada serie sin perder detalle.">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                                <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Periodo</th>
                                <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Ventas</th>
                                <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Donaciones</th>
                                <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Ingreso plataforma</th>
                                <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Flujo total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reporte['series'] as $fila)
                                <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                    <td class="px-4 py-4 text-sm font-medium text-slate-900">{{ $fila['label'] }}</td>
                                    <td class="px-4 py-4 font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format($fila['ventas'], 2) }}</td>
                                    <td class="px-4 py-4 font-mono-data text-sm text-[#a03f29]">Bs {{ number_format($fila['donaciones'], 2) }}</td>
                                    <td class="px-4 py-4 font-mono-data text-sm text-[#1f6b52]">Bs {{ number_format($fila['ingreso'], 2) }}</td>
                                    <td class="px-4 py-4 font-mono-data text-sm text-slate-900">Bs {{ number_format($fila['flujo'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.panel-card>
        </div>

        <div class="space-y-6">
            <x-admin.panel-card title="Top emprendedores" description="Quienes capturan mas ventas dentro del rango.">
                <div class="space-y-3">
                    @forelse ($reporte['top_emprendedores'] as $emprendedor)
                        <article class="rounded-2xl bg-[#fcfbfe] px-4 py-4">
                            <p class="font-medium text-slate-900">{{ $emprendedor->nombre_negocio }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $emprendedor->responsable }}</p>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-slate-500">{{ $emprendedor->pedidos }} pedido(s)</span>
                                <span class="font-mono-data text-[#5f4cae]">Bs {{ number_format((float) $emprendedor->ventas, 2) }}</span>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">Sin ventas asociadas en este rango.</p>
                    @endforelse
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Top productos" description="Piezas con mayor traccion comercial en el periodo elegido.">
                <div class="space-y-3">
                    @forelse ($reporte['top_productos'] as $producto)
                        <article class="rounded-2xl bg-[#fcfbfe] px-4 py-4">
                            <p class="font-medium text-slate-900">{{ $producto->nombre }}</p>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-slate-500">{{ $producto->unidades }} unidad(es)</span>
                                <span class="font-mono-data text-[#5f4cae]">Bs {{ number_format((float) $producto->ventas, 2) }}</span>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">Sin productos vendidos en este rango.</p>
                    @endforelse
                </div>
            </x-admin.panel-card>
        </div>
    </div>
</div>
