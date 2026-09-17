<?php

namespace App\Services\Bot;

use App\Models\Location;
use Illuminate\Support\Str;

class SicoesLocationResolver
{
    private const UNSPECIFIED = 'No especificado';

    public function resolve(array $analysis, array $document = []): array
    {
        $workplace = is_array($analysis['lugar_trabajo'] ?? null)
            ? $analysis['lugar_trabajo']
            : [];
        $exactAddress = $this->text($workplace['direccion_exacta'] ?? '');
        $municipalityText = $this->text($workplace['municipio'] ?? '');
        $departmentText = $this->text($workplace['departamento'] ?? '');
        $evidence = $this->text($workplace['evidencia'] ?? '');
        $documentSource = $this->text($workplace['documento_fuente'] ?? $document['filename'] ?? '');
        $confidence = $this->confidence($workplace['confianza'] ?? 0);
        $discardedCandidates = $this->candidates($workplace['direcciones_candidatas_descartadas'] ?? []);
        $reviewReasons = [];
        $source = 'document_workplace';

        if ($evidence !== '' && $this->isExcludedAddressEvidence($evidence)) {
            $reviewReasons[] = 'La única evidencia de ubicación corresponde a entrega, consultas, reunión o domicilio legal; no demuestra el lugar de trabajo.';
            $exactAddress = '';
            $municipalityText = '';
            $departmentText = '';
            $source = 'excluded_non_work_address';
        }

        if ($evidence === '' && ($exactAddress !== '' || $municipalityText !== '' || $departmentText !== '')) {
            $reviewReasons[] = 'La ubicación detectada no conserva evidencia textual del lugar de trabajo.';
        }

        if ($confidence < (float) config('sicoes.location.automatic_confidence', 0.75)) {
            $reviewReasons[] = 'La confianza de la ubicación del lugar de trabajo es insuficiente.';
        }

        if ((bool) ($workplace['requiere_revision'] ?? false)) {
            $reviewReasons[] = 'Claude marcó la ubicación para revisión manual.';
        }

        $municipality = $this->findMunicipality($municipalityText)
            ?: $this->findMunicipality($exactAddress);
        $department = $municipality['department'] ?? $this->findDepartment($departmentText);

        if (! $department) {
            $structuredMunicipality = $this->structuredProcessMunicipality($document);
            $municipality = $structuredMunicipality
                ? $this->findMunicipality($structuredMunicipality)
                : null;
            $department = $municipality['department'] ?? $this->structuredProcessDepartment($document);
            $source = $municipality || $department ? 'sicoes_process_structured_data' : $source;
        }

        if ($municipality) {
            $municipalityText = $municipality['municipality'];
            $department = $municipality['department'];
        } else {
            $municipalityText = '';
        }

        $department = $department ?: self::UNSPECIFIED;
        if ($department === self::UNSPECIFIED) {
            $reviewReasons[] = 'No se pudo determinar el departamento del lugar de trabajo.';
        }

        $locationIds = $this->locationIds($department);
        if ($department !== self::UNSPECIFIED && $locationIds === []) {
            $reviewReasons[] = "El departamento {$department} no existe en el catálogo de ubicaciones.";
        }

        $unclassifiedCandidateKeys = collect($discardedCandidates)
            ->filter(fn (array $candidate): bool => in_array(
                $this->normalize((string) ($candidate['tipo'] ?? '')),
                ['', 'otra'],
                true,
            ))
            ->map(fn (array $candidate): string => $this->normalize(implode(' ', array_filter([
                $candidate['direccion_exacta'] ?? null,
                $candidate['municipio'] ?? null,
                $candidate['departamento'] ?? null,
            ]))))
            ->filter()
            ->unique();

        if ($unclassifiedCandidateKeys->count() > 1) {
            $reviewReasons[] = 'El documento contiene varias direcciones candidatas y requiere confirmar cuál corresponde al lugar de trabajo.';
        }

        $reviewReasons = array_values(array_unique($reviewReasons));
        $normalizedText = $this->normalizedLocationText($exactAddress, $municipalityText, $department);

        return [
            'direccion_exacta' => $exactAddress,
            'municipio' => $municipalityText ?: null,
            'departamento' => $department,
            'texto_normalizado' => $normalizedText,
            'selected_location_ids' => $locationIds,
            'evidencia' => $evidence,
            'documento_fuente' => $documentSource,
            'confianza' => $confidence,
            'fuente_resolucion' => $source,
            'direcciones_candidatas_descartadas' => $discardedCandidates,
            'requiere_revision' => $reviewReasons !== [],
            'motivos_revision' => $reviewReasons,
        ];
    }

    private function structuredProcessMunicipality(array $document): string
    {
        foreach ([
            'municipio',
            'municipality',
            'lugarPrestacion',
            'lugar_prestacion',
            'municipioEjecucion',
            'municipio_ejecucion',
        ] as $key) {
            $value = $this->text(data_get($document, "row.{$key}", ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function structuredProcessDepartment(array $document): ?string
    {
        foreach ([
            'departamento',
            'department',
            'departamentoEjecucion',
            'departamento_ejecucion',
        ] as $key) {
            $value = $this->text(data_get($document, "row.{$key}", ''));
            if ($department = $this->findDepartment($value)) {
                return $department;
            }
        }

        return null;
    }

    private function findMunicipality(string $value): ?array
    {
        $needle = $this->normalize($value);
        if ($needle === '' || $needle === $this->normalize(self::UNSPECIFIED)) {
            return null;
        }

        $catalog = config('bolivia_municipalities', []);
        $matches = [];

        foreach ($catalog as $municipality => $department) {
            $normalizedMunicipality = $this->normalize((string) $municipality);
            if (
                $needle === $normalizedMunicipality
                || (
                    mb_strlen($normalizedMunicipality) >= 4
                    && preg_match('/\b'.preg_quote($normalizedMunicipality, '/').'\b/u', $needle) === 1
                )
            ) {
                $matches[$normalizedMunicipality] = [
                    'municipality' => $this->displayName((string) $municipality),
                    'department' => (string) $department,
                ];
            }
        }

        if ($matches === []) {
            return null;
        }

        uasort($matches, fn (array $left, array $right): int => mb_strlen($right['municipality']) <=> mb_strlen($left['municipality']));

        return array_values($matches)[0];
    }

    private function findDepartment(string $value): ?string
    {
        $needle = $this->normalize($value);
        if ($needle === '' || $needle === $this->normalize(self::UNSPECIFIED)) {
            return null;
        }

        foreach (array_values(array_unique(array_values(config('bolivia_municipalities', [])))) as $department) {
            $normalized = $this->normalize((string) $department);
            if (
                $needle === $normalized
                || preg_match('/\b'.preg_quote($normalized, '/').'\b/u', $needle) === 1
            ) {
                return (string) $department;
            }
        }

        return null;
    }

    private function locationIds(string $department): array
    {
        if ($department === self::UNSPECIFIED) {
            $department = self::UNSPECIFIED;
        }

        $normalized = $this->normalize($department);

        return Location::query()
            ->get(['id', 'location_name'])
            ->filter(fn (Location $location): bool => $this->normalize($location->location_name) === $normalized)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    private function normalizedLocationText(string $address, string $municipality, string $department): string
    {
        if ($address !== '') {
            $parts = [$address];
            $normalizedAddress = $this->normalize($address);

            if ($municipality !== '' && ! Str::contains($normalizedAddress, $this->normalize($municipality))) {
                $parts[] = $municipality;
            }

            if (
                $department !== self::UNSPECIFIED
                && ! Str::contains($normalizedAddress, $this->normalize($department))
            ) {
                $parts[] = $department;
            }

            return implode(', ', array_values(array_unique($parts)));
        }

        if ($municipality !== '' && $department !== self::UNSPECIFIED) {
            return "{$municipality}, {$department}";
        }

        if ($municipality !== '') {
            return $municipality;
        }

        return $department !== self::UNSPECIFIED ? "Departamento de {$department}" : '';
    }

    private function isExcludedAddressEvidence(string $evidence): bool
    {
        $normalized = $this->normalize($evidence);
        $workSignals = [
            'lugar de trabajo',
            'lugar de funciones',
            'sede de funciones',
            'destino del cargo',
            'prestara sus servicios',
            'prestacion del servicio',
            'desempenara funciones',
        ];

        if (Str::contains($normalized, $workSignals)) {
            return false;
        }

        return Str::contains($normalized, [
            'presentacion de propuestas',
            'entrega de propuestas',
            'entregar propuestas',
            'propuestas seran entregadas',
            'propuestas entregadas',
            'lugar de entrega',
            'consultas escritas',
            'recibir consultas',
            'consultas se recibiran',
            'domicilio legal',
            'reunion de aclaracion',
            'direccion de contratacion',
        ]);
    }

    private function candidates(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn (mixed $candidate): bool => is_array($candidate))
            ->map(fn (array $candidate): array => [
                'direccion_exacta' => $this->text($candidate['direccion_exacta'] ?? ''),
                'municipio' => $this->text($candidate['municipio'] ?? ''),
                'departamento' => $this->text($candidate['departamento'] ?? ''),
                'tipo' => $this->text($candidate['tipo'] ?? ''),
                'evidencia' => $this->text($candidate['evidencia'] ?? ''),
                'motivo_descarte' => $this->text($candidate['motivo_descarte'] ?? ''),
            ])
            ->filter(fn (array $candidate): bool => collect($candidate)->contains(fn (string $value): bool => $value !== ''))
            ->values()
            ->all();
    }

    private function confidence(mixed $value): float
    {
        return is_numeric($value)
            ? round(max(0.0, min(1.0, (float) $value)), 4)
            : 0.0;
    }

    private function displayName(string $value): string
    {
        $particles = ['de', 'del', 'la', 'las', 'los', 'y', 'el'];

        return collect(preg_split('/\s+/', Str::headline($value)) ?: [])
            ->filter()
            ->values()
            ->map(function (string $word, int $index) use ($particles): string {
                $lower = Str::lower($word);

                return $index > 0 && in_array($lower, $particles, true) ? $lower : $word;
            })
            ->implode(' ');
    }

    private function text(mixed $value): string
    {
        return is_scalar($value) ? trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8')) : '';
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }
}
