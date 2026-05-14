<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    public function build(array $filters): array
    {
        [$desde, $hasta] = $this->resolverRango(
            $filters['preset'] ?? '30d',
            $filters['desde'] ?? null,
            $filters['hasta'] ?? null,
        );

        $filtros = [
            'emprendedor_id' => ! empty($filters['emprendedor_id']) ? (int) $filters['emprendedor_id'] : null,
            'categoria_id' => ! empty($filters['categoria_id']) ? (int) $filters['categoria_id'] : null,
            'metodo_pago' => $filters['metodo_pago'] ?? null,
            'estado_transaccion' => $filters['estado_transaccion'] ?? null,
            'tipo_transaccion' => $filters['tipo_transaccion'] ?? null,
        ];

        $granularidad = $this->resolverGranularidad($desde, $hasta);
        $ventasBrutas = $this->ventasBrutas($desde, $hasta, $filtros);
        $donaciones = $this->donaciones($desde, $hasta, $filtros);
        $ingresoPlataforma = $this->ingresoPlataformaEstimado($ventasBrutas, $donaciones);
        $flujoTotal = $ventasBrutas + $donaciones;
        $pedidos = $this->pedidosValidos($desde, $hasta, $filtros);
        $ticketPromedio = $pedidos > 0 ? $ventasBrutas / $pedidos : 0;
        $transacciones = $this->resumenTransacciones($desde, $hasta, $filtros);
        $series = $this->series($desde, $hasta, $granularidad, $filtros);

        [$desdePrevio, $hastaPrevio] = $this->resolverPeriodoPrevio($desde, $hasta);

        $ventasPrevias = $this->ventasBrutas($desdePrevio, $hastaPrevio, $filtros);
        $donacionesPrevias = $this->donaciones($desdePrevio, $hastaPrevio, $filtros);
        $ingresoPrevio = $this->ingresoPlataformaEstimado($ventasPrevias, $donacionesPrevias);
        $flujoPrevio = $ventasPrevias + $donacionesPrevias;
        $ticketPrevio = ($pedidosPrevios = $this->pedidosValidos($desdePrevio, $hastaPrevio, $filtros)) > 0
            ? $ventasPrevias / $pedidosPrevios
            : 0;
        $tasaPrevia = $this->resumenTransacciones($desdePrevio, $hastaPrevio, $filtros)['tasa_exito'];

        return [
            'rango' => [
                'desde' => $desde,
                'hasta' => $hasta,
                'granularidad' => $granularidad,
                'previo_desde' => $desdePrevio,
                'previo_hasta' => $hastaPrevio,
            ],
            'kpis' => [
                'ventas_brutas' => $ventasBrutas,
                'donaciones' => $donaciones,
                'ingreso_plataforma' => $ingresoPlataforma,
                'flujo_total' => $flujoTotal,
                'ticket_promedio' => $ticketPromedio,
                'tasa_exito' => $transacciones['tasa_exito'],
                'pedidos' => $pedidos,
                'transacciones_completadas' => $transacciones['completadas'],
            ],
            'comparativas' => [
                'ventas_brutas' => $this->comparativa($ventasBrutas, $ventasPrevias),
                'donaciones' => $this->comparativa($donaciones, $donacionesPrevias),
                'ingreso_plataforma' => $this->comparativa($ingresoPlataforma, $ingresoPrevio),
                'flujo_total' => $this->comparativa($flujoTotal, $flujoPrevio),
                'ticket_promedio' => $this->comparativa($ticketPromedio, $ticketPrevio),
                'tasa_exito' => $this->comparativa($transacciones['tasa_exito'], $tasaPrevia),
            ],
            'series' => $series,
            'top_emprendedores' => $this->topEmprendedores($desde, $hasta, $filtros),
            'top_productos' => $this->topProductos($desde, $hasta, $filtros),
            'metodos_pago' => $this->metodosPago($desde, $hasta, $filtros),
            'estados_transaccion' => $this->estadosTransaccion($desde, $hasta, $filtros),
        ];
    }

    private function resolverPeriodoPrevio(Carbon $desde, Carbon $hasta): array
    {
        $duracion = $desde->diffInDays($hasta) + 1;
        $finPrevio = $desde->copy()->subDay()->endOfDay();
        $inicioPrevio = $finPrevio->copy()->subDays($duracion - 1)->startOfDay();

        return [$inicioPrevio, $finPrevio];
    }

    private function resolverRango(string $preset, ?string $desde, ?string $hasta): array
    {
        if ($desde && $hasta) {
            $inicio = Carbon::parse($desde)->startOfDay();
            $fin = Carbon::parse($hasta)->endOfDay();

            if ($inicio->lte($fin)) {
                return [$inicio, $fin];
            }
        }

        $ahora = now();

        return match ($preset) {
            'today' => [$ahora->copy()->startOfDay(), $ahora->copy()->endOfDay()],
            '7d' => [$ahora->copy()->subDays(6)->startOfDay(), $ahora->copy()->endOfDay()],
            '90d' => [$ahora->copy()->subDays(89)->startOfDay(), $ahora->copy()->endOfDay()],
            '6m' => [$ahora->copy()->subMonths(6)->startOfDay(), $ahora->copy()->endOfDay()],
            '1y' => [$ahora->copy()->subYear()->startOfDay(), $ahora->copy()->endOfDay()],
            'all' => [$this->primeraFechaConDatos(), $ahora->copy()->endOfDay()],
            default => [$ahora->copy()->subDays(29)->startOfDay(), $ahora->copy()->endOfDay()],
        };
    }

    private function resolverGranularidad(Carbon $desde, Carbon $hasta): string
    {
        $dias = $desde->diffInDays($hasta) + 1;

        if ($dias <= 31) {
            return 'day';
        }

        if ($dias <= 180) {
            return 'week';
        }

        return 'month';
    }

    private function primeraFechaConDatos(): Carbon
    {
        $fechas = collect([
            DB::table('pedidos')->min('created_at'),
            DB::table('donaciones')->min('created_at'),
            DB::table('transacciones')->min('created_at'),
        ])->filter();

        return $fechas->isEmpty()
            ? now()->startOfMonth()
            : Carbon::parse($fechas->min())->startOfDay();
    }

    private function ventasBrutas(Carbon $desde, Carbon $hasta, array $filtros): float
    {
        if ($filtros['categoria_id']) {
            return (float) $this->pedidoItemsBaseQuery($desde, $hasta, $filtros)->sum('pedido_items.subtotal');
        }

        return (float) DB::table('pedidos')
            ->whereIn('estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('emprendedor_id', $emprendedorId))
            ->sum('total');
    }

    private function donaciones(Carbon $desde, Carbon $hasta, array $filtros): float
    {
        return (float) DB::table('donaciones')
            ->leftJoin('emprendedores', 'emprendedores.id', '=', 'donaciones.emprendedor_id')
            ->whereBetween('donaciones.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('donaciones.emprendedor_id', $emprendedorId))
            ->when($filtros['categoria_id'], fn ($query, $categoriaId) => $query->where('emprendedores.categoria_id', $categoriaId))
            ->sum('monto');
    }

    private function ingresoPlataformaEstimado(float $ventasBrutas, float $donaciones): float
    {
        return ($ventasBrutas * config('reporting.platform_fees.sales'))
            + ($donaciones * config('reporting.platform_fees.donations'));
    }

    private function pedidosValidos(Carbon $desde, Carbon $hasta, array $filtros): int
    {
        if ($filtros['categoria_id']) {
            return (int) $this->pedidoItemsBaseQuery($desde, $hasta, $filtros)
                ->distinct('pedidos.id')
                ->count('pedidos.id');
        }

        return (int) DB::table('pedidos')
            ->whereIn('estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('emprendedor_id', $emprendedorId))
            ->count();
    }

    private function resumenTransacciones(Carbon $desde, Carbon $hasta, array $filtros): array
    {
        $totales = $this->transaccionesBaseQuery($desde, $hasta, $filtros)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $completadas = (int) ($totales['completada'] ?? 0);
        $base = (int) collect($totales)->sum();

        return [
            'completadas' => $completadas,
            'tasa_exito' => $base > 0 ? round(($completadas / $base) * 100, 1) : 0,
        ];
    }

    private function series(Carbon $desde, Carbon $hasta, string $granularidad, array $filtros): Collection
    {
        $buckets = $this->buckets($desde, $hasta, $granularidad);
        $ventas = $this->serieVentas($desde, $hasta, $granularidad, $filtros);
        $donaciones = $this->serieDonaciones($desde, $hasta, $granularidad, $filtros);

        return $buckets->map(function (array $bucket) use ($ventas, $donaciones) {
            $ventasValor = (float) ($ventas[$bucket['key']] ?? 0);
            $donacionesValor = (float) ($donaciones[$bucket['key']] ?? 0);
            $ingresoValor = $this->ingresoPlataformaEstimado($ventasValor, $donacionesValor);

            return [
                'key' => $bucket['key'],
                'label' => $bucket['label'],
                'ventas' => $ventasValor,
                'donaciones' => $donacionesValor,
                'ingreso' => $ingresoValor,
                'flujo' => $ventasValor + $donacionesValor,
            ];
        });
    }

    private function buckets(Carbon $desde, Carbon $hasta, string $granularidad): Collection
    {
        $inicio = match ($granularidad) {
            'week' => $desde->copy()->startOfWeek(),
            'month' => $desde->copy()->startOfMonth(),
            default => $desde->copy()->startOfDay(),
        };

        $fin = match ($granularidad) {
            'week' => $hasta->copy()->startOfWeek(),
            'month' => $hasta->copy()->startOfMonth(),
            default => $hasta->copy()->startOfDay(),
        };

        $intervalo = match ($granularidad) {
            'week' => '1 week',
            'month' => '1 month',
            default => '1 day',
        };

        return collect(CarbonPeriod::create($inicio, $intervalo, $fin))->map(function (Carbon $fecha) use ($granularidad) {
            return [
                'key' => $this->bucketKey($fecha, $granularidad),
                'label' => match ($granularidad) {
                    'week' => $fecha->format('d M'),
                    'month' => $fecha->translatedFormat('M y'),
                    default => $fecha->format('d M'),
                },
            ];
        });
    }

    private function serieVentas(Carbon $desde, Carbon $hasta, string $granularidad, array $filtros): Collection
    {
        [$groupExpr, $keyExpr] = $this->bucketSql($granularidad, 'pedidos.created_at');

        if ($filtros['categoria_id']) {
            return $this->pedidoItemsBaseQuery($desde, $hasta, $filtros)
                ->selectRaw("$groupExpr as bucket, COALESCE(SUM(pedido_items.subtotal), 0) as total")
                ->groupByRaw($keyExpr)
                ->orderBy('bucket')
                ->pluck('total', 'bucket');
        }

        return DB::table('pedidos')
            ->selectRaw("$groupExpr as bucket, COALESCE(SUM(total), 0) as total")
            ->whereIn('estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('emprendedor_id', $emprendedorId))
            ->groupByRaw($keyExpr)
            ->orderBy('bucket')
            ->pluck('total', 'bucket');
    }

    private function serieDonaciones(Carbon $desde, Carbon $hasta, string $granularidad, array $filtros): Collection
    {
        [$groupExpr, $keyExpr] = $this->bucketSql($granularidad, 'donaciones.created_at');

        return DB::table('donaciones')
            ->leftJoin('emprendedores', 'emprendedores.id', '=', 'donaciones.emprendedor_id')
            ->selectRaw("$groupExpr as bucket, COALESCE(SUM(monto), 0) as total")
            ->whereBetween('donaciones.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('donaciones.emprendedor_id', $emprendedorId))
            ->when($filtros['categoria_id'], fn ($query, $categoriaId) => $query->where('emprendedores.categoria_id', $categoriaId))
            ->groupByRaw($keyExpr)
            ->orderBy('bucket')
            ->pluck('total', 'bucket');
    }

    private function bucketSql(string $granularidad, string $columnaFecha): array
    {
        return match ($granularidad) {
            'week' => [
                "to_char(date_trunc('week', $columnaFecha), 'YYYY-MM-DD')",
                "date_trunc('week', $columnaFecha)",
            ],
            'month' => [
                "to_char(date_trunc('month', $columnaFecha), 'YYYY-MM-DD')",
                "date_trunc('month', $columnaFecha)",
            ],
            default => [
                "to_char(date_trunc('day', $columnaFecha), 'YYYY-MM-DD')",
                "date_trunc('day', $columnaFecha)",
            ],
        };
    }

    private function bucketKey(Carbon $fecha, string $granularidad): string
    {
        return match ($granularidad) {
            'week' => $fecha->copy()->startOfWeek()->format('Y-m-d'),
            'month' => $fecha->copy()->startOfMonth()->format('Y-m-d'),
            default => $fecha->copy()->startOfDay()->format('Y-m-d'),
        };
    }

    private function topEmprendedores(Carbon $desde, Carbon $hasta, array $filtros): Collection
    {
        return DB::table('pedidos')
            ->join('emprendedores', 'emprendedores.id', '=', 'pedidos.emprendedor_id')
            ->join('usuarios', 'usuarios.id', '=', 'emprendedores.usuario_id')
            ->whereIn('pedidos.estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('pedidos.emprendedor_id', $emprendedorId))
            ->when($filtros['categoria_id'], fn ($query, $categoriaId) => $query->where('emprendedores.categoria_id', $categoriaId))
            ->groupBy('emprendedores.id', 'emprendedores.nombre_negocio', 'usuarios.nombre_completo')
            ->orderByDesc(DB::raw('SUM(pedidos.total)'))
            ->limit(5)
            ->get([
                'emprendedores.id',
                'emprendedores.nombre_negocio',
                'usuarios.nombre_completo as responsable',
                DB::raw('COUNT(pedidos.id) as pedidos'),
                DB::raw('SUM(pedidos.total) as ventas'),
            ]);
    }

    private function topProductos(Carbon $desde, Carbon $hasta, array $filtros): Collection
    {
        return DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'productos.id', '=', 'pedido_items.producto_id')
            ->whereIn('pedidos.estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('pedidos.emprendedor_id', $emprendedorId))
            ->when($filtros['categoria_id'], fn ($query, $categoriaId) => $query->where('productos.categoria_id', $categoriaId))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc(DB::raw('SUM(pedido_items.subtotal)'))
            ->limit(5)
            ->get([
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(pedido_items.cantidad) as unidades'),
                DB::raw('SUM(pedido_items.subtotal) as ventas'),
            ]);
    }

    private function metodosPago(Carbon $desde, Carbon $hasta, array $filtros): Collection
    {
        return $this->transaccionesBaseQuery($desde, $hasta, $filtros)
            ->selectRaw('metodo_pago, COUNT(*) as total, COALESCE(SUM(monto), 0) as monto')
            ->groupBy('metodo_pago')
            ->orderByDesc('total')
            ->get();
    }

    private function estadosTransaccion(Carbon $desde, Carbon $hasta, array $filtros): Collection
    {
        return $this->transaccionesBaseQuery($desde, $hasta, $filtros)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderByDesc('total')
            ->get();
    }

    private function pedidoItemsBaseQuery(Carbon $desde, Carbon $hasta, array $filtros)
    {
        return DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'productos.id', '=', 'pedido_items.producto_id')
            ->whereIn('pedidos.estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
            ->when($filtros['emprendedor_id'], fn ($query, $emprendedorId) => $query->where('pedidos.emprendedor_id', $emprendedorId))
            ->when($filtros['categoria_id'], fn ($query, $categoriaId) => $query->where('productos.categoria_id', $categoriaId));
    }

    private function transaccionesBaseQuery(Carbon $desde, Carbon $hasta, array $filtros)
    {
        return DB::table('transacciones')
            ->whereBetween('transacciones.created_at', [$desde, $hasta])
            ->when($filtros['metodo_pago'], fn ($query, $metodo) => $query->where('transacciones.metodo_pago', $metodo))
            ->when($filtros['estado_transaccion'], fn ($query, $estado) => $query->where('transacciones.estado', $estado))
            ->when($filtros['tipo_transaccion'], fn ($query, $tipo) => $query->where('transacciones.referencia_tipo', $tipo));
    }

    private function comparativa(float $actual, float $previo): array
    {
        if ($previo == 0.0) {
            return [
                'actual' => $actual,
                'previo' => $previo,
                'delta' => $actual > 0 ? 100.0 : 0.0,
            ];
        }

        return [
            'actual' => $actual,
            'previo' => $previo,
            'delta' => round((($actual - $previo) / $previo) * 100, 1),
        ];
    }
}
