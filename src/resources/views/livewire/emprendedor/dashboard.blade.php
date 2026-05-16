<div class="space-y-8">
    <x-admin.page-header eyebrow="Panel emprendedor" :title="'Bienvenido, '.$perfil->nombre_emprendimiento" description="Controla pedidos, catalogo y avance comercial del negocio desde un dashboard pensado para operacion real.">
        <x-slot name="actions">
            <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="rounded-2xl bg-[#a03f29] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                Ver pedidos
            </a>
            <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white transition hover:opacity-90">
                Gestionar productos
            </a>
            <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                Perfil del negocio
            </a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="flex items-center gap-4 rounded-3xl border border-[#efc2bb] bg-[#ffdad6] px-5 py-4 text-[#93000a]">
            <span class="material-symbols-outlined">warning</span>
            <span class="text-sm">{{ $metricas['stock_bajo'] }} producto(s) con stock critico y {{ $metricas['pedidos_pendientes'] }} pedido(s) pendiente(s) de atencion.</span>
        </div>
        <div class="flex items-center gap-4 rounded-3xl border border-[#d8d2de] bg-[#e6deff] px-5 py-4 text-[#4a3597]">
            <span class="material-symbols-outlined">info</span>
            <span class="text-sm">Tu perfil emprendedor esta {{ $perfil->estado_aprobacion }} y lleva {{ $metricas['porcentaje_perfil'] }}% de preparacion comercial.</span>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Ventas del mes</p>
            <p class="mt-4 font-display text-3xl text-[#5f4cae]">Bs {{ number_format($metricas['ventas_mes'], 2) }}</p>
            <p class="mt-3 text-xs text-slate-500">Solo se cuentan pedidos confirmados, entregados o completados.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Pedidos pendientes</p>
            <p class="mt-4 font-display text-3xl text-[#a03f29]">{{ $metricas['pedidos_pendientes'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Pedidos que esperan confirmacion o accion inmediata.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Pedidos atendidos</p>
            <p class="mt-4 font-display text-3xl text-slate-900">{{ $metricas['pedidos_atendidos'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Operacion confirmada y entregada en tu negocio.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Productos activos</p>
            <p class="mt-4 font-display text-3xl text-[#745800]">{{ $metricas['productos'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Piezas visibles para consulta, compra o reserva.</p>
        </div>

        <div class="rounded-3xl border border-[#d8d2de] bg-white p-5">
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-slate-500">Perfil comercial</p>
            <p class="mt-4 font-display text-3xl text-[#1f6b52]">{{ $metricas['porcentaje_perfil'] }}%</p>
            <p class="mt-3 text-xs text-slate-500">{{ $metricas['tareas_completadas'] }} de {{ $metricas['tareas_total'] }} hitos completados.</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,0.8fr)] 2xl:grid-cols-[minmax(0,1.55fr)_minmax(340px,0.8fr)]">
        <x-admin.panel-card title="Pedidos recientes" description="Lo ultimo que paso en tu operacion comercial dentro del marketplace.">
            <x-slot name="actions">
                <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="text-sm font-medium text-primary-600 hover:underline">Ver todos</a>
            </x-slot>

            <div class="space-y-4">
                @forelse ($pedidosRecientes as $pedido)
                    <article class="rounded-3xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">{{ $pedido->codigo }}</p>
                                <h3 class="mt-2 font-display text-xl text-slate-900">{{ $pedido->usuario?->nombre_completo ?? 'Cliente sin nombre' }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $pedido->notas ?: 'Sin notas del cliente para este pedido.' }}</p>
                            </div>
                            <div class="text-right md:min-w-[180px]">
                                <p class="font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $pedido->total, 2) }}</p>
                                <div class="mt-2 flex justify-end">
                                    <x-admin.status-badge :tone="match($pedido->estado) { 'cancelado' => 'red', 'pendiente' => 'amber', 'confirmado', 'entregado', 'completado' => 'green', default => 'gray' }">
                                        {{ $pedido->estado }}
                                    </x-admin.status-badge>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-[#d8d2de] px-6 py-10 text-center text-slate-500">
                        Todavia no hay pedidos recientes para mostrar.
                    </div>
                @endforelse
            </div>
        </x-admin.panel-card>

        <aside class="space-y-6">
            <x-admin.panel-card title="Checklist del negocio" description="Marca rapido lo que ya completaste para operar y mostrar mejor tu emprendimiento.">
                <div class="space-y-3">
                    @foreach ($checklist as $paso)
                        <div class="flex items-start gap-3 rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700">
                            <span class="material-symbols-outlined mt-0.5 {{ $paso['done'] ? 'text-[#1f6b52]' : 'text-slate-400' }}">{{ $paso['done'] ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            <span>{{ $paso['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Acciones rapidas" description="Mantiene alineados pedidos, catalogo y perfil comercial.">
                <div class="space-y-3">
                    <a href="{{ route('emprendedor.pedidos.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Atender pedidos</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>

                    <a href="{{ route('emprendedor.productos.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Administrar productos</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>

                    <a href="{{ route('emprendedor.perfil.index') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                        <span>Editar perfil del negocio</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                </div>
            </x-admin.panel-card>

            <x-admin.panel-card title="Productos con atencion inmediata" description="Inventario que pide reposicion o ajuste antes de afectar la operacion.">
                <div class="space-y-3">
                    @forelse ($productosStockBajo as $producto)
                        <div class="rounded-2xl border border-[#ebe6ef] bg-[#fcfbfe] px-4 py-4 text-sm text-slate-700">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $producto->nombre }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $producto->categoria?->nombre ?? 'Sin categoria' }}</p>
                                </div>
                                <p class="font-mono-data text-[#a03f29]">Stock {{ $producto->stock }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-[#f7f2fb] px-4 py-4 text-sm text-slate-600">
                            No hay productos con urgencia de stock en este momento.
                        </div>
                    @endforelse
                </div>
            </x-admin.panel-card>
        </aside>
    </div>
</div>
