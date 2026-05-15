<div class="space-y-8">
    <x-admin.page-header eyebrow="Trazabilidad social" title="Historial de puntos" description="Audita como se asignaron puntos por cada donacion registrada en la plataforma." />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Registros" :value="$resumen['registros']" icon="history" tone="primary" />
        <x-admin.kpi-card label="Puntos otorgados" :value="$resumen['puntos_otorgados']" icon="star" tone="secondary" />
        <x-admin.kpi-card label="Multiplicados" :value="$resumen['multiplicados']" icon="trending_up" tone="tertiary" />
        <x-admin.kpi-card label="Donaciones" :value="$resumen['donaciones_vinculadas']" icon="volunteer_activism" tone="neutral" />
    </div>

    <x-admin.panel-card title="Bitacora de puntos" description="Busca por donador o ID de donacion para revisar la asignacion exacta.">
        <x-slot name="actions">
            <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar donador o ID de donacion" class="wayna-input lg:w-80">
        </x-slot>

        @if ($historial->isEmpty())
            <x-admin.empty-state title="Sin historial disponible" description="Cuando una donacion genere puntos, el detalle quedara registrado aqui con su multiplicador." icon="history" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Donador</th>
                            <th class="wayna-table-th">Donacion</th>
                            <th class="wayna-table-th">Puntos</th>
                            <th class="wayna-table-th">Multiplicador</th>
                            <th class="wayna-table-th">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $registro)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td">
                                    <p class="text-sm text-ink">{{ $registro->usuario?->nombre_completo ?? 'Usuario no disponible' }}</p>
                                    <p class="mt-1 text-xs text-ink-muted">{{ $registro->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="wayna-table-td">
                                    <p class="font-mono-data text-sm text-primary-600">#{{ $registro->donacion_id }}</p>
                                    <p class="mt-1 text-sm text-ink-soft">Bs {{ number_format((float) ($registro->donacion?->monto ?? 0), 2) }} · {{ $registro->donacion?->emprendedor?->nombre_negocio ?? 'Fondo general' }}</p>
                                </td>
                                <td class="wayna-table-td font-mono-data text-ink">{{ $registro->puntos_ganados }}</td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$registro->multiplicador > 1 ? 'green' : 'gray'">
                                        x{{ number_format((float) $registro->multiplicador, 1) }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($registro->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $historial->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
