<?php

namespace App\Imports;

use App\Models\Laboratory;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

class LaboratoryImport implements SkipsEmptyRows, ToCollection
{
    private int $imported = 0;

    private int $skipped = 0;

    private ?Collection $patients = null;

    public function collection(Collection $rows): void
    {
        $headerIndex = $this->findHeaderIndex($rows);

        if ($headerIndex === null) {
            throw new \RuntimeException('No se encontró la fila de cabeceras. Verifica que el archivo tenga la columna "nombres_y_apellidos".');
        }

        $headers = $this->normalizeHeaders($rows[$headerIndex]);

        foreach ($rows->slice($headerIndex + 1) as $row) {
            $data = $this->rowToData($headers, $row);
            $patientName = $this->value($data, ['nombres_y_apellidos', 'nombresapellidos', 'nombre_y_apellido', 'nombre_apellido', 'nombre', 'paciente']);

            if ($patientName === null || $patientName === '') {
                continue;
            }

            $patient = $this->findPatientByName((string) $patientName);

            if (! $patient) {
                $this->skipped++;
                continue;
            }

            Laboratory::create([
                'patient_id'   => $patient->id,
                'hematocrito'  => $this->value($data, ['hto', 'hematocrito']),
                'hemoglobina'  => $this->value($data, ['hb', 'hemoglobina']),
                'urea_pre'     => $this->value($data, ['upre', 'ureapre', 'urea_pre']),
                'urea_post'    => $this->value($data, ['upost', 'upos', 'ureapost', 'urea_post']),
                'cloro'        => $this->value($data, ['cloro']),
                'sodio'        => $this->value($data, ['sodio']),
                'potasio'      => $this->value($data, ['potasio']),
                'fosforo'      => $this->value($data, ['fosforoserico', 'fosforo_serico', 'fosforo']),
                'calcio_total' => $this->value($data, ['calcioserico', 'calcio_serico', 'calcio_total', 'calcio']),
                'tgo'          => $this->value($data, ['tgo']),
                'tgp'          => $this->value($data, ['tgp']),
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);

            $this->imported++;
        }
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    private function findHeaderIndex(Collection $rows): ?int
    {
        foreach ($rows->take(10) as $index => $row) {
            $headers = $this->normalizeHeaders($row);

            if (in_array('nombresyapellidos', $headers, true)) {
                return $index;
            }
        }

        return null;
    }

    private function normalizeHeaders($row): array
    {
        return collect($this->normalizeRow($row))
            ->map(fn ($header) => $this->normalizeKey($header))
            ->all();
    }

    private function rowToData(array $headers, $row): array
    {
        $data = [];
        $row = $this->normalizeRow($row);

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $data[$header] = $row[$index] ?? null;
        }

        return $data;
    }

    private function normalizeRow($row): array
    {
        $values = collect($row)->values()->all();
        $filledValues = array_values(array_filter($values, fn ($value) => $value !== null && $value !== ''));

        if (count($filledValues) === 1 && is_string($filledValues[0])) {
            $columns = str_getcsv($filledValues[0], ';');

            if (count($columns) === 1) {
                $columns = str_getcsv($filledValues[0], ',');
            }

            if (count($columns) === 1) {
                $columns = str_getcsv($filledValues[0], chr(9));
            }

            if (count($columns) > 1) {
                return $columns;
            }
        }

        return $values;
    }

    private function findPatientByName(string $patientName): ?Patient
    {
        $normalizedPatientName = $this->normalizePersonName($patientName);

        if ($normalizedPatientName === '') {
            return null;
        }

        $this->patients ??= Patient::all();

        return $this->patients->first(function (Patient $patient) use ($normalizedPatientName) {
            return $this->normalizePersonName($patient->name) === $normalizedPatientName;
        });
    }

    private function normalizePersonName(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }

    private function normalizeKey($value): string
    {
        return Str::of((string) $value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();
    }

    private function value(array $data, array $keys)
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);

            if (array_key_exists($normalizedKey, $data)) {
                $value = $data[$normalizedKey];

                return is_string($value) ? trim($value) : $value;
            }
        }
        return null;
    }
}