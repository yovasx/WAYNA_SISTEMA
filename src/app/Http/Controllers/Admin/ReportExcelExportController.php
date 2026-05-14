<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminReportExport;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExcelExportController extends Controller
{
    public function __invoke(Request $request, AdminReportService $reportService): BinaryFileResponse
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

        return Excel::download(new AdminReportExport($reporte), 'wayna-reportes.xlsx');
    }
}
