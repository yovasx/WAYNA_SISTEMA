<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function __invoke(Request $request, AdminReportService $reportService): StreamedResponse
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

        return response()->streamDownload(function () use ($reporte) {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, ['Periodo', 'Ventas brutas', 'Donaciones', 'Ingreso plataforma', 'Flujo total']);

            foreach ($reporte['series'] as $fila) {
                fputcsv($stream, [
                    $fila['label'],
                    number_format($fila['ventas'], 2, '.', ''),
                    number_format($fila['donaciones'], 2, '.', ''),
                    number_format($fila['ingreso'], 2, '.', ''),
                    number_format($fila['flujo'], 2, '.', ''),
                ]);
            }

            fclose($stream);
        }, 'wayna-reportes.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
