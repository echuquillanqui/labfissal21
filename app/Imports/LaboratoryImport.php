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
    private const HEADERS = [
        'nombres_y_apellidos' => 'patient_name',
        'hto' => 'hematocrito',
        'hb' => 'hemoglobina',
        'upre' => 'urea_pre',
        'upost' => 'urea_post',
        'cloro' => 'cloro',
        'sodio' => 'sodio',
        'potasio' => 'potasio',
        'fosforoserico' => 'fosforo',
        'calcioserico' => 'calcio_total',
        'tgo' => 'tgo',
        'tgp' => 'tgp',
    ];

    private int $imported = 0;

    private int $skipped = 0;

    private ?Collection $patients = null;

    public function collection(Collection $rows): void
    {
        $headerRow = $rows->first();

        if (! $headerRow) {
            throw new \RuntimeException('El archivo está vacío.');
        }

        $headers = $this->mapHeaders($headerRow);
        $missingHeaders = array_diff(array_keys(self::HEADERS), array_keys($headers));

        if ($missingHeaders !== []) {
            throw new \RuntimeException('Faltan cabeceras obligatorias: '.implode(', ', $missingHeaders).'.');
        }

        foreach ($rows->skip(1) as $row) {
            $data = $this->rowToData($headers, $row);
            $patientName = $data['patient_name'] ?? null;

            if ($patientName === null || trim((string) $patientName) === '') {
                continue;
            }

            $patient = $this->findPatientByName((string) $patientName);

            if (! $patient) {
                $this->skipped++;
                continue;
            }

            Laboratory::create([
                'patient_id' => $patient->id,
                'hematocrito' => $data['hematocrito'] ?? null,
                'hemoglobina' => $data['hemoglobina'] ?? null,
                'urea_pre' => $data['urea_pre'] ?? null,
                'urea_post' => $data['urea_post'] ?? null,
                'cloro' => $data['cloro'] ?? null,
                'sodio' => $data['sodio'] ?? null,
                'potasio' => $data['potasio'] ?? null,
                'fosforo' => $data['fosforo'] ?? null,
                'calcio_total' => $data['calcio_total'] ?? null,
                'tgo' => $data['tgo'] ?? null,
                'tgp' => $data['tgp'] ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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

    private function mapHeaders($row): array
    {
        $headers = [];

        foreach (collect($row)->values() as $index => $header) {
            $normalizedHeader = $this->normalizeHeader($header);

            if (array_key_exists($normalizedHeader, self::HEADERS)) {
                $headers[$normalizedHeader] = [
                    'index' => $index,
                    'field' => self::HEADERS[$normalizedHeader],
                ];
            }
        }

        return $headers;
    }

    private function rowToData(array $headers, $row): array
    {
        $data = [];
        $values = collect($row)->values();

        foreach ($headers as $header) {
            $value = $values->get($header['index']);
            $data[$header['field']] = is_string($value) ? trim($value) : $value;
        }

        return $data;
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

    private function normalizeHeader($value): string
    {
        return Str::of((string) $value)
            ->ascii()
            ->lower()
            ->squish()
            ->toString();
    }
}
