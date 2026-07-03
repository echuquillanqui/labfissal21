<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Laboratorio por Paciente</title>
    <style>
        /* 1. Ajustamos el margen lateral para centrar horizontalmente */
        @page { margin: 16px 50px; } 
        
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #000; margin: 0; }
        
        /* 2. Añadimos padding-top para empujar todo hacia el centro vertical */
        .sheet { 
            page-break-after: always; 
            position: relative; 
            padding-top: 180px; /* Ajusta este valor para subir o bajar el bloque */
            padding-bottom: 180px;
        }
        .sheet:last-child { page-break-after: auto; }
        
        .title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 11px; margin: 8px 0 16px; }
        
        .patient-meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .patient-meta td { border: 0; padding: 1px 4px; font-size: 8px; vertical-align: bottom; }
        
        .label { font-weight: bold; text-transform: uppercase; white-space: nowrap; }
        .line { border-bottom: 1px solid #000 !important; text-align: center; }
        
        .section { text-align: center; font-weight: bold; text-decoration: underline; margin: 8px 0 6px; }
        
        table.results { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.results th, table.results td { border: 1px solid #000; padding: 2px 4px; font-size: 8px; line-height: 1.1; }
        table.results th { background: #9e9a9a; text-align: center; font-weight: bold; text-transform: uppercase; }
        table.results td:nth-child(1) { text-align: left; }
        table.results td:nth-child(2), table.results td:nth-child(3) { text-align: center; }
        table.results td:nth-child(4) { text-align: center; }
        
        .subhead td { background: #d4d0d0; font-weight: bold; text-align: left !important; }
        
        /* 3. Ajuste de la marca de agua para que quede en el nuevo centro */
        .watermark { position: fixed; top: 40%; left: 0; right: 0; text-align: center; color: rgba(0,0,0,0.18); font-size: 54px; z-index: -1; }
        .signature-container {
            text-align: center;
            margin-top: 40px; /* Separación de la tabla de arriba */
        }
        .signature-img {
            width: 150px; /* Ajusta este tamaño según lo necesites */
            height: auto;
        }
    </style>
</head>
    <body>
        @foreach($reports as $report)
        <div class="sheet">
            <div class="watermark">Página {{ $loop->iteration }}</div>
            <div class="title">RESULTADOS DE LABORATORIO</div>

            <table class="patient-meta">
                <tr>
                    <td class="label" style="width: 10%;">PACIENTE:</td>
                    <td class="line" style="width: 38%;">{{ mb_strtoupper($report->patient->name ?? '') }}</td>
                    <td style="width: 16%;"></td>
                    <td class="line" style="width: 18%;">{{ $report->patient->dni ?? '' }}</td>
                    <td style="width: 18%;"></td>
                </tr>

                <tr>
                    <td class="label">FECHA:</td>
                    <td class="line">{{ optional($report->created_at)->format('d/m/Y') }}</td>
                    <td class="label" style="text-align: right;">PROCEDENCIA:</td>
                    <td class="line">FISSAL</td>
                    <td></td>
                </tr>
            </table>
            
            <div class="section">AREA DE HEMATOLOGIA</div>
            <table class="results">
                <thead>
                    <tr>
                        <th style="width: 35%;">ANALISIS</th>
                        <th style="width: 20%;">RESULTADOS</th>
                        <th style="width: 13%;">UNIDAD</th>
                        <th style="width: 32%;">VALORES DE REFERENCIA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>HEMATOCRITO</td>
                        <td>{{ $report->hematocrito ?? '-' }}</td>
                        <td>%</td>
                        <td>VARONES:42.0 - 54.0 / MUJERES:37.0 - 48.0</td>
                    </tr>
                    <tr>
                        <td>HEMOGLOBINA</td>
                        <td>{{ $report->hemoglobina ?? '-' }}</td>
                        <td>g/dl</td>
                        <td>VARONES:14.0 - 18.0 / MUJERES:12.0 - 16.0</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="section" style="margin-top: 14px;">AREA DE BIOQUIMICA</div>
            <table class="results">
                <thead>
                    <tr>
                        <th style="width: 35%;">ANALISIS</th>
                        <th style="width: 20%;">RESULTADOS</th>
                        <th style="width: 13%;">UNIDAD</th>
                        <th style="width: 32%;">VALORES REFERENCIALES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>UREA PRE</td><td>{{ $report->urea_pre ?? '-' }}</td><td>mg/dl</td><td>10 - 50</td></tr>
                    <tr><td>UREA POST</td><td>{{ $report->urea_post ?? '-' }}</td><td>mg/dl</td><td>10 - 50</td></tr>
                    <tr class="subhead"><td colspan="4">Perfil de electrolitos (Cloro, Sodio y Potasio)</td></tr>
                    <tr><td>Cloro</td><td>{{ $report->cloro ?? '-' }}</td><td>mmol/L</td><td>98 - 107</td></tr>
                    <tr><td>Sodio</td><td>{{ $report->sodio ?? '-' }}</td><td>mmol/L</td><td>135 - 148</td></tr>
                    <tr><td>Potasio</td><td>{{ $report->potasio ?? '-' }}</td><td>mmol/L</td><td>3.5 - 5.3</td></tr>
                    <tr><td>Dosaje de Fósforo inorganico (fosfato)</td><td>{{ $report->fosforo ?? '-' }}</td><td>mg/dl</td><td>2.5 - 5.6</td></tr>
                    <tr><td>Dosaje de Calcio; total</td><td>{{ $report->calcio_total ?? '-' }}</td><td>mg/dl</td><td>8.8 - 10.2</td></tr>
                    <tr><td>Dosaje de transaminasa glutámico oxalacética (TGO)</td><td>{{ $report->tgo ?? '-' }}</td><td>U/L</td><td>Varones: &lt; 50 / Mujeres: &lt; 35</td></tr>
                    <tr><td>Dosaje de transaminasa glutámico pirúvica (TGP)</td><td>{{ $report->tgp ?? '-' }}</td><td>U/L</td><td>Varones: &lt; 50 / Mujeres: &lt; 36</td></tr>
                </tbody>
            </table> 
            <div class="signature-container">
                <img src="{{ public_path('storage/firma/firma.png') }}" class="signature-img" alt="Firma">
            </div>
        </div> 
        @endforeach
    </body>
</html>