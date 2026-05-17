<div class="space-y-8">
    <x-admin.page-header eyebrow="Impacto social" title="Donaciones" description="Registro de donaciones realizadas por la comunidad al ecosistema artesanal." />

    <div class="grid gap-4 md:grid-cols-2">
        <x-admin.kpi-card label="Total donado" :value="'Bs '.number_format($totalMonto, 2)" icon="volunteer_activism" tone="primary" />
        <x-admin.kpi-card label="Donaciones" :value="$totalDonaciones" icon="favorite" tone="secondary" />
    </div>

    <x-admin.panel-card title="Historial de donaciones" description="Busca por donante para revisar el impacto generado.">
        <x-slot name="actions">
            <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar donante" class="wayna-input lg:w-80">
        </x-slot>

        @if ($donaciones->isEmpty())
            <x-admin.empty-state title="Sin donaciones registradas" description="Cuando los donantes hagan sus primeras contribuciones, apareceran aqui con su monto y destino." icon="volunteer_activism" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Donante</th>
                            <th class="wayna-table-th">Emprendedor</th>
                            <th class="wayna-table-th">Monto</th>
                            <th class="wayna-table-th">Anonima</th>
                            <th class="wayna-table-th">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donaciones as $donacion)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td text-ink">{{ $donacion->usuario?->nombre_completo ?? 'Anonimo' }}</td>
                                <td class="wayna-table-td">{{ $donacion->emprendedor?->nombre_negocio ?? 'Fondo general' }}</td>
                                <td class="wayna-table-td font-mono-data text-primary-600">Bs {{ number_format((float) $donacion->monto, 2) }}</td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$donacion->es_anonima ? 'gray' : 'green'">{{ $donacion->es_anonima ? 'Si' : 'No' }}</x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($donacion->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $donaciones->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </x-admin.panel-card>
</div>
