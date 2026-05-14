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
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar codigo de reserva" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">
                <select wire:model.live="filtroEstado" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
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
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Codigo</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Usuario</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Tipo</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Fecha</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservas as $reserva)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5 font-mono-data text-sm text-[#5f4cae]">{{ $reserva->codigo }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $reserva->usuario?->nombre_completo ?? 'N/A' }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ str_replace('_', ' ', $reserva->tipo) }}</td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($reserva->fecha_reserva)->format('d/m/Y') }}</td>
                                <td class="px-4 py-5">
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
