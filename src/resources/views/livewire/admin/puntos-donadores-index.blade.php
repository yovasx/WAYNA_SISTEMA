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
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar donador" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">

                <select wire:model.live="filtroNivel" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
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
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Posicion</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Donador</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Nivel</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Puntos</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Actualizado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($puntosDonadores as $registro)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5 font-mono-data text-sm text-[#5f4cae]">{{ ($puntosDonadores->firstItem() ?? 1) + $loop->index }}</td>
                                <td class="px-4 py-5">
                                    <p class="text-sm text-slate-700">{{ $registro->usuario?->nombre_completo ?? 'Usuario no disponible' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $registro->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$registro->nivel === 'oro' ? 'green' : ($registro->nivel === 'plata' ? 'gray' : 'amber')">
                                        {{ $registro->nivel }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 font-mono-data text-sm text-slate-900">{{ $registro->puntos_total }}</td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($registro->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $puntosDonadores->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
