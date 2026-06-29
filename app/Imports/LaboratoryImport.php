<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\Laboratory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class LaboratoryImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 5; // Las cabeceras del reporte FISSAL inician en la fila 5
    }

    public function model(array $row)
    {
        // Validación: Asegurar que exista la columna 'id' (o 'patient_id') en la fila del Excel
        if (!isset($row['id']) || empty(trim($row['id']))) {
            return null;
        }

        $idExcel = trim($row['id']);

        // Buscar el paciente directamente por su ID de la base de datos
        $paciente = Patient::find($idExcel);

        // Si el paciente existe, registramos sus exámenes de laboratorio
        if ($paciente) {
            return new Laboratory([
                'patient_id'         => $paciente->id,
                
                // Hematología
                'hematocrito'        => $row['hto'] ?? null,
                'hemoglobina'        => $row['hb'] ?? null,
                
                // Química Sanguínea, Electrolitos y Transaminasas
                'urea_pre'           => $row['upre'] ?? null,
                'urea_post'          => $row['upost'] ?? null,
                'cloro'              => $row['cloro'] ?? null,
                'sodio'              => $row['sodio'] ?? null,
                'potasio'            => $row['potasio'] ?? null,
                'fosforo'            => $row['fosforoserico'] ?? null,
                'calcio_total'       => $row['calcioserico'] ?? null,
                'tgo'                => $row['tgo'] ?? null,
                'tgp'                => $row['tgp'] ?? null,
                
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ]);
        }

        return null; 
    }
}