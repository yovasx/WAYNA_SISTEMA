<div class="space-y-8">
    <x-admin.page-header eyebrow="Gestion de personas" title="Emprendedores" description="Aprueba, suspende y supervisa perfiles emprendedores segun el flujo real de la plataforma." />

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
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="abrirModalEdicion({{ $perfil->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8d2de] text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae]" title="Editar datos">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        @if ($perfil->estado_aprobacion !== 'aprobado')
                                            <button type="button" wire:click="aprobar({{ $perfil->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8d2de] text-slate-600 transition hover:border-[#1f6b52] hover:text-[#1f6b52]" title="Aprobar emprendedor">
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                            </button>
                                        @endif

                                        @if ($perfil->estado_aprobacion !== 'suspendido')
                                            <button type="button" wire:click="suspender({{ $perfil->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#f0c7c2] text-[#93000a] transition hover:bg-[#ffdad6]" title="Suspender emprendedor">
                                                <span class="material-symbols-outlined text-[18px]">block</span>
                                            </button>
                                        @else
                                            <button type="button" wire:click="reactivar({{ $perfil->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8d2de] text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae]" title="Reactivar emprendedor">
                                                <span class="material-symbols-outlined text-[18px]">sync</span>
                                            </button>
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

    @if ($mostrarModalEdicion)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="w-full max-w-2xl rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Emprendedor</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">Editar datos del emprendedor</h3>
                    </div>
                    <button type="button" wire:click="cerrarModalEdicion" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="grid gap-5 px-6 py-6 lg:grid-cols-2">
                    <div class="lg:col-span-2">
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre del negocio</label>
                        <input wire:model.live="editNombreNegocio" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre del emprendimiento">
                        <x-input-error :messages="$errors->get('editNombreNegocio')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Categoria</label>
                        <select wire:model.live="editCategoriaId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="">Sin categoria</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('editCategoriaId')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">NIT</label>
                        <input wire:model.live="editNit" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Opcional">
                        <x-input-error :messages="$errors->get('editNit')" class="mt-2" />
                    </div>

                    <div class="lg:col-span-2">
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                        <textarea wire:model.live="editDescripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Describe el emprendimiento..."></textarea>
                        <x-input-error :messages="$errors->get('editDescripcion')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModalEdicion" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardarEdicion" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">Guardar cambios</button>
                </div>
            </div>
        </div>
    @endif
</div>
