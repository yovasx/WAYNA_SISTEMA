<div class="space-y-8">
    <x-admin.page-header eyebrow="Catalogo global" title="Productos" description="Gestiona el inventario transversal del marketplace y visibilidad de piezas destacadas.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Nuevo producto</button>
        </x-slot>
    </x-admin.page-header>

    @if (session('admin_status'))
        <div class="rounded-2xl border border-[#d8d2de] bg-[#eef7f2] px-4 py-3 text-sm text-[#1f6b52]">{{ session('admin_status') }}</div>
    @endif

    @if (session('admin_error'))
        <div class="rounded-2xl border border-[#f0c7c2] bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">{{ session('admin_error') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="inventory_2" tone="primary" />
        <x-admin.kpi-card label="Activos" :value="$resumen['activos']" icon="toggle_on" tone="tertiary" />
        <x-admin.kpi-card label="Agotados" :value="$resumen['agotados']" icon="remove_shopping_cart" tone="secondary" />
        <x-admin.kpi-card label="Stock critico" :value="$resumen['stock_critico']" icon="warning" tone="neutral" />
    </div>

    <x-admin.panel-card title="Inventario transversal" description="Filtra por categoria, emprendedor o disponibilidad para operar el catalogo sin cambiar de panel.">
        <x-slot name="actions">
            <div class="grid w-full gap-3 lg:grid-cols-5">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar producto o emprendedor" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0">

                <select wire:model.live="filtroCategoria" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todas las categorias</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filtroEmprendedor" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los emprendedores</option>
                    @foreach ($perfiles as $perfil)
                        <option value="{{ $perfil->id }}">{{ $perfil->nombre_emprendimiento }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filtroEstado" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los estados</option>
                    <option value="disponible">Disponible</option>
                    <option value="ultimas_unidades">Ultimas unidades</option>
                    <option value="agotado">Agotado</option>
                </select>

            </div>
        </x-slot>

        @if ($productos->isEmpty())
            <x-admin.empty-state title="No hay productos para mostrar" description="Crea el primer producto global o ajusta los filtros para revisar otro segmento del catalogo." icon="inventory_2">
                <x-slot name="action">
                    <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">Crear primer producto</button>
                </x-slot>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Producto</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Emprendedor</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Categoria</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Precio</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Stock</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Activo</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="font-medium text-slate-900">{{ $producto->nombre }}</p>
                                    <p class="mt-1 max-w-sm text-sm text-slate-600">{{ $producto->descripcion ?: 'Sin descripcion registrada.' }}</p>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $producto->perfilEmprendedor?->nombre_emprendimiento ?? 'Sin emprendedor' }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $producto->categoria?->nombre ?? 'Sin categoria' }}</td>
                                <td class="px-4 py-5 text-right font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $producto->precio, 2) }}</td>
                                <td class="px-4 py-5 text-right text-sm text-slate-700">{{ $producto->stock }}</td>
                                <td class="px-4 py-5">
                                    <select wire:change="actualizarEstado({{ $producto->id }}, $event.target.value)" class="rounded-xl border border-[#d8d2de] bg-white px-3 py-2 text-xs text-slate-700 outline-none focus:border-[#5f4cae] focus:ring-0">
                                        <option value="disponible" @selected($producto->estado_disponibilidad === 'disponible')>Disponible</option>
                                        <option value="ultimas_unidades" @selected($producto->estado_disponibilidad === 'ultimas_unidades')>Ultimas unidades</option>
                                        <option value="agotado" @selected($producto->estado_disponibilidad === 'agotado')>Agotado</option>
                                    </select>
                                </td>
                                <td class="px-4 py-5">
                                    <button type="button" wire:click="alternarActivo({{ $producto->id }})">
                                        <x-admin.status-badge :tone="$producto->activo ? 'green' : 'gray'">{{ $producto->activo ? 'Si' : 'No' }}</x-admin.status-badge>
                                    </button>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="flex justify-end gap-2">
                                        <x-admin.action-button wire:click="abrirModal({{ $producto->id }})" icon="edit" label="Editar producto" tone="primary" />
                                        <x-admin.action-button wire:click="confirmarEliminar({{ $producto->id }})" icon="delete" label="Eliminar producto" tone="danger" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $productos->links() }}</div>
        @endif
    </x-admin.panel-card>

    @if ($mostrarModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Producto</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $productoIdEditando ? 'Editar producto' : 'Nuevo producto global' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModal" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="grid gap-6 px-6 py-6 lg:grid-cols-2">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Emprendedor</label>
                        <select wire:model.live="perfilEmprendedorId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="">Selecciona un emprendedor</option>
                            @foreach ($perfiles as $perfil)
                                <option value="{{ $perfil->id }}">{{ $perfil->nombre_emprendimiento }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('perfilEmprendedorId')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Categoria</label>
                        <select wire:model.live="categoriaId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="">Sin categoria</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('categoriaId')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre</label>
                        <input wire:model.live="nombre" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre del producto">
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Precio</label>
                        <input wire:model.live="precio" type="number" step="0.01" min="0" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="0.00">
                        <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                    </div>

                    <div class="lg:col-span-2">
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                        <textarea wire:model.live="descripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Cuenta la historia, materiales o tecnica principal."></textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Stock</label>
                        <input wire:model.live="stock" type="number" min="0" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="0">
                        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Disponibilidad</label>
                        <select wire:model.live="estadoDisponibilidad" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="disponible">Disponible</option>
                            <option value="ultimas_unidades">Ultimas unidades</option>
                            <option value="agotado">Agotado</option>
                        </select>
                        <x-input-error :messages="$errors->get('estadoDisponibilidad')" class="mt-2" />
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-4 lg:col-span-2">
                        <input wire:model.live="activo" type="checkbox" class="rounded border-[#cbc4d4] text-[#5f4cae] focus:ring-[#5f4cae]">
                        <span class="text-sm text-slate-700">Mantener el producto activo en el catalogo</span>
                    </label>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModal" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardar" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">{{ $productoIdEditando ? 'Guardar cambios' : 'Crear producto' }}</button>
                </div>
            </div>
        </div>
    @endif

    @if ($productoIdEliminar)
        <div class="fixed inset-0 z-[75] flex items-center justify-center bg-slate-950/30 px-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-[1.5rem] border border-[#d8d2de] bg-white p-6">
                <h3 class="font-display text-2xl text-slate-900">Eliminar producto</h3>
                <p class="mt-2 text-sm text-slate-600">Esta accion quitara el producto del inventario global administrado desde el panel.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('productoIdEliminar', null)" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700">Cancelar</button>
                    <button type="button" wire:click="eliminar" class="rounded-2xl bg-[#a03f29] px-4 py-3 text-sm font-medium text-white">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
