<div class="space-y-8">
    @if (session('pedidos_estado'))
        <div class="wayna-alert-success">{{ session('pedidos_estado') }}</div>
    @endif

    @if (session('pedidos_error'))
        <div class="wayna-alert-danger">{{ session('pedidos_error') }}</div>
    @endif

    <x-admin.page-header eyebrow="Operacion diaria" title="Pedidos del negocio" description="Confirma, entrega y sigue los pedidos reales de tu emprendimiento sin salir de tu panel.">
        <x-slot name="actions">
            <a href="{{ route('dashboard.emprendedor') }}" wire:navigate class="rounded-2xl border border-[#d8d2de] bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-[#5f4cae] hover:text-[#5f4cae]">
                Volver al dashboard
            </a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="receipt_long" tone="primary" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="En curso" :value="$resumen['activos']" icon="local_shipping" tone="tertiary" />
        <x-admin.kpi-card label="Ventas" :value="'Bs '.number_format($resumen['ventas'], 2)" icon="payments" tone="neutral" />
    </div>

    <x-admin.panel-card title="Seguimiento de pedidos" description="Busca por codigo o cliente y ejecuta acciones segun el estado real del pedido.">
        <x-slot name="actions">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar codigo o cliente" class="wayna-input lg:w-80">
                <select wire:model.live="filtroEstado" class="wayna-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="entregado">Entregado</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </x-slot>

        @if ($pedidos->isEmpty())
            <x-admin.empty-state title="Sin pedidos todavia" description="Cuando los compradores realicen pedidos, veras aqui el historial, las acciones pendientes y el detalle de cada compra." icon="receipt_long" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Codigo</th>
                            <th class="wayna-table-th">Cliente</th>
                            <th class="wayna-table-th">Total</th>
                            <th class="wayna-table-th">Estado</th>
                            <th class="wayna-table-th">Fecha</th>
                            <th class="wayna-table-th text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pedidos as $pedido)
                            <tr class="wayna-table-row">
                                <td class="wayna-table-td font-mono-data text-primary-600">{{ $pedido->codigo }}</td>
                                <td class="wayna-table-td">
                                    <p class="text-ink">{{ $pedido->usuario?->nombre_completo ?? 'Usuario eliminado' }}</p>
                                    <p class="mt-1 text-xs text-ink-muted">{{ $pedido->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="wayna-table-td font-mono-data text-ink">Bs {{ number_format((float) $pedido->total, 2) }}</td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="match($pedido->estado) { 'cancelado' => 'red', 'pendiente' => 'amber', 'confirmado', 'entregado', 'completado' => 'green', default => 'gray' }">
                                        {{ $pedido->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($pedido->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="wayna-table-td">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button type="button" wire:click="alternarDetalle({{ $pedido->id }})" class="rounded-xl border border-stroke px-3 py-2 text-xs text-ink-soft transition hover:border-primary-300 hover:text-primary-700">
                                            {{ $pedidoExpandidoId === $pedido->id ? 'Ocultar' : 'Detalle' }}
                                        </button>

                                        @if ($pedido->estado === 'pendiente')
                                            <button type="button" wire:click="confirmarPedido({{ $pedido->id }})" class="rounded-xl bg-primary-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-primary-600">Confirmar</button>
                                            <button type="button" wire:click="cancelarPedido({{ $pedido->id }})" class="rounded-xl bg-danger-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-danger-600">Cancelar</button>
                                        @elseif ($pedido->estado === 'confirmado')
                                            <button type="button" wire:click="marcarEntregado({{ $pedido->id }})" class="rounded-xl bg-success-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-success-600">Entregar</button>
                                            <button type="button" wire:click="cancelarPedido({{ $pedido->id }})" class="rounded-xl bg-danger-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-danger-600">Cancelar</button>
                                        @elseif ($pedido->estado === 'entregado')
                                            <button type="button" wire:click="completarPedido({{ $pedido->id }})" class="rounded-xl bg-success-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-success-600">Completar</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if ($pedidoExpandidoId === $pedido->id)
                                <tr class="wayna-table-row bg-surface-soft/60">
                                    <td colspan="6" class="px-4 py-5">
                                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1.3fr)_minmax(260px,0.7fr)]">
                                            <div class="rounded-2xl border border-stroke-soft bg-surface-raised p-4">
                                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-ink-muted">Items del pedido</p>
                                                <div class="mt-4 space-y-3">
                                                    @forelse ($pedido->items as $item)
                                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-stroke-soft bg-surface-soft px-4 py-3 text-sm">
                                                            <div>
                                                                <p class="font-medium text-ink">{{ $item->producto?->nombre ?? 'Producto eliminado' }}</p>
                                                                <p class="mt-1 text-ink-muted">{{ $item->cantidad }} unidad(es) x Bs {{ number_format((float) $item->precio_unitario, 2) }}</p>
                                                            </div>
                                                            <p class="font-mono-data text-primary-600">Bs {{ number_format((float) $item->subtotal, 2) }}</p>
                                                        </div>
                                                    @empty
                                                        <p class="text-sm text-ink-muted">Este pedido no tiene items visibles.</p>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="rounded-2xl border border-stroke-soft bg-surface-raised p-4 text-sm text-ink-soft">
                                                <p class="font-mono-data text-xs uppercase tracking-[0.24em] text-ink-muted">Contexto del pedido</p>
                                                <div class="mt-4 space-y-3">
                                                    <div>
                                                        <p class="font-medium text-ink">Metodo de entrega</p>
                                                        <p class="mt-1">{{ str_replace('_', ' ', $pedido->metodo_entrega) }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-ink">Notas</p>
                                                        <p class="mt-1">{{ $pedido->notas ?: 'Sin notas del cliente.' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $pedidos->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
