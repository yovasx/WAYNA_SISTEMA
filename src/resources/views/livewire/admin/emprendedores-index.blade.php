<div class="space-y-8">
    <x-admin.page-header eyebrow="Gestion de personas" title="Emprendedores" description="Aprueba, suspende y supervisa perfiles emprendedores segun el flujo real de la plataforma." />

    @if (session('admin_status'))
        <div class="rounded-2xl border border-[#d8d2de] bg-[#eef7f2] px-4 py-3 text-sm text-[#1f6b52]">{{ session('admin_status') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Aprobados" :value="$resumen['aprobados']" icon="verified" tone="tertiary" />
        <x-admin.kpi-card label="Suspendidos" :value="$resumen['suspendidos']" icon="block" tone="neutral" />
        <x-admin.kpi-card label="Con NIT" :value="$resumen['con_nit']" icon="badge" tone="primary" />
    </div>

    <x-admin.panel-card title="Mesa de revision" description="Busca por emprendimiento, responsable o correo y ejecuta acciones de aprobacion sin salir del panel.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar emprendimiento, ciudad o correo" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">

                <select wire:model.live="filtroEstado" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="suspendido">Suspendido</option>
                </select>

                <select wire:model.live="filtroCategoria" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todas las categorias</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot>

        @if ($emprendedores->isEmpty())
            <x-admin.empty-state title="Sin emprendedores para mostrar" description="Ajusta los filtros o espera nuevos registros para empezar la revision del ecosistema." icon="storefront" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Emprendimiento</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Responsable</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Categoria</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">NIT</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emprendedores as $perfil)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="font-medium text-slate-900">{{ $perfil->nombre_emprendimiento }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Alta {{ optional($perfil->created_at)->format('d/m/Y') }}</p>
                                </td>
                                <td class="px-4 py-5">
                                    <p class="text-sm text-slate-900">{{ $perfil->usuario?->name ?? 'Sin usuario' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $perfil->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $perfil->categoria?->nombre ?? 'Sin categoria' }}</td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$perfil->estado_aprobacion === 'aprobado' ? 'green' : ($perfil->estado_aprobacion === 'pendiente' ? 'amber' : 'red')">
                                        {{ $perfil->estado_aprobacion }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $perfil->nit ?: 'No registrado' }}</td>
                                <td class="px-4 py-5">
                                    <div class="flex justify-end gap-2">
                                        @if ($perfil->estado_aprobacion !== 'aprobado')
                                            <button type="button" wire:click="aprobar({{ $perfil->id }})" class="rounded-xl border border-[#d8d2de] px-3 py-2 text-xs font-medium text-slate-700 transition hover:border-[#1f6b52] hover:text-[#1f6b52]">Aprobar</button>
                                        @endif

                                        @if ($perfil->estado_aprobacion !== 'suspendido')
                                            <button type="button" wire:click="suspender({{ $perfil->id }})" class="rounded-xl border border-[#f0c7c2] px-3 py-2 text-xs font-medium text-[#93000a] transition hover:bg-[#ffdad6]">Suspender</button>
                                        @else
                                            <button type="button" wire:click="reactivar({{ $perfil->id }})" class="rounded-xl border border-[#d8d2de] px-3 py-2 text-xs font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Reactivar</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $emprendedores->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
