<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Laboratorio Consolidado</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta { font-size: 11px; margin-bottom: 15px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #777; padding: 4px 2px; text-align: center; font-size: 9px; }
        th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; }
        .text-left { text-align: left; padding-left: 5px; font-weight: bold; }
        .footer { position: fixed; bottom: -10px; width: 100%; text-align: right; font-size: 8px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Reporte General de Laboratorio FISSAL</h2>
    </div>

    <div class="meta">
        <strong>Fecha de Carga de Bloque:</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}<br>
        <strong>Cantidad de Pacientes Evaluados:</strong> {{ $reports->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="25%">Paciente</th>
                <th width="10%">DNI</th>
                <th>HB</th>
                <th>HTO</th>
                <th>U. Pre</th>
                <th>U. Post</th>
                <th>Na</th>
                <th>Cl</th>
                <th>K</th>
                <th>Ca</th>
                <th>P</th>
                <th>TGO</th>
                <th>TGP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td class="text-left">{{ $report->patient->name }}</td>
                <td>{{ $report->patient->dni }}</td>
                <td>{{ $report->hemoglobina ?? '-' }}</td>
                <td>{{ $report->hematocrito ? $report->hematocrito.'%' : '-' }}</td>
                <td>{{ $report->urea_pre ?? '-' }}</td>
                <td>{{ $report->urea_post ?? '-' }}</td>
                <td>{{ $report->sodio ?? '-' }}</td>
                <td>{{ $report->cloro ?? '-' }}</td>
                <td>{{ $report->potasio ?? '-' }}</td>
                <td>{{ $report->calcio_total ?? '-' }}</td>
                <td>{{ $report->fosforo ?? '-' }}</td>
                <td>{{ $report->tgo ?? '-' }}</td>
                <td>{{ $report->tgp ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Impresión de Control Clínico Automático - Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>