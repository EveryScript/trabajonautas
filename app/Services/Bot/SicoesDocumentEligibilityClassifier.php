<?php

namespace App\Services\Bot;

use Illuminate\Support\Str;

class SicoesDocumentEligibilityClassifier
{
    public function classify(array $document, string $text): array
    {
        $title = trim((string) ($document['title'] ?? ''));
        $normalizedTitle = $this->normalize($title);
        $normalizedText = $this->normalize($text);
        $searchable = trim($normalizedTitle.' '.$normalizedText);
        $companySignal = $this->firstSignal($searchable, [
            'contratacion de empresa',
            'empresa constructora',
            'empresa consultora',
            'empresas consultoras',
            'firma consultora',
            'firmas consultoras',
            'firma de auditoria',
            'firmas de auditoria',
            'persona juridica',
            'servicios empresariales',
            'asociacion accidental de firmas',
            'experiencia institucional de la empresa',
            'matricula de comercio vigente',
            'testimonio de constitucion de la empresa',
            'poder del representante legal',
        ]);

        $individualSignal = $this->firstSignal($searchable, [
            'consultoria individual de linea',
            'consultor individual de linea',
            'consultores individuales de linea',
            'consultoria individual por producto',
            'consultor individual por producto',
            'consultores individuales por producto',
            'servicios de consultoria individual',
            'documento base de contratacion de servicios de consultoria individual',
            'persona natural adjudicada como consultor',
            'consultoria individual',
            'consultor individual',
        ]);

        if ($individualSignal !== null) {
            if ($companySignal !== null) {
                return [
                    'decision' => 'needs_ai',
                    'eligible' => null,
                    'contract_type' => null,
                    'tipo_oportunidad' => 'no_determinado',
                    'reason' => 'El documento contiene senales contradictorias de modalidad individual y proponente empresarial.',
                    'evidence' => "Senal individual: {$individualSignal}. Senal empresarial: {$companySignal}.",
                ];
            }

            $product = str_contains($individualSignal, 'producto');
            $multiple = preg_match('/\bitem\s+1\b.*\bitem\s+2\b/s', $searchable) === 1
                || Str::contains($searchable, [
                    'consultores individuales de linea',
                    'consultores individuales por producto',
                    'varios consultores individuales',
                ]);

            return [
                'decision' => 'eligible',
                'eligible' => true,
                'contract_type' => $multiple ? 'multiple_individual' : ($product ? 'individual_product' : 'individual'),
                'tipo_oportunidad' => $product ? 'consultor_producto' : 'consultor_linea',
                'reason' => null,
                'evidence' => "Senal explicita en el documento: {$individualSignal}.",
            ];
        }

        if ($this->isGoodsProcurement($normalizedTitle)) {
            return $this->rejected(
                contractType: 'rejected_goods',
                opportunityType: 'bienes_servicios',
                reason: 'El objeto corresponde a compra, adquisicion o provision de bienes.',
                evidence: $title,
            );
        }

        if ($this->isWorksProcurement($normalizedTitle)) {
            return $this->rejected(
                contractType: 'rejected_works',
                opportunityType: 'obra',
                reason: 'El objeto corresponde a una obra o a un servicio empresarial de supervision de obra, sin modalidad individual explicita.',
                evidence: $title,
            );
        }

        if ($companySignal !== null) {
            return $this->rejected(
                contractType: 'rejected_company',
                opportunityType: 'empresa_consultora',
                reason: 'La convocatoria exige o identifica una empresa, firma consultora o persona juridica como proponente.',
                evidence: "Senal empresarial explicita: {$companySignal}.",
            );
        }

        return [
            'decision' => 'needs_ai',
            'eligible' => null,
            'contract_type' => null,
            'tipo_oportunidad' => 'no_determinado',
            'reason' => null,
            'evidence' => $title,
        ];
    }

    public function rejectedAnalysis(array $classification, array $document): array
    {
        return [
            'eligible' => false,
            'contract_type' => $classification['contract_type'],
            'es_oportunidad_consultor_persona' => false,
            'tipo_oportunidad' => $classification['tipo_oportunidad'],
            'debe_descartarse' => true,
            'motivo_descarte' => $classification['reason'],
            'evidencia_clasificacion' => $classification['evidence'],
            'cuce' => (string) ($document['cuce'] ?? ''),
            'titulo_objeto' => (string) ($document['title'] ?? ''),
            'area_ids' => [],
            'ubicacion_detectada' => [
                'texto' => '',
                'municipio' => '',
                'departamento' => '',
                'confianza' => 'no_especificado',
            ],
            'lugar_trabajo' => '',
            'duracion_contrato' => '',
            'modalidad_postulacion' => '',
            'sueldo' => [
                'valor' => 0,
                'detalle' => '',
                'sueldos' => [],
                'revision_manual' => false,
            ],
            'detalle_sueldos' => '',
            'descripcion' => '',
            'evidencias' => [
                'cuce' => '',
                'area_profesional' => '',
                'lugar_trabajo' => '',
                'duracion_contrato' => '',
                'modalidad_postulacion' => '',
                'sueldo' => '',
            ],
            'advertencias' => [],
            'preclassification' => $classification,
        ];
    }

    public function reconcile(array $analysis, array $classification): array
    {
        $analysis['preclassification'] = $classification;
        $analysis['eligible'] = ! ((bool) ($analysis['debe_descartarse'] ?? true));
        $analysis['contract_type'] = $this->contractTypeFromAnalysis($analysis);

        if ($classification['decision'] !== 'eligible') {
            return $analysis;
        }

        $type = (string) ($analysis['tipo_oportunidad'] ?? 'no_determinado');
        if (! in_array($type, ['consultor_linea', 'consultor_producto', 'consultoria_individual'], true)) {
            return $analysis;
        }

        $analysis['eligible'] = true;
        $analysis['contract_type'] = $classification['contract_type'];
        $analysis['es_oportunidad_consultor_persona'] = true;
        $analysis['debe_descartarse'] = false;
        $analysis['motivo_descarte'] = null;
        $analysis['advertencias'] = array_values(array_unique([
            ...(array) ($analysis['advertencias'] ?? []),
            'La elegibilidad se confirmo con senales explicitas de consultoria individual del documento.',
        ]));

        return $analysis;
    }

    public function contractTypeFromAnalysis(array $analysis): string
    {
        if ((bool) ($analysis['debe_descartarse'] ?? true)) {
            return match ((string) ($analysis['tipo_oportunidad'] ?? 'no_determinado')) {
                'empresa_consultora' => 'rejected_company',
                'bienes_servicios' => 'rejected_goods',
                'obra' => 'rejected_works',
                default => 'other_rejected',
            };
        }

        return match ((string) ($analysis['tipo_oportunidad'] ?? 'no_determinado')) {
            'consultor_producto' => 'individual_product',
            'consultor_linea', 'consultoria_individual' => 'individual',
            default => 'other_rejected',
        };
    }

    private function rejected(string $contractType, string $opportunityType, string $reason, string $evidence): array
    {
        return [
            'decision' => 'rejected',
            'eligible' => false,
            'contract_type' => $contractType,
            'tipo_oportunidad' => $opportunityType,
            'reason' => $reason,
            'evidence' => $evidence,
        ];
    }

    private function isGoodsProcurement(string $title): bool
    {
        return preg_match('/\b(adquisicion|compra|provision|suministro)\b.*\b(bienes|productos|materiales|medicamentos|alimentos|equipamiento|maquinaria|vehiculos)\b/', $title) === 1;
    }

    private function isWorksProcurement(string $title): bool
    {
        return preg_match('/\b(construccion|obra|mejoramiento|ampliacion|rehabilitacion)\b/', $title) === 1
            && preg_match('/\b(supervision|ejecucion|construccion|obra)\b/', $title) === 1;
    }

    private function firstSignal(string $text, array $signals): ?string
    {
        foreach ($signals as $signal) {
            if (str_contains($text, $signal)) {
                return $signal;
            }
        }

        return null;
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
