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
            <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar donador o ID de donacion" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">
        </x-slot>

        @if ($historial->isEmpty())
            <x-admin.empty-state title="Sin historial disponible" description="Cuando una donacion genere puntos, el detalle quedara registrado aqui con su multiplicador." icon="history" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Donador</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Donacion</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Puntos</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Multiplicador</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $registro)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="text-sm text-slate-700">{{ $registro->usuario?->nombre_completo ?? 'Usuario no disponible' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $registro->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="px-4 py-5">
                                    <p class="font-mono-data text-sm text-[#5f4cae]">#{{ $registro->donacion_id }}</p>
                                    <p class="mt-1 text-sm text-slate-600">Bs {{ number_format((float) ($registro->donacion?->monto ?? 0), 2) }} · {{ $registro->donacion?->emprendedor?->nombre_negocio ?? 'Fondo general' }}</p>
                                </td>
                                <td class="px-4 py-5 font-mono-data text-sm text-slate-900">{{ $registro->puntos_ganados }}</td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$registro->multiplicador > 1 ? 'green' : 'gray'">
                                        x{{ number_format((float) $registro->multiplicador, 1) }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($registro->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $historial->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
