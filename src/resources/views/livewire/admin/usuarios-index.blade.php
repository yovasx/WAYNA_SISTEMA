<div class="space-y-8">
    <x-admin.page-header eyebrow="Gestion de personas" title="Usuarios" description="Administra usuarios del sistema, asigna roles, activa o suspende cuentas.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Nuevo usuario</button>
        </x-slot>
    </x-admin.page-header>

    @if (session('admin_status'))
        <div class="rounded-2xl border border-[#d8d2de] bg-[#eef7f2] px-4 py-3 text-sm text-[#1f6b52]">{{ session('admin_status') }}</div>
    @endif
    @if (session('admin_error'))
        <div class="rounded-2xl border border-[#f0c7c2] bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">{{ session('admin_error') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="group" tone="primary" />
        <x-admin.kpi-card label="Activos" :value="$resumen['activos']" icon="check_circle" tone="green" />
        <x-admin.kpi-card label="Suspendidos" :value="$resumen['suspendidos']" icon="block" tone="red" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="amber" />
    </div>

    <x-admin.panel-card title="Listado de usuarios" description="Busca por nombre o correo, filtra por estado o rol.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar nombre o correo" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-72">
                <select wire:model.live="filtroEstado" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="suspendido">Suspendido</option>
                    <option value="pendiente">Pendiente</option>
                </select>
                <select wire:model.live="filtroRol" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->nombre }}">{{ ucfirst(strtolower($rol->nombre)) }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot>

        @if ($usuarios->isEmpty())
            <x-admin.empty-state title="Sin usuarios para mostrar" description="Ajusta los filtros o crea un nuevo usuario." icon="group" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Usuario</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Roles</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Registro</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="font-medium text-slate-900">{{ $usuario->nombre_completo }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $usuario->email }}</p>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($usuario->roles as $rol)
                                            <x-admin.status-badge tone="violet">{{ ucfirst(strtolower($rol->nombre)) }}</x-admin.status-badge>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$usuario->estado === 'activo' ? 'green' : ($usuario->estado === 'pendiente' ? 'amber' : 'red')">
                                        {{ $usuario->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($usuario->created_at)->format('d/m/Y') }}</td>
                                <td class="px-4 py-5">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="abrirModal({{ $usuario->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8d2de] text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae]" title="Editar usuario">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button type="button" wire:click="abrirModalRoles({{ $usuario->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8d2de] text-slate-600 transition hover:border-[#5f4cae] hover:text-[#5f4cae]" title="Gestionar roles">
                                            <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                                        </button>
                                        @if ($usuario->estado === 'activo')
                                            <button type="button" wire:click="suspender({{ $usuario->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#f0c7c2] text-[#93000a] transition hover:bg-[#ffdad6]" title="Suspender usuario">
                                                <span class="material-symbols-outlined text-[18px]">block</span>
                                            </button>
                                        @else
                                            <button type="button" wire:click="activar({{ $usuario->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8d2de] text-slate-600 transition hover:border-[#5f4cae] hover:text-[#1f6b52]" title="Activar usuario">
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $usuarios->links() }}</div>
        @endif
    </x-admin.panel-card>

    @if ($mostrarModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="w-full max-w-2xl rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Usuario</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $usuarioIdEditando ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModal" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="space-y-5 px-6 py-6">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre completo</label>
                        <input wire:model.live="nombre" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre y apellido">
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Correo electronico</label>
                        <input wire:model.live="email" type="email" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="correo@ejemplo.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    @if (! $usuarioIdEditando)
                        <div>
                            <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Contrasena</label>
                            <input wire:model.live="password" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Minimo 8 caracteres">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Confirmar contrasena</label>
                            <input wire:model.live="passwordConfirmacion" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Repite la contrasena">
                        </div>
                    @endif
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModal" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardar" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">{{ $usuarioIdEditando ? 'Guardar cambios' : 'Crear usuario' }}</button>
                </div>
            </div>
        </div>
    @endif

    @if ($mostrarModalRoles)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Roles</p>
                        <h3 class="mt-2 font-display text-2xl text-slate-900">Asignar roles al usuario</h3>
                    </div>
                    <button type="button" wire:click="cerrarModalRoles" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="space-y-4 px-6 py-6">
                    @foreach ($roles as $rol)
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-4 transition hover:border-[#5f4cae]">
                            <input type="checkbox" wire:model.live="rolesSeleccionados.{{ $rol->id }}" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                            <span class="text-sm font-medium text-slate-700">{{ ucfirst(strtolower($rol->nombre)) }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModalRoles" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardarRoles" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">Guardar roles</button>
                </div>
            </div>
        </div>
    @endif
</div>
