<div class="space-y-8">
    <x-admin.page-header eyebrow="Curaduria global" title="Categorias" description="Administra la taxonomia global del marketplace para que el catalogo crezca con orden.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">Nueva categoria</button>
        </x-slot>
    </x-admin.page-header>

    @if (session('admin_status'))
        <div class="rounded-2xl border border-[#d8d2de] bg-[#eef7f2] px-4 py-3 text-sm text-[#1f6b52]">{{ session('admin_status') }}</div>
    @endif

    @if (session('admin_error'))
        <div class="rounded-2xl border border-[#f0c7c2] bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">{{ session('admin_error') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="category" tone="primary" />
        <x-admin.kpi-card label="Con productos" :value="$resumen['con_productos']" icon="inventory_2" tone="secondary" />
        <x-admin.kpi-card label="Sin productos" :value="$resumen['sin_productos']" icon="shelves" tone="neutral" />
        <x-admin.kpi-card label="Con icono" :value="$resumen['con_icono']" icon="image" tone="tertiary" />
    </div>

    <x-admin.panel-card title="Categorias globales" description="Estas categorias alimentan filtros, relacion entre emprendedores y organizacion del inventario.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar categoria o descripcion" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0 lg:w-80">

            </div>
        </x-slot>

        @if ($categorias->isEmpty())
            <x-admin.empty-state title="Aun no hay categorias" description="Crea la primera categoria global para empezar a ordenar emprendedores y productos dentro del admin." icon="category">
                <x-slot name="action">
                    <button type="button" wire:click="abrirModal" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">Crear primera categoria</button>
                </x-slot>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Categoria</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Slug</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Icono</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Productos</th>
                            <th class="px-4 py-4 text-right font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="font-medium text-slate-900">{{ $categoria->nombre }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $categoria->descripcion ?: 'Sin descripcion registrada.' }}</p>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $categoria->slug }}</td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ $categoria->icono ?: 'Sin icono' }}</td>
                                <td class="px-4 py-5 font-mono-data text-sm text-[#5f4cae]">{{ $categoria->productos_count }}</td>
                                <td class="px-4 py-5">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" wire:click="abrirModal({{ $categoria->id }})" class="rounded-xl border border-[#d8d2de] px-3 py-2 text-xs font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Editar</button>
                                        <button type="button" wire:click="confirmarEliminar({{ $categoria->id }})" class="rounded-xl border border-[#f0c7c2] px-3 py-2 text-xs font-medium text-[#93000a] transition hover:bg-[#ffdad6]">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $categorias->links() }}</div>
        @endif
    </x-admin.panel-card>

    @if ($mostrarModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="w-full max-w-2xl rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Categoria</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $categoriaIdEditando ? 'Editar categoria' : 'Nueva categoria' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModal" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="space-y-5 px-6 py-6">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre</label>
                        <input wire:model.live="nombre" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Ej. Textiles, Ceramica, Joyeria">
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                    <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                    <textarea wire:model.live="descripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Describe brevemente esta familia de productos."></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModal" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardar" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">{{ $categoriaIdEditando ? 'Guardar cambios' : 'Crear categoria' }}</button>
                </div>
            </div>
        </div>
    @endif

    @if ($categoriaIdEliminar)
        <div class="fixed inset-0 z-[75] flex items-center justify-center bg-slate-950/30 px-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-[1.5rem] border border-[#d8d2de] bg-white p-6">
                <h3 class="font-display text-2xl text-slate-900">Eliminar categoria</h3>
                <p class="mt-2 text-sm text-slate-600">Solo se eliminara si no tiene productos asociados para no romper el catalogo existente.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('categoriaIdEliminar', null)" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700">Cancelar</button>
                    <button type="button" wire:click="eliminar" class="rounded-2xl bg-[#a03f29] px-4 py-3 text-sm font-medium text-white">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
