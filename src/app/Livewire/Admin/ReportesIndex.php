<?php

namespace App\Livewire\Admin;

use App\Services\Admin\AdminReportService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ReportesIndex extends Component
{
    public string $preset = '30d';

    public string $serie = 'todo';

    public string $desde = '';

    public string $hasta = '';

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
        ]);

        $chartMax = max(
            $reporte['series']->max('ventas') ?? 0,
            $reporte['series']->max('donaciones') ?? 0,
            $reporte['series']->max('ingreso') ?? 0,
            1
        );

        return view('livewire.admin.reportes-index', [
            'reporte' => $reporte,
            'chartMax' => $chartMax,
            'presets' => [
                'today' => 'Hoy',
                '7d' => '7D',
                '30d' => '30D',
                '90d' => '3M',
                '6m' => '6M',
                '1y' => '1A',
                'all' => 'Todo',
            ],
        ])->layout('layouts.admin', [
            'pageTitle' => 'Reportes',
        ]);
    }
}
