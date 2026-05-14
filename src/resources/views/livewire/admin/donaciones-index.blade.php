<div class="space-y-8">
    <x-admin.page-header eyebrow="Impacto social" title="Donaciones" description="Registro de donaciones realizadas por la comunidad al ecosistema artesanal." />

    <div class="grid gap-4 md:grid-cols-2">
        <x-admin.kpi-card label="Total donado" :value="'Bs '.number_format($totalMonto, 2)" icon="volunteer_activism" tone="primary" />
        <x-admin.kpi-card label="Donaciones" :value="$totalDonaciones" icon="favorite" tone="secondary" />
    </div>

    <x-admin.panel-card title="Historial de donaciones" description="Busca por donante para revisar el impacto generado.">
        <x-slot name="actions">
            <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar donante" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">
        </x-slot>

        @if ($donaciones->isEmpty())
            <x-admin.empty-state title="Sin donaciones registradas" description="Cuando los donantes hagan sus primeras contribuciones, apareceran aqui con su monto y destino." icon="volunteer_activism" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Donante</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Emprendedor</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Monto</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Anonima</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donaciones as $donacion)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $donacion->usuario?->nombre_completo ?? 'Anonimo' }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $donacion->emprendedor?->nombre_negocio ?? 'Fondo general' }}</td>
                                <td class="px-4 py-5 font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $donacion->monto, 2) }}</td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$donacion->es_anonima ? 'gray' : 'green'">{{ $donacion->es_anonima ? 'Si' : 'No' }}</x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($donacion->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $donaciones->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
