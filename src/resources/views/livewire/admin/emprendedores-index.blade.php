<div class="space-y-8">
    <x-admin.page-header eyebrow="Gestion de personas" title="Emprendedores" description="Aprueba, suspende y supervisa perfiles emprendedores segun el flujo real de la plataforma.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModalEdicion" class="inline-flex items-center gap-2 rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                <span class="material-symbols-outlined text-[20px]">storefront</span>
                <span>Nuevo emprendedor</span>
            </button>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="storefront" tone="primary" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Aprobados" :value="$resumen['aprobados']" icon="verified" tone="green" />
        <x-admin.kpi-card label="Suspendidos" :value="$resumen['suspendidos']" icon="block" tone="red" />
        <x-admin.kpi-card label="Con NIT" :value="$resumen['con_nit']" icon="badge" tone="amber" />
    </div>

    <x-admin.panel-card title="Tablero de estados" description="Recupera al instante pendientes, aprobados o suspendidos y mantiene visibles los perfiles fuera del flujo activo.">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <button type="button" wire:click="seleccionarFiltroEstado('')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === '' ? 'border-[#5f4cae] bg-[#f7f2fb]' : 'border-[#d8d2de] bg-white hover:border-[#5f4cae]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Todos</p>
                <p class="mt-3 font-display text-3xl text-slate-900">{{ $resumen['totales'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Vista completa del ecosistema.</p>
            </button>

            <button type="button" wire:click="seleccionarFiltroEstado('pendiente')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === 'pendiente' ? 'border-[#745800] bg-[#fff3cd]' : 'border-[#d8d2de] bg-white hover:border-[#745800]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Pendientes</p>
                <p class="mt-3 font-display text-3xl text-[#745800]">{{ $resumen['pendientes'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Perfiles que esperan aprobacion.</p>
            </button>

            <button type="button" wire:click="seleccionarFiltroEstado('aprobado')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === 'aprobado' ? 'border-[#1f6b52] bg-[#eef7f2]' : 'border-[#d8d2de] bg-white hover:border-[#1f6b52]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Aprobados</p>
                <p class="mt-3 font-display text-3xl text-[#1f6b52]">{{ $resumen['aprobados'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Negocios activos y operando.</p>
            </button>

            <button type="button" wire:click="seleccionarFiltroEstado('suspendido')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === 'suspendido' ? 'border-[#93000a] bg-[#ffdad6]' : 'border-[#d8d2de] bg-white hover:border-[#93000a]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Suspendidos</p>
                <p class="mt-3 font-display text-3xl text-[#93000a]">{{ $resumen['suspendidos'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Casos retenidos o fuera de servicio.</p>
            </button>
        </div>
    </x-admin.panel-card>

    <x-admin.panel-card title="Mesa de revision" description="Busca por emprendimiento, responsable o correo y ejecuta acciones de aprobacion sin salir del panel.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar emprendimiento, ciudad o correo" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">
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
                                        <x-admin.action-button wire:click="abrirModalEdicion({{ $perfil->id }})" icon="edit" label="Editar emprendedor" tone="primary" />

                                        @if ($perfil->estado_aprobacion !== 'aprobado')
                                            <x-admin.action-button wire:click="aprobar({{ $perfil->id }})" icon="check_circle" label="Aprobar emprendedor" tone="success" />
                                        @endif

                                        @if ($perfil->estado_aprobacion !== 'suspendido')
                                            <x-admin.action-button wire:click="suspender({{ $perfil->id }})" icon="block" label="Suspender emprendedor" tone="danger" />
                                        @else
                                            <x-admin.action-button wire:click="reactivar({{ $perfil->id }})" icon="sync" label="Reactivar emprendedor" tone="success" />
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
            <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Emprendedor</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $emprendedorEditandoId ? 'Editar datos del emprendedor' : 'Nuevo emprendedor' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModalEdicion" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="space-y-6 px-6 py-6">
                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Responsable</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">Datos del usuario que gestiona el emprendimiento</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-2">
                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre completo</label>
                                <input wire:model.live="editNombreUsuario" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre completo del responsable">
                                <x-input-error :messages="$errors->get('editNombreUsuario')" class="mt-2" />
                            </div>

                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Correo electronico</label>
                                <input wire:model.live="editEmail" type="email" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="correo@emprendimiento.com">
                                <x-input-error :messages="$errors->get('editEmail')" class="mt-2" />
                            </div>

                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Telefono</label>
                                <input wire:model.live="editTelefono" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Opcional">
                                <x-input-error :messages="$errors->get('editTelefono')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Foto de perfil del responsable</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">Imagen visible del usuario asociado</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-[160px_minmax(0,1fr)]">
                            <div class="flex h-40 w-40 items-center justify-center overflow-hidden rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-white text-slate-400">
                                @if ($editFotoPerfilNueva)
                                    <img src="{{ $editFotoPerfilNueva->temporaryUrl() }}" alt="Nueva foto del responsable" class="h-full w-full object-cover">
                                @elseif ($this->editFotoPerfilActualUrl() && ! $editEliminarFotoPerfil)
                                    <img src="{{ $this->editFotoPerfilActualUrl() }}" alt="Foto actual del responsable" class="h-full w-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-[42px]">account_circle</span>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Subir imagen</label>
                                    <input wire:model.live="editFotoPerfilNueva" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#f7f2fb] file:px-3 file:py-2 file:text-sm file:font-medium file:text-[#5f4cae]">
                                    <x-input-error :messages="$errors->get('editFotoPerfilNueva')" class="mt-2" />
                                </div>

                                @if ($this->editFotoPerfilActualUrl())
                                    <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                                        <input wire:model.live="editEliminarFotoPerfil" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                                        <span class="text-sm text-slate-700">Quitar foto de perfil actual</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Negocio</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">Informacion publica del emprendimiento</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-2">
                            <div class="lg:col-span-2">
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre del negocio</label>
                                <input wire:model.live="editNombreNegocio" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre del emprendimiento">
                                <x-input-error :messages="$errors->get('editNombreNegocio')" class="mt-2" />
                            </div>

                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Categoria</label>
                                <select wire:model.live="editCategoriaId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                                    <option value="">Sin categoria</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('editCategoriaId')" class="mt-2" />
                            </div>

                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">NIT</label>
                                <input wire:model.live="editNit" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Opcional">
                                <x-input-error :messages="$errors->get('editNit')" class="mt-2" />
                            </div>

                            <div class="lg:col-span-2">
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                                <textarea wire:model.live="editDescripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Describe el emprendimiento..."></textarea>
                                <x-input-error :messages="$errors->get('editDescripcion')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Portada del emprendimiento</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">Imagen principal del negocio</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
                            <div class="flex h-40 w-full items-center justify-center overflow-hidden rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-white text-slate-400">
                                @if ($editFotoPortadaNueva)
                                    <img src="{{ $editFotoPortadaNueva->temporaryUrl() }}" alt="Nueva portada" class="h-full w-full object-cover">
                                @elseif ($this->editFotoPortadaActualUrl() && ! $editEliminarFotoPortada)
                                    <img src="{{ $this->editFotoPortadaActualUrl() }}" alt="Portada actual" class="h-full w-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-[42px]">image</span>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Subir portada</label>
                                    <input wire:model.live="editFotoPortadaNueva" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#f7f2fb] file:px-3 file:py-2 file:text-sm file:font-medium file:text-[#5f4cae]">
                                    <x-input-error :messages="$errors->get('editFotoPortadaNueva')" class="mt-2" />
                                </div>

                                @if ($this->editFotoPortadaActualUrl())
                                    <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                                        <input wire:model.live="editEliminarFotoPortada" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                                        <span class="text-sm text-slate-700">Quitar portada actual</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Seguridad</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">{{ $emprendedorEditandoId ? 'Actualiza la contrasena solo si hace falta' : 'Define la contrasena inicial del responsable' }}</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-2">
                            @if (! $emprendedorEditandoId)
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Contrasena</label>
                                    <input wire:model.live="editPassword" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Minimo 8 caracteres">
                                    <x-input-error :messages="$errors->get('editPassword')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Confirmar contrasena</label>
                                    <input wire:model.live="editPasswordConfirmacion" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Repite la contrasena">
                                </div>
                            @else
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nueva contrasena</label>
                                    <input wire:model.live="editNuevaPassword" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Deja vacio para mantener la actual">
                                    <x-input-error :messages="$errors->get('editNuevaPassword')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Confirmar nueva contrasena</label>
                                    <input wire:model.live="editNuevaPasswordConfirmacion" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Repite la nueva contrasena">
                                </div>
                            @endif
                        </div>
                    </section>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModalEdicion" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardarEdicion" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">{{ $emprendedorEditandoId ? 'Guardar cambios' : 'Crear emprendedor' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
