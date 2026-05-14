<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportPdfExportController extends Controller
{
    public function __invoke(Request $request, AdminReportService $reportService): Response
    {
        $reporte = $reportService->build([
            'preset' => (string) $request->string('preset', '30d'),
            'desde' => $request->string('desde')->toString() ?: null,
            'hasta' => $request->string('hasta')->toString() ?: null,
            'emprendedor_id' => $request->string('emprendedor_id')->toString() ?: null,
            'categoria_id' => $request->string('categoria_id')->toString() ?: null,
            'metodo_pago' => $request->string('metodo_pago')->toString() ?: null,
            'estado_transaccion' => $request->string('estado_transaccion')->toString() ?: null,
            'tipo_transaccion' => $request->string('tipo_transaccion')->toString() ?: null,
        ]);

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'reporte' => $reporte,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('wayna-reportes.pdf');
    }
}
