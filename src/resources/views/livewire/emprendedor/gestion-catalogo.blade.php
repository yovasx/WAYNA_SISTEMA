<div class="space-y-8">
    @if (session('catalogo_estado'))
        <div class="wayna-alert-success">
            {{ session('catalogo_estado') }}
        </div>
    @endif

    @if (session('catalogo_error'))
        <div class="wayna-alert-danger">
            {{ session('catalogo_error') }}
        </div>
    @endif

    <x-admin.page-header eyebrow="Gestion de productos" title="Mi catalogo artesanal" description="Administra inventario, precios y visibilidad de tus piezas activas desde una vista separada del dashboard.">
        <x-slot name="actions">
            <button type="button" wire:click="abrirModalProducto" class="wayna-btn-primary">
                Nuevo producto
            </button>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-admin.kpi-card label="Productos activos" :value="$resumen['activos']" icon="inventory_2" tone="primary" />
        <x-admin.kpi-card label="Agotados" :value="$resumen['agotados']" icon="remove_shopping_cart" tone="secondary" />
        <x-admin.kpi-card label="Stock critico" :value="$resumen['stock_critico']" icon="warning" tone="amber" />
        <x-admin.kpi-card label="Ventas acumuladas" :value="'Bs '.number_format($resumen['ventas_totales'], 2)" icon="payments" tone="green" />
        <x-admin.kpi-card label="Unidades vendidas" :value="$resumen['unidades_vendidas']" icon="sell" tone="neutral" />
    </div>

    <x-admin.panel-card title="Productos mas vendidos" description="Cada pedido validado suma unidades y monto al ranking. El bloque respeta el periodo y los filtros activos del catalogo.">
        <x-slot name="actions">
            <div class="grid w-full gap-3 sm:grid-cols-2 lg:w-auto">
                <select wire:model.live="graficoPreset" class="wayna-select">
                    <option value="7d">Ultimos 7 dias</option>
                    <option value="30d">Ultimos 30 dias</option>
                    <option value="90d">Ultimos 90 dias</option>
                    <option value="all">Todo el historial</option>
                </select>

                <select wire:model.live="graficoMetrica" class="wayna-select">
                    <option value="unidades">Por unidades</option>
                    <option value="ventas">Por monto</option>
                </select>
            </div>
        </x-slot>

        @if ($topProductosGrafico->isEmpty())
            <x-admin.empty-state title="Sin productos vendidos" description="Cuando existan pedidos confirmados, entregados o completados, este ranking mostrara las piezas con mejor salida comercial." icon="bar_chart" />
        @else
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)]">
                <div class="space-y-4">
                    @foreach ($topProductosGrafico as $producto)
                        @php
                            $valorPrincipal = (float) ($graficoMetrica === 'ventas' ? $producto->ventas : $producto->unidades);
                            $ancho = $graficoMax > 0 ? max(($valorPrincipal / $graficoMax) * 100, 8) : 0;
                        @endphp

                        <article class="rounded-[1.5rem] border border-[#ebe6ef] bg-[#fcfbfe] p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-medium text-ink">{{ $producto->nombre }}</p>
                                    <p class="mt-1 text-sm text-ink-soft">{{ (int) $producto->unidades }} und. vendidas</p>
                                </div>

                                <div class="text-left sm:text-right">
                                    <p class="font-mono-data text-sm text-primary-600">
                                        @if ($graficoMetrica === 'ventas')
                                            Bs {{ number_format((float) $producto->ventas, 2) }}
                                        @else
                                            {{ (int) $producto->unidades }} und.
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Bs {{ number_format((float) $producto->ventas, 2) }} en ventas</p>
                                </div>
                            </div>

                            <div class="mt-4 h-3 overflow-hidden rounded-full bg-[#e8e2ee]">
                                <div class="h-full rounded-full bg-[#5f4cae] transition-all" style="width: {{ min($ancho, 100) }}%"></div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="rounded-[1.5rem] border border-[#ebe6ef] bg-white p-5">
                    <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Lectura del ranking</p>
                    <div class="mt-5 space-y-4 text-sm text-slate-600">
                        <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                            <p class="font-medium text-slate-900">Metrica activa</p>
                            <p class="mt-1">{{ $graficoMetrica === 'ventas' ? 'Ordenado por monto vendido acumulado.' : 'Ordenado por unidades vendidas acumuladas.' }}</p>
                        </div>

                        <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                            <p class="font-medium text-slate-900">Estados que suman</p>
                            <p class="mt-1">Solo cuentan pedidos confirmados, entregados o completados.</p>
                        </div>

                        <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                            <p class="font-medium text-slate-900">Contexto</p>
                            <p class="mt-1">El ranking se recalcula cuando cambias categoria o disponibilidad dentro de tu catalogo.</p>
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </x-admin.panel-card>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,2.8fr)_minmax(320px,1fr)] 2xl:grid-cols-[minmax(0,3fr)_minmax(340px,1fr)]">
        <section class="wayna-card overflow-hidden">
            <div class="flex flex-col gap-4 border-b border-stroke-soft bg-surface-soft px-6 py-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-ink-muted">Gestion de productos</p>
                    <h2 class="mt-2 font-display text-3xl text-ink">Mi catalogo artesanal</h2>
                    <p class="mt-2 text-sm text-ink-soft">Administra inventario, precios y visibilidad de tus piezas activas.</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 border-b border-stroke-soft px-6 py-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="relative w-full xl:flex-1">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-ink-muted">Buscar</span>
                    <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Nombre o descripcion del producto" class="wayna-input pl-20">
                </div>

                <div class="grid w-full gap-3 xl:w-auto xl:grid-cols-2 xl:items-center">
                    <label for="filtroCategoria" class="wayna-label">Categoria</label>
                    <select id="filtroCategoria" wire:model.live="filtroCategoria" class="wayna-select w-full xl:w-auto xl:min-w-[220px]">
                        <option value="">Todas</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>

                    <label for="filtroEstado" class="wayna-label">Estado</label>
                    <select id="filtroEstado" wire:model.live="filtroEstado" class="wayna-select w-full xl:w-auto xl:min-w-[220px]">
                        <option value="">Todos</option>
                        <option value="disponible">Disponible</option>
                        <option value="ultimas_unidades">Ultimas unidades</option>
                        <option value="agotado">Agotado</option>
                    </select>
                </div>
            </div>

            <div class="wayna-table-wrap rounded-none border-0">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th px-6">Producto</th>
                            <th class="wayna-table-th px-6">Categoria</th>
                            <th class="wayna-table-th px-6 text-right">Rendimiento</th>
                            <th class="wayna-table-th px-6 text-right">Precio</th>
                            <th class="wayna-table-th px-6 text-right">Stock</th>
                            <th class="wayna-table-th px-6">Estado</th>
                            <th class="wayna-table-th px-6 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td px-6">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-primary-50 font-display text-lg text-primary-700">
                                            {{ strtoupper(mb_substr($producto->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-ink">{{ $producto->nombre }}</h3>
                                            <p class="mt-1 text-sm text-ink-soft">{{ $producto->descripcion ?: 'Sin descripcion registrada.' }}</p>
                                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                <span class="inline-flex rounded-full {{ $producto->activo ? 'bg-success-100 text-success-700' : 'bg-surface-soft text-ink-soft' }} px-3 py-1 font-medium">{{ $producto->activo ? 'Activo' : 'Inactivo' }}</span>
                                                <span>{{ (int) $producto->unidades_vendidas }} und. vendidas</span>
                                                <span>Bs {{ number_format((float) $producto->ventas_generadas, 2) }} en ventas</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="wayna-table-td px-6">{{ $producto->categoria?->nombre ?? 'Sin categoria' }}</td>
                                <td class="wayna-table-td px-6 text-right">
                                    @if ((int) $producto->unidades_vendidas >= 10)
                                        <span class="inline-flex rounded-full bg-success-100 px-3 py-1 text-xs font-medium text-success-700">Alta rotacion</span>
                                    @elseif ((int) $producto->unidades_vendidas > 0)
                                        <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700">Movimiento medio</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-surface-soft px-3 py-1 text-xs font-medium text-ink-soft">Aun sin ventas</span>
                                    @endif
                                    <p class="mt-2 text-xs text-slate-500">
                                        @if ($producto->ultima_venta)
                                            Ultima venta {{ \Illuminate\Support\Carbon::parse($producto->ultima_venta)->format('d/m/Y') }}
                                        @else
                                            Sin ventas registradas
                                        @endif
                                    </p>
                                </td>
                                <td class="wayna-table-td px-6 text-right font-mono-data text-primary-600">Bs {{ number_format((float) $producto->precio, 2) }}</td>
                                <td class="wayna-table-td px-6 text-right text-ink">{{ $producto->stock }}</td>
                                <td class="wayna-table-td px-6">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $producto->estado_disponibilidad === 'agotado' ? 'bg-danger-100 text-danger-700' : ($producto->estado_disponibilidad === 'ultimas_unidades' ? 'bg-accent-50 text-accent-700' : 'bg-primary-50 text-primary-700') }}">
                                        {{ str_replace('_', ' ', $producto->estado_disponibilidad) }}
                                    </span>
                                </td>
                                <td class="wayna-table-td px-6">
                                    <div class="flex justify-end gap-2">
                                        <x-admin.action-button wire:click="abrirModalProducto({{ $producto->id }})" icon="edit" label="Editar producto" tone="primary" />
                                        <x-admin.action-button wire:click="confirmarEliminarProducto({{ $producto->id }})" icon="delete" label="Eliminar producto" tone="danger" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12">
                                    <div class="rounded-3xl border border-dashed border-stroke bg-surface-soft px-6 py-10 text-center">
                                        <p class="font-display text-2xl text-ink">Aun no tienes productos publicados</p>
                                        <p class="mt-2 text-sm text-ink-soft">Crea tu primer producto para empezar a poblar el catalogo con tus piezas activas.</p>
                                        <button type="button" wire:click="abrirModalProducto" class="wayna-btn-primary mt-5">
                                            Crear primer producto
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-stroke-soft px-6 py-4">
                {{ $productos->links(data: ['scrollTo' => false]) }}
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-[1.75rem] border border-[#d8d2de] bg-white p-6">
                <div>
                    <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Lectura comercial</p>
                    <h2 class="mt-2 font-display text-2xl text-slate-900">Productos que necesitan accion</h2>
                    <p class="mt-2 text-sm text-slate-600">Usa este bloque para detectar piezas que aun no venden o que necesitan reposicion antes de afectar la operacion.</p>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($productosSinVentas as $producto)
                        <article class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-medium text-slate-900">{{ $producto->nombre }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">Producto visible aun sin pedidos cerrados.</p>
                                </div>
                                <span class="rounded-full bg-surface-soft px-3 py-1 text-xs font-medium text-ink-soft">
                                    Sin ventas
                                </span>
                            </div>
                            <div class="mt-4 text-xs text-slate-500">Stock actual: {{ $producto->stock }}</div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-[#d8d2de] bg-[#fcfbfe] px-5 py-8 text-center">
                            <p class="font-display text-xl text-slate-900">Todos tus productos ya tuvieron movimiento</p>
                            <p class="mt-2 text-sm text-slate-600">Este bloque resaltara automaticamente las piezas que aun necesiten traccion comercial.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-[#d8d2de] bg-[#f7f2fb] p-6">
                <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Stock critico</p>
                <h2 class="mt-2 font-display text-2xl text-slate-900">Revisa estas piezas primero</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($productosStockCritico as $producto)
                        <article class="rounded-2xl border border-[#ebe6ef] bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-medium text-slate-900">{{ $producto->nombre }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">Estado {{ str_replace('_', ' ', $producto->estado_stock) }}</p>
                                </div>
                                <span class="rounded-full bg-accent-50 px-3 py-1 text-xs font-medium text-accent-700">
                                    Stock {{ $producto->stock }}
                                </span>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-[#d8d2de] bg-white px-5 py-8 text-center">
                            <p class="font-display text-xl text-slate-900">Sin alertas de inventario</p>
                            <p class="mt-2 text-sm text-slate-600">No hay piezas con riesgo inmediato de reposicion.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-[#d8d2de] bg-[#f7f2fb] p-6">
                <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Cuenta empresaria</p>
                <h2 class="mt-2 font-display text-2xl text-slate-900">{{ $perfil->nombre_emprendimiento }}</h2>
                <p class="mt-2 text-sm text-slate-600">La historia, branding, checklist y ubicacion del negocio ahora se gestionan desde una seccion propia del panel.</p>
                <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="mt-4 inline-flex rounded-2xl bg-white px-4 py-3 text-sm font-medium text-[#5f4cae] transition hover:text-[#4a3597]">
                    Editar cuenta empresaria
                </a>
            </section>
        </aside>
    </div>

    @if ($mostrarModalProducto)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[1.75rem] border border-[#d8d2de] bg-white">
                <div class="flex items-center justify-between border-b border-[#ebe6ef] px-6 py-5">
                    <div>
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Producto</p>
                        <h3 class="mt-2 font-display text-3xl text-slate-900">{{ $productoIdEditando ? 'Editar producto' : 'Nuevo producto artesanal' }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarModalProducto" class="rounded-full border border-[#d8d2de] px-3 py-2 text-sm text-slate-600 hover:border-[#5f4cae] hover:text-[#5f4cae]">Cerrar</button>
                </div>

                <div class="grid gap-6 px-6 py-6 lg:grid-cols-2">
                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Nombre</label>
                        <input wire:model.live="productoNombre" type="text" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Nombre del producto">
                        <x-input-error :messages="$errors->get('productoNombre')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Categoria</label>
                        <select wire:model.live="productoCategoriaId" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="">Sin categoria</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('productoCategoriaId')" class="mt-2" />
                    </div>

                    <div class="lg:col-span-2">
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Descripcion</label>
                        <textarea wire:model.live="productoDescripcion" rows="4" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="Cuenta la historia de la pieza, materiales o tecnica."></textarea>
                        <x-input-error :messages="$errors->get('productoDescripcion')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Precio</label>
                        <input wire:model.live="productoPrecio" type="number" step="0.01" min="0" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="0.00">
                        <x-input-error :messages="$errors->get('productoPrecio')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Stock</label>
                        <input wire:model.live="productoStock" type="number" min="0" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0" placeholder="0">
                        <x-input-error :messages="$errors->get('productoStock')" class="mt-2" />
                    </div>

                    <div>
                        <label class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Disponibilidad</label>
                        <select wire:model.live="productoEstadoDisponibilidad" class="mt-2 w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#5f4cae] focus:ring-0">
                            <option value="disponible">Disponible</option>
                            <option value="ultimas_unidades">Ultimas unidades</option>
                            <option value="agotado">Agotado</option>
                        </select>
                        <x-input-error :messages="$errors->get('productoEstadoDisponibilidad')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-4">
                        <span class="material-symbols-outlined text-[#5f4cae]">check_circle</span>
                        <label class="text-sm text-slate-700">Los productos nuevos se publican como activos dentro de tu catalogo.</label>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#ebe6ef] px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cerrarModalProducto" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">Cancelar</button>
                    <button type="button" wire:click="guardarProducto" class="rounded-2xl bg-[#5f4cae] px-4 py-3 text-sm font-medium text-white transition hover:opacity-90">{{ $productoIdEditando ? 'Guardar cambios' : 'Crear producto' }}</button>
                </div>
            </div>
        </div>
    @endif

    @if ($productoIdEliminar)
        <div class="fixed inset-0 z-[75] flex items-center justify-center bg-slate-950/30 px-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-[1.5rem] border border-[#d8d2de] bg-white p-6">
                <h3 class="font-display text-2xl text-slate-900">Eliminar producto</h3>
                <p class="mt-2 text-sm text-slate-600">Esta accion quitara el producto de tu catalogo visible. Podras volver a crearlo despues si lo necesitas.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('productoIdEliminar', null)" class="rounded-2xl border border-[#d8d2de] px-4 py-3 text-sm font-medium text-slate-700">Cancelar</button>
                    <button type="button" wire:click="eliminarProducto" class="rounded-2xl bg-[#a03f29] px-4 py-3 text-sm font-medium text-white">Eliminar</button>
                </div>
            </div>
        </div>
    @endif

</div>
