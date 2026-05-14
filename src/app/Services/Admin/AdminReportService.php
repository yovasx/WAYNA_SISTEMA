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

        $granularidad = $this->resolverGranularidad($desde, $hasta);
        $ventasBrutas = $this->ventasBrutas($desde, $hasta);
        $donaciones = $this->donaciones($desde, $hasta);
        $ingresoPlataforma = $this->ingresoPlataformaEstimado($ventasBrutas, $donaciones);
        $flujoTotal = $ventasBrutas + $donaciones;
        $pedidos = $this->pedidosValidos($desde, $hasta);
        $ticketPromedio = $pedidos > 0 ? $ventasBrutas / $pedidos : 0;
        $transacciones = $this->resumenTransacciones($desde, $hasta);
        $series = $this->series($desde, $hasta, $granularidad);

        return [
            'rango' => [
                'desde' => $desde,
                'hasta' => $hasta,
                'granularidad' => $granularidad,
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
            'series' => $series,
            'top_emprendedores' => $this->topEmprendedores($desde, $hasta),
            'top_productos' => $this->topProductos($desde, $hasta),
            'metodos_pago' => $this->metodosPago($desde, $hasta),
            'estados_transaccion' => $this->estadosTransaccion($desde, $hasta),
        ];
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

    private function ventasBrutas(Carbon $desde, Carbon $hasta): float
    {
        return (float) DB::table('pedidos')
            ->whereIn('estado', config('reporting.sales_states'))
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('total');
    }

    private function donaciones(Carbon $desde, Carbon $hasta): float
    {
        return (float) DB::table('donaciones')
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('monto');
    }

    private function ingresoPlataformaEstimado(float $ventasBrutas, float $donaciones): float
    {
        return ($ventasBrutas * config('reporting.platform_fees.sales'))
            + ($donaciones * config('reporting.platform_fees.donations'));
    }

    private function pedidosValidos(Carbon $desde, Carbon $hasta): int
    {
        return (int) DB::table('pedidos')
            ->whereIn('estado', config('reporting.sales_states'))
            ->whereBetween('created_at', [$desde, $hasta])
            ->count();
    }

    private function resumenTransacciones(Carbon $desde, Carbon $hasta): array
    {
        $totales = DB::table('transacciones')
            ->selectRaw('estado, COUNT(*) as total')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $completadas = (int) ($totales['completada'] ?? 0);
        $base = (int) collect($totales)->sum();

        return [
            'completadas' => $completadas,
            'tasa_exito' => $base > 0 ? round(($completadas / $base) * 100, 1) : 0,
        ];
    }

    private function series(Carbon $desde, Carbon $hasta, string $granularidad): Collection
    {
        $buckets = $this->buckets($desde, $hasta, $granularidad);
        $ventas = $this->serieVentas($desde, $hasta, $granularidad);
        $donaciones = $this->serieDonaciones($desde, $hasta, $granularidad);

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

    private function serieVentas(Carbon $desde, Carbon $hasta, string $granularidad): Collection
    {
        [$groupExpr, $keyExpr] = $this->bucketSql($granularidad);

        return DB::table('pedidos')
            ->selectRaw("$groupExpr as bucket, COALESCE(SUM(total), 0) as total")
            ->whereIn('estado', config('reporting.sales_states'))
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupByRaw($keyExpr)
            ->orderBy('bucket')
            ->pluck('total', 'bucket');
    }

    private function serieDonaciones(Carbon $desde, Carbon $hasta, string $granularidad): Collection
    {
        [$groupExpr, $keyExpr] = $this->bucketSql($granularidad);

        return DB::table('donaciones')
            ->selectRaw("$groupExpr as bucket, COALESCE(SUM(monto), 0) as total")
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupByRaw($keyExpr)
            ->orderBy('bucket')
            ->pluck('total', 'bucket');
    }

    private function bucketSql(string $granularidad): array
    {
        return match ($granularidad) {
            'week' => [
                "to_char(date_trunc('week', created_at), 'YYYY-MM-DD')",
                "date_trunc('week', created_at)",
            ],
            'month' => [
                "to_char(date_trunc('month', created_at), 'YYYY-MM-DD')",
                "date_trunc('month', created_at)",
            ],
            default => [
                "to_char(date_trunc('day', created_at), 'YYYY-MM-DD')",
                "date_trunc('day', created_at)",
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

    private function topEmprendedores(Carbon $desde, Carbon $hasta): Collection
    {
        return DB::table('pedidos')
            ->join('emprendedores', 'emprendedores.id', '=', 'pedidos.emprendedor_id')
            ->join('usuarios', 'usuarios.id', '=', 'emprendedores.usuario_id')
            ->whereIn('pedidos.estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
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

    private function topProductos(Carbon $desde, Carbon $hasta): Collection
    {
        return DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'productos.id', '=', 'pedido_items.producto_id')
            ->whereIn('pedidos.estado', config('reporting.sales_states'))
            ->whereBetween('pedidos.created_at', [$desde, $hasta])
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

    private function metodosPago(Carbon $desde, Carbon $hasta): Collection
    {
        return DB::table('transacciones')
            ->selectRaw('metodo_pago, COUNT(*) as total, COALESCE(SUM(monto), 0) as monto')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('metodo_pago')
            ->orderByDesc('total')
            ->get();
    }

    private function estadosTransaccion(Carbon $desde, Carbon $hasta): Collection
    {
        return DB::table('transacciones')
            ->selectRaw('estado, COUNT(*) as total')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('estado')
            ->orderByDesc('total')
            ->get();
    }
}
