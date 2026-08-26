<?php

namespace App\Services\Bot;

use Illuminate\Support\Str;

class SicoesDescriptionBuilder
{
    public function build(array $data): string
    {
        $location = is_array($data['location'] ?? null) ? $data['location'] : [];
        $duration = is_array($data['duration'] ?? null) ? $data['duration'] : [];
        $modality = is_array($data['modality'] ?? null) ? $data['modality'] : [];
        $salary = is_array($data['salary'] ?? null) ? $data['salary'] : [];
        $attachments = collect(is_array($data['attachments'] ?? null) ? $data['attachments'] : [])
            ->filter(fn (mixed $attachment): bool => is_array($attachment) && trim((string) ($attachment['url'] ?? '')) !== '')
            ->values();
        $parts = [];

        $this->appendSection($parts, 'LUGAR DEL TRABAJO', $location['texto_normalizado'] ?? $location['direccion_exacta'] ?? null);
        $this->appendSection($parts, 'DURACIÓN DEL CONTRATO', $duration['texto_exacto'] ?? null);
        $this->appendSection($parts, 'MODALIDAD DE POSTULACIÓN', $modality['texto_exacto'] ?? null);

        if ($this->shouldAddRupePhrase($modality)) {
            $parts[] = '<p>De manera digital a través del RUPE en <a href="https://www.sicoes.gob.bo" target="_blank" rel="noopener noreferrer">www.sicoes.gob.bo</a>.</p>';
        }

        $this->appendSection($parts, $data['identifier_label'] ?? 'CUCE', $data['cuce'] ?? null);

        if ((int) ($salary['valor'] ?? 0) === 1) {
            $rows = collect($salary['detalle'] ?? [])
                ->filter(fn (mixed $row): bool => is_array($row))
                ->map(function (array $row): ?string {
                    $role = trim((string) ($row['cargo'] ?? ''));
                    $amount = (int) ($row['monto_bob'] ?? 0);

                    if ($role === '' || $amount <= 0) {
                        return null;
                    }

                    return e($role).': '.number_format($amount, 0, ',', '.').' Bs.';
                })
                ->filter()
                ->values();

            if ($rows->isNotEmpty()) {
                $parts[] = '<p><strong>DETALLE DE SUELDOS:</strong></p>';
                foreach ($rows as $row) {
                    $parts[] = '<p>'.$row.'</p>';
                }
            }
        }

        $parts[] = '<p><br></p>';
        $parts[] = '<p>¡Impulsa tu futuro profesional!</p>';
        $parts[] = '<p>Esta convocatoria fue verificada por el equipo de <strong>TRABAJONAUTAS.COM</strong> y representa una excelente oportunidad de crecimiento para ti. ¡No la dejes pasar!</p>';
        $parts[] = '<p><br></p>';
        $parts[] = '<p><strong>Fuente:</strong> <a href="https://www.sicoes.gob.bo" target="_blank" rel="noopener noreferrer">www.sicoes.gob.bo</a></p>';

        if ($attachments->count() === 1) {
            $parts[] = '<p>Descarga todos los detalles en el archivo adjunto:</p>';
        } elseif ($attachments->count() > 1) {
            $parts[] = '<p>Descarga todos los detalles en los archivos adjuntos:</p>';
        }

        return implode('', $parts);
    }

    private function appendSection(array &$parts, string $label, mixed $value): void
    {
        $value = is_scalar($value) ? trim((string) $value) : '';
        if ($value === '' || $this->normalize($value) === 'no especificado') {
            return;
        }

        if ($parts !== []) {
            $parts[] = '<p><br></p>';
        }

        $parts[] = '<p><strong>'.e($label).':</strong></p>';
        $parts[] = '<p>'.e($value).'</p>';
    }

    private function shouldAddRupePhrase(array $modality): bool
    {
        if (($modality['tipo'] ?? null) !== 'digital_rupe') {
            return false;
        }

        $text = $this->normalize((string) ($modality['texto_exacto'] ?? ''));

        return ! Str::contains($text, ['www sicoes gob bo', 'a traves del rupe']);
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
