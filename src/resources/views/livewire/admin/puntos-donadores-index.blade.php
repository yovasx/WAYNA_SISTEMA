<div class="space-y-8">
    <x-admin.page-header eyebrow="Comunidad donadora" title="Puntos donador" description="Ranking operativo de puntos acumulados por donaciones y actividad solidaria." />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Registros" :value="$resumen['totales']" icon="military_tech" tone="primary" />
        <x-admin.kpi-card label="Puntos" :value="$resumen['puntos']" icon="star" tone="secondary" />
        <x-admin.kpi-card label="Nivel oro" :value="$resumen['oro']" icon="workspace_premium" tone="tertiary" />
        <x-admin.kpi-card label="Top score" :value="$resumen['maximo']" icon="emoji_events" tone="neutral" />
    </div>

    <x-admin.panel-card title="Ranking de donadores" description="Consulta puntaje, nivel y ultima actualizacion por usuario.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar donador" class="wayna-input lg:w-80">

                <select wire:model.live="filtroNivel" class="wayna-select">
                    <option value="">Todos los niveles</option>
                    <option value="bronce">Bronce</option>
                    <option value="plata">Plata</option>
                    <option value="oro">Oro</option>
                </select>
            </div>
        </x-slot>

        @if ($puntosDonadores->isEmpty())
            <x-admin.empty-state title="Sin ranking disponible" description="Cuando existan puntos acumulados por donacion, apareceran aqui con el nivel correspondiente." icon="military_tech" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Posicion</th>
                            <th class="wayna-table-th">Donador</th>
                            <th class="wayna-table-th">Nivel</th>
                            <th class="wayna-table-th">Puntos</th>
                            <th class="wayna-table-th">Actualizado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($puntosDonadores as $registro)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td font-mono-data text-primary-600">{{ ($puntosDonadores->firstItem() ?? 1) + $loop->index }}</td>
                                <td class="wayna-table-td">
                                    <p class="text-sm text-ink">{{ $registro->usuario?->nombre_completo ?? 'Usuario no disponible' }}</p>
                                    <p class="mt-1 text-xs text-ink-muted">{{ $registro->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$registro->nivel === 'oro' ? 'green' : ($registro->nivel === 'plata' ? 'gray' : 'amber')">
                                        {{ $registro->nivel }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td font-mono-data text-ink">{{ $registro->puntos_total }}</td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($registro->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $puntosDonadores->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </x-admin.panel-card>
</div>
