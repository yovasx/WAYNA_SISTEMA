<div class="space-y-8">
    <x-admin.page-header eyebrow="Operaciones" title="Pedidos" description="Gestion de pedidos del marketplace, estados y seguimiento." />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="receipt_long" tone="primary" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Confirmados" :value="$resumen['confirmados']" icon="check_circle" tone="tertiary" />
        <x-admin.kpi-card label="Ingresos" :value="'Bs '.number_format($resumen['ingresos'], 2)" icon="payments" tone="neutral" />
    </div>

    <x-admin.panel-card title="Listado de pedidos" description="Busca por codigo o cliente, filtra por estado.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar codigo o cliente" class="wayna-input lg:w-80">
                <select wire:model.live="filtroEstado" class="wayna-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </x-slot>

        @if ($pedidos->isEmpty())
            <x-admin.empty-state title="Sin pedidos registrados" description="Cuando los compradores realicen pedidos, apareceran aqui con su estado y seguimiento." icon="receipt_long" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Codigo</th>
                            <th class="wayna-table-th">Cliente</th>
                            <th class="wayna-table-th">Emprendedor</th>
                            <th class="wayna-table-th">Total</th>
                            <th class="wayna-table-th">Estado</th>
                            <th class="wayna-table-th">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pedidos as $pedido)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td font-mono-data text-primary-600">{{ $pedido->codigo }}</td>
                                <td class="wayna-table-td text-ink">{{ $pedido->usuario?->nombre_completo ?? 'Usuario eliminado' }}</td>
                                <td class="wayna-table-td">{{ $pedido->emprendedor?->nombre_negocio ?? 'N/A' }}</td>
                                <td class="wayna-table-td font-mono-data text-ink">Bs {{ number_format((float) $pedido->total, 2) }}</td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$pedido->estado === 'cancelado' ? 'red' : ($pedido->estado === 'pendiente' ? 'amber' : 'green')">
                                        {{ $pedido->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($pedido->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $pedidos->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
