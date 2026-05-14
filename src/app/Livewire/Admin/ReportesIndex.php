<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use App\Models\PerfilEmprendedor;
use App\Services\Admin\AdminReportService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ReportesIndex extends Component
{
    public string $preset = '30d';

    public string $serie = 'todo';

    public string $desde = '';

    public string $hasta = '';

    public string $emprendedorId = '';

    public string $categoriaId = '';

    public string $metodoPago = '';

    public string $estadoTransaccion = '';

    public string $tipoTransaccion = '';

    public function aplicarPreset(string $preset): void
    {
        $this->preset = $preset;
        $this->desde = '';
        $this->hasta = '';
    }

    public function render(AdminReportService $reportService): View
    {
        $reporte = $reportService->build([
            'preset' => $this->preset,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'emprendedor_id' => $this->emprendedorId,
            'categoria_id' => $this->categoriaId,
            'metodo_pago' => $this->metodoPago,
            'estado_transaccion' => $this->estadoTransaccion,
            'tipo_transaccion' => $this->tipoTransaccion,
        ]);

        $chartMax = match ($this->serie) {
            'ventas' => max($reporte['series']->max('ventas') ?? 0, 1),
            'donaciones' => max($reporte['series']->max('donaciones') ?? 0, 1),
            'ingreso' => max($reporte['series']->max('ingreso') ?? 0, 1),
            default => max(
                $reporte['series']->max('ventas') ?? 0,
                $reporte['series']->max('donaciones') ?? 0,
                $reporte['series']->max('ingreso') ?? 0,
                1
            ),
        };

        $comparativas = collect($reporte['comparativas'])->mapWithKeys(function (array $fila, string $key) {
            $delta = $fila['delta'];
            $prefijo = $delta > 0 ? '+' : '';

            return [$key => $prefijo.number_format($delta, 1).'% vs previo'];
        });

        return view('livewire.admin.reportes-index', [
            'reporte' => $reporte,
            'chartMax' => $chartMax,
            'comparativas' => $comparativas,
            'presets' => [
                'today' => 'Hoy',
                '7d' => '7D',
                '30d' => '30D',
                '90d' => '3M',
                '6m' => '6M',
                '1y' => '1A',
                'all' => 'Todo',
            ],
            'emprendedores' => PerfilEmprendedor::query()->orderBy('nombre_negocio')->get(['id', 'nombre_negocio']),
            'categorias' => Categoria::query()->orderBy('nombre')->get(['id', 'nombre']),
        ])->layout('layouts.admin', [
            'pageTitle' => 'Reportes',
        ]);
    }
}
