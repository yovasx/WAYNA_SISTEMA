<div class="space-y-8">
    <x-admin.page-header eyebrow="Pagos y cobros" title="Transacciones" description="Monitorea cobros de pedidos, donaciones y reservas desde un solo flujo operativo." />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-admin.kpi-card label="Totales" :value="$resumen['totales']" icon="payments" tone="primary" />
        <x-admin.kpi-card label="Pendientes" :value="$resumen['pendientes']" icon="hourglass_top" tone="secondary" />
        <x-admin.kpi-card label="Completadas" :value="$resumen['completadas']" icon="check_circle" tone="tertiary" />
        <x-admin.kpi-card label="Fallidas" :value="$resumen['fallidas']" icon="error" tone="neutral" />
        <x-admin.kpi-card label="Monto" :value="'Bs '.number_format($resumen['monto_total'], 2)" icon="account_balance_wallet" tone="primary" />
    </div>

    <x-admin.panel-card title="Registro de transacciones" description="Filtra por tipo de referencia, metodo de pago o estado para auditar el movimiento del ecosistema.">
        <x-slot name="actions">
            <div class="grid w-full gap-3 lg:grid-cols-4">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar usuario, QR o referencia" class="wayna-input">

                <select wire:model.live="filtroTipo" class="wayna-select">
                    <option value="">Todos los tipos</option>
                    <option value="pedido">Pedido</option>
                    <option value="donacion">Donacion</option>
                    <option value="reserva">Reserva</option>
                </select>

                <select wire:model.live="filtroMetodo" class="wayna-select">
                    <option value="">Todos los metodos</option>
                    <option value="qr">QR</option>
                    <option value="nfc">NFC</option>
                    <option value="microtransaccion">Microtransaccion</option>
                </select>

                <select wire:model.live="filtroEstado" class="wayna-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completada">Completada</option>
                    <option value="fallida">Fallida</option>
                    <option value="reembolsada">Reembolsada</option>
                </select>
            </div>
        </x-slot>

        @if ($transacciones->isEmpty())
            <x-admin.empty-state title="Sin transacciones registradas" description="Cuando existan cobros o pagos procesados, quedaran visibles aqui con su referencia y estado." icon="payments" />
        @else
            <div class="wayna-table-wrap">
                <table class="wayna-table">
                    <thead class="wayna-table-head">
                        <tr>
                            <th class="wayna-table-th">Operacion</th>
                            <th class="wayna-table-th">Usuario</th>
                            <th class="wayna-table-th">Metodo</th>
                            <th class="wayna-table-th">Monto</th>
                            <th class="wayna-table-th">Estado</th>
                            <th class="wayna-table-th">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transacciones as $transaccion)
                            @php
                                $referencia = $transaccion->referencia;
                                $tituloReferencia = match ($transaccion->referencia_tipo) {
                                    'pedido' => $referencia?->codigo ? 'Pedido '.$referencia->codigo : 'Pedido #'.$transaccion->referencia_id,
                                    'reserva' => $referencia?->codigo ? 'Reserva '.$referencia->codigo : 'Reserva #'.$transaccion->referencia_id,
                                    default => 'Donacion #'.$transaccion->referencia_id,
                                };
                            @endphp

                            <tr class="wayna-table-row">
                                <td class="wayna-table-td">
                                    <p class="font-medium text-ink">{{ $tituloReferencia }}</p>
                                    <p class="mt-1 text-sm text-ink-soft">{{ strtoupper($transaccion->referencia_tipo) }} · ID {{ $transaccion->id }}</p>
                                </td>
                                <td class="wayna-table-td">
                                    <p class="text-sm text-ink">{{ $transaccion->usuario?->nombre_completo ?? 'Usuario no disponible' }}</p>
                                    <p class="mt-1 text-xs text-ink-muted">{{ $transaccion->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="wayna-table-td">{{ str_replace('_', ' ', $transaccion->metodo_pago) }}</td>
                                <td class="wayna-table-td font-mono-data text-primary-600">Bs {{ number_format((float) $transaccion->monto, 2) }}</td>
                                <td class="wayna-table-td">
                                    <x-admin.status-badge :tone="$transaccion->estado === 'fallida' ? 'red' : ($transaccion->estado === 'pendiente' ? 'amber' : ($transaccion->estado === 'reembolsada' ? 'gray' : 'green'))">
                                        {{ $transaccion->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="wayna-table-td text-ink-muted">{{ optional($transaccion->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $transacciones->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </x-admin.panel-card>
</div>
