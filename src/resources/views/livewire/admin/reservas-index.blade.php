<div class="space-y-8">
    <x-admin.page-header eyebrow="Experiencias" title="Reservas" description="Reservas de productos, talleres y experiencias culturales." />

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="calendar_month" tone="primary" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Confirmadas" :value="$resumen['confirmadas']" icon="check_circle" tone="tertiary" />
    </div>

    <x-admin.panel-card title="Listado de reservas" description="Filtra por estado para gestionar la agenda.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar codigo de reserva" class="wayna-input lg:w-80">
                <select wire:model.live="filtroEstado" class="wayna-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="reprogramada">Reprogramada</option>
                </select>
            </div>
        </x-slot>

        @if ($reservas->isEmpty())
            <x-admin.empty-state title="Sin reservas registradas" description="Cuando los usuarios hagan reservas de talleres o productos, apareceran aqui." icon="calendar_month" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Codigo</th>
                            <th class="wayna-table-th">Usuario</th>
                            <th class="wayna-table-th">Tipo</th>
                            <th class="wayna-table-th">Fecha</th>
                            <th class="wayna-table-th">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservas as $reserva)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td font-mono-data text-primary-600">{{ $reserva->codigo }}</td>
                                <td class="wayna-table-td text-ink">{{ $reserva->usuario?->nombre_completo ?? 'N/A' }}</td>
                                <td class="wayna-table-td">{{ str_replace('_', ' ', $reserva->tipo) }}</td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($reserva->fecha_reserva)->format('d/m/Y') }}</td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$reserva->estado === 'cancelada' ? 'red' : ($reserva->estado === 'pendiente' ? 'amber' : 'green')">
                                        {{ $reserva->estado }}
                                    </x-admin.status-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $reservas->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
