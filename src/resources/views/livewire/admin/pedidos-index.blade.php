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
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar codigo o cliente" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">
                <select wire:model.live="filtroEstado" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
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
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Codigo</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Cliente</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Emprendedor</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Total</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pedidos as $pedido)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5 font-mono-data text-sm text-[#5f4cae]">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $pedido->usuario?->nombre_completo ?? 'Usuario eliminado' }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $pedido->emprendedor?->nombre_negocio ?? 'N/A' }}</td>
                                <td class="px-4 py-5 font-mono-data text-sm text-slate-900">Bs {{ number_format((float) $pedido->total, 2) }}</td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$pedido->estado === 'cancelado' ? 'red' : ($pedido->estado === 'pendiente' ? 'amber' : 'green')">
                                        {{ $pedido->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($pedido->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $pedidos->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
