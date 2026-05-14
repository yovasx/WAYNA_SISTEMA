<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AdminReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $reporte,
    ) {}

    public function sheets(): array
    {
        $rango = $this->reporte['rango'];
        $kpis = $this->reporte['kpis'];
        $comparativas = $this->reporte['comparativas'];

        return [
            new AdminReportSheet('Resumen', [
                ['Metrica', 'Valor actual', 'Periodo previo', 'Variacion %'],
                ['Ventas brutas', $kpis['ventas_brutas'], $comparativas['ventas_brutas']['previo'], $comparativas['ventas_brutas']['delta']],
                ['Donaciones', $kpis['donaciones'], $comparativas['donaciones']['previo'], $comparativas['donaciones']['delta']],
                ['Ingreso plataforma', $kpis['ingreso_plataforma'], $comparativas['ingreso_plataforma']['previo'], $comparativas['ingreso_plataforma']['delta']],
                ['Flujo total', $kpis['flujo_total'], $comparativas['flujo_total']['previo'], $comparativas['flujo_total']['delta']],
                ['Ticket promedio', $kpis['ticket_promedio'], $comparativas['ticket_promedio']['previo'], $comparativas['ticket_promedio']['delta']],
                ['Tasa de exito', $kpis['tasa_exito'], $comparativas['tasa_exito']['previo'], $comparativas['tasa_exito']['delta']],
                [],
                ['Rango actual', $rango['desde']->format('Y-m-d'), $rango['hasta']->format('Y-m-d'), ''],
                ['Rango previo', $rango['previo_desde']->format('Y-m-d'), $rango['previo_hasta']->format('Y-m-d'), ''],
            ]),
            new AdminReportSheet('Series', array_merge(
                [['Periodo', 'Ventas brutas', 'Donaciones', 'Ingreso plataforma', 'Flujo total']],
                $this->reporte['series']->map(fn (array $fila) => [
                    $fila['label'],
                    $fila['ventas'],
                    $fila['donaciones'],
                    $fila['ingreso'],
                    $fila['flujo'],
                ])->all()
            )),
            new AdminReportSheet('Top emprendedores', array_merge(
                [['Emprendedor', 'Responsable', 'Pedidos', 'Ventas']],
                collect($this->reporte['top_emprendedores'])->map(fn ($fila) => [
                    $fila->nombre_negocio,
                    $fila->responsable,
                    $fila->pedidos,
                    $fila->ventas,
                ])->all()
            )),
            new AdminReportSheet('Top productos', array_merge(
                [['Producto', 'Unidades', 'Ventas']],
                collect($this->reporte['top_productos'])->map(fn ($fila) => [
                    $fila->nombre,
                    $fila->unidades,
                    $fila->ventas,
                ])->all()
            )),
            new AdminReportSheet('Metodos de pago', array_merge(
                [['Metodo', 'Cantidad', 'Monto']],
                collect($this->reporte['metodos_pago'])->map(fn ($fila) => [
                    $fila->metodo_pago,
                    $fila->total,
                    $fila->monto,
                ])->all()
            )),
            new AdminReportSheet('Estados transaccion', array_merge(
                [['Estado', 'Cantidad']],
                collect($this->reporte['estados_transaccion'])->map(fn ($fila) => [
                    $fila->estado,
                    $fila->total,
                ])->all()
            )),
        ];
    }
}
