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
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar usuario, QR o referencia" class="w-full rounded-2xl border border-[#d8d2de] bg-[#fcfbfe] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#5f4cae] focus:ring-0">

                <select wire:model.live="filtroTipo" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los tipos</option>
                    <option value="pedido">Pedido</option>
                    <option value="donacion">Donacion</option>
                    <option value="reserva">Reserva</option>
                </select>

                <select wire:model.live="filtroMetodo" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
                    <option value="">Todos los metodos</option>
                    <option value="qr">QR</option>
                    <option value="nfc">NFC</option>
                    <option value="microtransaccion">Microtransaccion</option>
                </select>

                <select wire:model.live="filtroEstado" class="rounded-2xl border border-[#d8d2de] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#5f4cae] focus:ring-0">
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
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#ebe6ef] bg-[#fcfbfe]">
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Operacion</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Usuario</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Metodo</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Monto</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Estado</th>
                            <th class="px-4 py-4 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">Fecha</th>
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

                            <tr class="border-b border-[#f0ecf4] align-top last:border-b-0">
                                <td class="px-4 py-5">
                                    <p class="font-medium text-slate-900">{{ $tituloReferencia }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ strtoupper($transaccion->referencia_tipo) }} · ID {{ $transaccion->id }}</p>
                                </td>
                                <td class="px-4 py-5">
                                    <p class="text-sm text-slate-700">{{ $transaccion->usuario?->nombre_completo ?? 'Usuario no disponible' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $transaccion->usuario?->email ?? 'Sin correo' }}</p>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-700">{{ str_replace('_', ' ', $transaccion->metodo_pago) }}</td>
                                <td class="px-4 py-5 font-mono-data text-sm text-[#5f4cae]">Bs {{ number_format((float) $transaccion->monto, 2) }}</td>
                                <td class="px-4 py-5">
                                    <x-admin.status-badge :tone="$transaccion->estado === 'fallida' ? 'red' : ($transaccion->estado === 'pendiente' ? 'amber' : ($transaccion->estado === 'reembolsada' ? 'gray' : 'green'))">
                                        {{ $transaccion->estado }}
                                    </x-admin.status-badge>
                                </td>
                                <td class="px-4 py-5 text-sm text-slate-500">{{ optional($transaccion->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $transacciones->links() }}</div>
        @endif
    </x-admin.panel-card>
</div>
