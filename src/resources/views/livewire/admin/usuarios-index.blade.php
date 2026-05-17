<div class="space-y-8">
    <x-admin.page-header eyebrow="Gestion de personas" title="Usuarios" description="Administra usuarios del sistema, asigna roles, activa o suspende cuentas.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModal" class="wayna-btn-primary">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                <span>Nuevo usuario</span>
            </button>
        </x-slot>
    </x-admin.page-header>

    @if (session('admin_status'))
        <div class="wayna-alert-success">{{ session('admin_status') }}</div>
    @endif
    @if (session('admin_error'))
        <div class="wayna-alert-danger">{{ session('admin_error') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="group" tone="primary" />
        <x-admin.kpi-card label="Activos" :value="$resumen['activos']" icon="check_circle" tone="green" />
        <x-admin.kpi-card label="Suspendidos" :value="$resumen['suspendidos']" icon="block" tone="red" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="amber" />
    </div>

    <x-admin.panel-card title="Tablero de estados" description="Segmenta rapido el directorio para recuperar visibles los usuarios activos, suspendidos y pendientes.">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <button type="button" wire:click="seleccionarFiltroEstado('')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === '' ? 'border-[#5f4cae] bg-[#f7f2fb]' : 'border-[#d8d2de] bg-white hover:border-[#5f4cae]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Todos</p>
                <p class="mt-3 font-display text-3xl text-slate-900">{{ $resumen['totales'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Vista general del directorio.</p>
            </button>

            <button type="button" wire:click="seleccionarFiltroEstado('activo')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === 'activo' ? 'border-[#1f6b52] bg-[#eef7f2]' : 'border-[#d8d2de] bg-white hover:border-[#1f6b52]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Activos</p>
                <p class="mt-3 font-display text-3xl text-[#1f6b52]">{{ $resumen['activos'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Cuentas listas para operar.</p>
            </button>

            <button type="button" wire:click="seleccionarFiltroEstado('suspendido')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === 'suspendido' ? 'border-[#93000a] bg-[#ffdad6]' : 'border-[#d8d2de] bg-white hover:border-[#93000a]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Suspendidos</p>
                <p class="mt-3 font-display text-3xl text-[#93000a]">{{ $resumen['suspendidos'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Usuarios fuera de circulacion.</p>
            </button>

            <button type="button" wire:click="seleccionarFiltroEstado('pendiente')" class="rounded-[1.5rem] border px-4 py-4 text-left transition {{ $filtroEstado === 'pendiente' ? 'border-[#745800] bg-[#fff3cd]' : 'border-[#d8d2de] bg-white hover:border-[#745800]' }}">
                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Pendientes</p>
                <p class="mt-3 font-display text-3xl text-[#745800]">{{ $resumen['pendientes'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Registros que requieren seguimiento.</p>
            </button>
        </div>
    </x-admin.panel-card>

    <x-admin.panel-card title="Listado de usuarios" description="Busca por nombre o correo, filtra por estado o rol.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar nombre o correo" class="wayna-input lg:w-72">
                <select wire:model.live="filtroRol" class="wayna-select">
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
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Usuario</th>
                            <th class="wayna-table-th">Roles</th>
                            <th class="wayna-table-th">Estado</th>
                            <th class="wayna-table-th">Registro</th>
                            <th class="wayna-table-th text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td">
                                    <p class="font-medium text-ink">{{ $usuario->nombre_completo }}</p>
                                    <p class="mt-1 text-sm text-ink-soft">{{ $usuario->email }}</p>
                                </td>
                                <td class="wayna-table-td">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($usuario->roles as $rol)
                                            <x-admin.status-badge tone="violet">{{ ucfirst(strtolower($rol->nombre)) }}</x-admin.status-badge>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$usuario->estado === 'activo' ? 'green' : ($usuario->estado === 'pendiente' ? 'amber' : 'red')">
                                        {{ $usuario->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($usuario->created_at)->format('d/m/Y') }}</td>
                                <td class="wayna-table-td">
                                    <div class="flex justify-end gap-1">
                                        <x-admin.action-button wire:click="abrirModal({{ $usuario->id }})" icon="edit" label="Editar usuario" tone="primary" />
                                        <x-admin.action-button wire:click="abrirModalRoles({{ $usuario->id }})" icon="manage_accounts" label="Gestionar roles" tone="neutral" />
                                        @if ($usuario->estado === 'activo')
                                            <x-admin.action-button wire:click="suspender({{ $usuario->id }})" icon="block" label="Suspender usuario" tone="danger" />
                                        @else
                                            <x-admin.action-button wire:click="activar({{ $usuario->id }})" icon="check_circle" label="Activar usuario" tone="success" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $usuarios->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </x-admin.panel-card>

    @if ($mostrarModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Usuario</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $usuarioIdEditando ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModal" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="space-y-6 px-6 py-6">
                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Datos base</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">Informacion principal del usuario</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-2">
                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre completo</label>
                                <input wire:model.live="nombre" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre y apellido">
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Correo electronico</label>
                                <input wire:model.live="email" type="email" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="correo@ejemplo.com">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Telefono</label>
                                <input wire:model.live="telefono" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Opcional">
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Foto de perfil</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">Avatar del usuario</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-[160px_minmax(0,1fr)]">
                            <div class="flex h-40 w-40 items-center justify-center overflow-hidden rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-white text-slate-400">
                                @if ($fotoPerfilNueva)
                                    <img src="{{ $fotoPerfilNueva->temporaryUrl() }}" alt="Nueva foto de perfil" class="h-full w-full object-cover">
                                @elseif ($this->fotoPerfilActualUrl() && ! $eliminarFotoPerfil)
                                    <img src="{{ $this->fotoPerfilActualUrl() }}" alt="Foto actual de perfil" class="h-full w-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-[42px]">account_circle</span>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Subir imagen</label>
                                    <input wire:model.live="fotoPerfilNueva" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#f7f2fb] file:px-3 file:py-2 file:text-sm file:font-medium file:text-[#5f4cae]">
                                    <x-input-error :messages="$errors->get('fotoPerfilNueva')" class="mt-2" />
                                </div>

                                @if ($this->fotoPerfilActualUrl())
                                    <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                                        <input wire:model.live="eliminarFotoPerfil" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                                        <span class="text-sm text-slate-700">Quitar foto de perfil actual</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                        <div>
                            <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Seguridad</p>
                            <h4 class="mt-2 text-lg font-medium text-slate-900">{{ $usuarioIdEditando ? 'Actualizar contrasena si lo necesitas' : 'Define la contrasena inicial del usuario' }}</h4>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-2">
                            @if (! $usuarioIdEditando)
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Contrasena</label>
                                    <input wire:model.live="password" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Minimo 8 caracteres">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Confirmar contrasena</label>
                                    <input wire:model.live="passwordConfirmacion" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Repite la contrasena">
                                </div>
                            @else
                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nueva contrasena</label>
                                    <input wire:model.live="nuevaPassword" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Deja vacio para mantener la actual">
                                    <x-input-error :messages="$errors->get('nuevaPassword')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Confirmar nueva contrasena</label>
                                    <input wire:model.live="nuevaPasswordConfirmacion" type="password" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Repite la nueva contrasena">
                                </div>
                            @endif
                        </div>
                    </section>

                    @if ($usuarioIdEditando && $mostrarBloqueEmprendedor)
                        <section class="space-y-5 rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-5">
                            <div>
                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Perfil emprendedor</p>
                                <h4 class="mt-2 text-lg font-medium text-slate-900">Datos comerciales vinculados al usuario</h4>
                            </div>

                            <div class="grid gap-5 lg:grid-cols-2">
                                <div class="lg:col-span-2">
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre del negocio</label>
                                    <input wire:model.live="emprendedorNombreNegocio" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre del emprendimiento">
                                    <x-input-error :messages="$errors->get('emprendedorNombreNegocio')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Categoria</label>
                                    <select wire:model.live="emprendedorCategoriaId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                                        <option value="">Sin categoria</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('emprendedorCategoriaId')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">NIT</label>
                                    <input wire:model.live="emprendedorNit" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Opcional">
                                    <x-input-error :messages="$errors->get('emprendedorNit')" class="mt-2" />
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                                    <textarea wire:model.live="emprendedorDescripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Describe el emprendimiento y su propuesta de valor."></textarea>
                                    <x-input-error :messages="$errors->get('emprendedorDescripcion')" class="mt-2" />
                                </div>

                                <div class="lg:col-span-2 grid gap-5 lg:grid-cols-[160px_minmax(0,1fr)]">
                                    <div class="flex h-40 w-40 items-center justify-center overflow-hidden rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-white text-slate-400">
                                        @if ($emprendedorFotoPortadaNueva)
                                            <img src="{{ $emprendedorFotoPortadaNueva->temporaryUrl() }}" alt="Nueva portada del emprendimiento" class="h-full w-full object-cover">
                                        @elseif ($this->fotoPortadaActualUrl() && ! $eliminarEmprendedorFotoPortada)
                                            <img src="{{ $this->fotoPortadaActualUrl() }}" alt="Portada actual del emprendimiento" class="h-full w-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-[42px]">image</span>
                                        @endif
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Portada del emprendimiento</label>
                                            <input wire:model.live="emprendedorFotoPortadaNueva" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#f7f2fb] file:px-3 file:py-2 file:text-sm file:font-medium file:text-[#5f4cae]">
                                            <x-input-error :messages="$errors->get('emprendedorFotoPortadaNueva')" class="mt-2" />
                                        </div>

                                        @if ($this->fotoPortadaActualUrl())
                                            <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-white px-4 py-4">
                                                <input wire:model.live="eliminarEmprendedorFotoPortada" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                                                <span class="text-sm text-slate-700">Quitar portada actual del emprendimiento</span>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </section>
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
