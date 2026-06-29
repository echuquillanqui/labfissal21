<?php

namespace App\Imports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PatientImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Verifica si la fila tiene datos para evitar importar filas vacías
        if (!isset($row['nombre']) || !isset($row['dni'])) {
            return null;
        }

        // Usamos updateOrCreate para evitar errores de DNI duplicado.
        // Si el DNI ya existe, actualiza el nombre; si no, crea uno nuevo.
        return Patient::updateOrCreate(
            ['dni' => $row['dni']], // Condición de búsqueda
            ['name' => $row['nombre']] // Valores a actualizar o crear
        );
    }
}