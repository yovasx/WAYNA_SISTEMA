<div class="space-y-8">
    <x-admin.page-header eyebrow="Reconocimientos" title="Insignias" description="Administra las insignias que sostienen dinamicas de comunidad, gamificacion y reconocimiento.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Nueva insignia</button>
        </x-slot>
    </x-admin.page-header>

    @if (session('admin_status'))
        <div class="rounded-2xl border border-[#d8d2de] bg-[#eef7f2] px-4 py-3 text-sm text-[#1f6b52]">{{ session('admin_status') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="workspace_premium" tone="primary" />
        <x-admin.kpi-card label="Con icono" :value="$resumen['con_icono']" icon="image" tone="secondary" />
        <x-admin.kpi-card label="Con criterio" :value="$resumen['con_criterio']" icon="rule" tone="tertiary" />
        <x-admin.kpi-card label="Sin criterio" :value="$resumen['sin_criterio']" icon="notes" tone="neutral" />
    </div>

    <x-admin.panel-card title="Catalogo de insignias" description="Mantiene el inventario de reconocimientos visibles para usuarios y campañas futuras.">
        <x-slot name="actions">
            <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar insignia" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">
        </x-slot>

        @if ($insignias->isEmpty())
            <x-admin.empty-state title="Sin insignias registradas" description="Crea la primera insignia para empezar a estructurar criterios de logros y reconocimientos." icon="workspace_premium">
                <x-slot name="action">
                    <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">Crear primera insignia</button>
                </x-slot>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Insignia</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Icono</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Criterio</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($insignias as $insignia)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="font-medium text-slate-900">{{ $insignia->nombre }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $insignia->descripcion ?: 'Sin descripcion registrada.' }}</p>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-700 break-all">{{ $insignia->icono ?: 'Sin icono' }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $insignia->criterio_json ? 'Configurado' : 'Pendiente' }}</td>
                                <td class="px-4 py-5">
                                    <div class="flex justify-end gap-2">
                                        <x-admin.action-button wire:click="abrirModal({{ $insignia->id }})" icon="edit" label="Editar insignia" tone="primary" />
                                        <x-admin.action-button wire:click="confirmarEliminar({{ $insignia->id }})" icon="delete" label="Eliminar insignia" tone="danger" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $insignias->links() }}</div>
        @endif
    </x-admin.panel-card>

    @if ($mostrarModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Insignia</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $insigniaIdEditando ? 'Editar insignia' : 'Nueva insignia' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModal" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="space-y-5 px-6 py-6">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre</label>
                        <input wire:model.live="nombre" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Ej. Donador oro, Embajador cultural">
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                        <textarea wire:model.live="descripcion" rows="3" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Explica que reconoce esta insignia dentro del ecosistema."></textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Icono</label>
                        <input wire:model.live="icono" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="URL o identificador del icono">
                        <x-input-error :messages="$errors->get('icono')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Criterio JSON</label>
                        <textarea wire:model.live="criterioJson" rows="8" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 font-mono text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder='{"tipo":"donacion","minimo":10}'></textarea>
                        <x-input-error :messages="$errors->get('criterioJson')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModal" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardar" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">{{ $insigniaIdEditando ? 'Guardar cambios' : 'Crear insignia' }}</button>
                </div>
            </div>
        </div>
    @endif

    @if ($insigniaIdEliminar)
        <div class="fixed inset-0 z-[75] flex items-center justify-center bg-slate-950/30 px-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-[1.5rem] border border-[#d8d2de] bg-white p-6">
                <h3 class="font-display text-2xl text-slate-900">Eliminar insignia</h3>
                <p class="mt-2 text-sm text-slate-600">La insignia se quitara del catalogo de reconocimientos y dejara de estar disponible para nuevos flujos.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('insigniaIdEliminar', null)" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700">Cancelar</button>
                    <button type="button" wire:click="eliminar" class="rounded-2xl bg-[#a03f29] px-4 py-3 text-sm font-medium text-white">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
