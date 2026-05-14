<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Reporte WAYNA</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                color: #1e293b;
                font-size: 12px;
                margin: 24px;
            }

            h1, h2, h3 {
                margin: 0;
            }

            .header {
                margin-bottom: 20px;
            }

            .muted {
                color: #64748b;
            }

            .grid {
                width: 100%;
                border-collapse: separate;
                border-spacing: 10px;
                margin-bottom: 18px;
            }

            .card {
                border: 1px solid #d8d2de;
                border-radius: 14px;
                padding: 12px;
                background: #fcfbfe;
                vertical-align: top;
            }

            .label {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: #64748b;
            }

            .value {
                font-size: 22px;
                font-weight: bold;
                margin-top: 6px;
            }

            .helper {
                margin-top: 6px;
                font-size: 10px;
                color: #64748b;
            }

            table.report {
                width: 100%;
                border-collapse: collapse;
                margin-top: 12px;
                margin-bottom: 20px;
            }

            table.report th,
            table.report td {
                border: 1px solid #e7e0f1;
                padding: 8px;
                text-align: left;
            }

            table.report th {
                background: #f7f2fb;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.1em;
            }

            .section {
                margin-top: 20px;
            }

            .legend {
                margin-top: 8px;
                margin-bottom: 10px;
            }

            .legend-item {
                display: inline-block;
                margin-right: 18px;
                font-size: 10px;
                color: #475569;
            }

            .legend-dot {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 2px;
                margin-right: 6px;
                vertical-align: middle;
            }

            .chart-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .chart-table td {
                padding: 8px 6px;
                border-bottom: 1px solid #ede8f1;
                vertical-align: middle;
            }

            .chart-track {
                width: 100%;
                height: 10px;
                background: #f1ecf5;
                border-radius: 999px;
                overflow: hidden;
            }

            .chart-bar {
                height: 10px;
                border-radius: 999px;
            }

            .metric-cell {
                width: 29%;
            }

            .period-cell {
                width: 13%;
                font-weight: bold;
                color: #1e293b;
            }

            .numeric {
                margin-top: 4px;
                font-size: 10px;
                color: #475569;
            }
        </style>
    </head>
    <body>
        @php
            $chartRows = $reporte['series']->take(8)->values();
            $chartMax = max(
                $chartRows->max('ventas') ?? 0,
                $chartRows->max('donaciones') ?? 0,
                $chartRows->max('ingreso') ?? 0,
                1
            );
        @endphp

        <div class="header">
            <h1>Reporte ejecutivo WAYNA</h1>
            <p class="muted">
                Rango actual: {{ $reporte['rango']['desde']->format('d/m/Y') }} - {{ $reporte['rango']['hasta']->format('d/m/Y') }}
                | Rango previo: {{ $reporte['rango']['previo_desde']->format('d/m/Y') }} - {{ $reporte['rango']['previo_hasta']->format('d/m/Y') }}
            </p>
        </div>

        <table class="grid">
            <tr>
                <td class="card">
                    <div class="label">Ventas brutas</div>
                    <div class="value">Bs {{ number_format($reporte['kpis']['ventas_brutas'], 2) }}</div>
                    <div class="helper">{{ number_format($reporte['comparativas']['ventas_brutas']['delta'], 1) }}% vs previo</div>
                </td>
                <td class="card">
                    <div class="label">Donaciones</div>
                    <div class="value">Bs {{ number_format($reporte['kpis']['donaciones'], 2) }}</div>
                    <div class="helper">{{ number_format($reporte['comparativas']['donaciones']['delta'], 1) }}% vs previo</div>
                </td>
                <td class="card">
                    <div class="label">Ingreso plataforma</div>
                    <div class="value">Bs {{ number_format($reporte['kpis']['ingreso_plataforma'], 2) }}</div>
                    <div class="helper">Estimado · {{ number_format($reporte['comparativas']['ingreso_plataforma']['delta'], 1) }}% vs previo</div>
                </td>
                <td class="card">
                    <div class="label">Flujo total</div>
                    <div class="value">Bs {{ number_format($reporte['kpis']['flujo_total'], 2) }}</div>
                    <div class="helper">{{ number_format($reporte['comparativas']['flujo_total']['delta'], 1) }}% vs previo</div>
                </td>
            </tr>
        </table>

        <div class="section">
            <h2>Grafico ejecutivo del periodo</h2>
            <div class="legend">
                <span class="legend-item"><span class="legend-dot" style="background:#5f4cae;"></span>Ventas</span>
                <span class="legend-item"><span class="legend-dot" style="background:#a03f29;"></span>Donaciones</span>
                <span class="legend-item"><span class="legend-dot" style="background:#1f6b52;"></span>Ingreso plataforma</span>
            </div>

            <table class="chart-table">
                @foreach ($chartRows as $fila)
                    @php
                        $ventasWidth = $fila['ventas'] > 0 ? max(($fila['ventas'] / $chartMax) * 100, 4) : 0;
                        $donacionesWidth = $fila['donaciones'] > 0 ? max(($fila['donaciones'] / $chartMax) * 100, 4) : 0;
                        $ingresoWidth = $fila['ingreso'] > 0 ? max(($fila['ingreso'] / $chartMax) * 100, 4) : 0;
                    @endphp
                    <tr>
                        <td class="period-cell">{{ $fila['label'] }}</td>
                        <td class="metric-cell">
                            <div class="chart-track">
                                <div class="chart-bar" style="width: {{ $ventasWidth }}%; background: #5f4cae;"></div>
                            </div>
                            <div class="numeric">Ventas: Bs {{ number_format($fila['ventas'], 2) }}</div>
                        </td>
                        <td class="metric-cell">
                            <div class="chart-track">
                                <div class="chart-bar" style="width: {{ $donacionesWidth }}%; background: #a03f29;"></div>
                            </div>
                            <div class="numeric">Donaciones: Bs {{ number_format($fila['donaciones'], 2) }}</div>
                        </td>
                        <td class="metric-cell">
                            <div class="chart-track">
                                <div class="chart-bar" style="width: {{ $ingresoWidth }}%; background: #1f6b52;"></div>
                            </div>
                            <div class="numeric">Ingreso: Bs {{ number_format($fila['ingreso'], 2) }}</div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="section">
            <h2>Resumen por periodo</h2>
            <table class="report">
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Ventas</th>
                        <th>Donaciones</th>
                        <th>Ingreso plataforma</th>
                        <th>Flujo total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reporte['series'] as $fila)
                        <tr>
                            <td>{{ $fila['label'] }}</td>
                            <td>Bs {{ number_format($fila['ventas'], 2) }}</td>
                            <td>Bs {{ number_format($fila['donaciones'], 2) }}</td>
                            <td>Bs {{ number_format($fila['ingreso'], 2) }}</td>
                            <td>Bs {{ number_format($fila['flujo'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Top emprendedores</h2>
            <table class="report">
                <thead>
                    <tr>
                        <th>Emprendedor</th>
                        <th>Responsable</th>
                        <th>Pedidos</th>
                        <th>Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reporte['top_emprendedores'] as $fila)
                        <tr>
                            <td>{{ $fila->nombre_negocio }}</td>
                            <td>{{ $fila->responsable }}</td>
                            <td>{{ $fila->pedidos }}</td>
                            <td>Bs {{ number_format((float) $fila->ventas, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Top productos</h2>
            <table class="report">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Unidades</th>
                        <th>Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reporte['top_productos'] as $fila)
                        <tr>
                            <td>{{ $fila->nombre }}</td>
                            <td>{{ $fila->unidades }}</td>
                            <td>Bs {{ number_format((float) $fila->ventas, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
