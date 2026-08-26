<?php

namespace Tests\Unit;

use App\Services\Bot\SicoesDescriptionBuilder;
use PHPUnit\Framework\TestCase;

class SicoesDescriptionBuilderTest extends TestCase
{
    public function test_builds_rupe_description_with_multiple_salaries_and_one_attachment(): void
    {
        $description = (new SicoesDescriptionBuilder)->build([
            'location' => ['texto_normalizado' => 'Guaqui, La Paz'],
            'duration' => ['texto_exacto' => 'Seis meses'],
            'modality' => [
                'texto_exacto' => 'Presentación electrónica.',
                'tipo' => 'digital_rupe',
            ],
            'cuce' => '26-0000-00-0000001-1-1',
            'salary' => [
                'valor' => 1,
                'detalle' => [
                    ['cargo' => 'Coordinador', 'monto_bob' => 14402],
                    ['cargo' => 'Especialista Social', 'monto_bob' => 12788],
                ],
            ],
            'attachments' => [['url' => 'convocatorias/sicoes/documento.docx']],
        ]);

        $this->assertStringContainsString('Guaqui, La Paz', $description);
        $this->assertStringContainsString('DURACIÓN DEL CONTRATO', $description);
        $this->assertStringContainsString('De manera digital a través del RUPE', $description);
        $this->assertSame(1, substr_count($description, 'De manera digital a través del RUPE'));
        $this->assertStringContainsString('DETALLE DE SUELDOS', $description);
        $this->assertStringContainsString('Coordinador: 14.402 Bs.', $description);
        $this->assertStringContainsString('Especialista Social: 12.788 Bs.', $description);
        $this->assertLessThan(
            strpos($description, 'Especialista Social'),
            strpos($description, 'Coordinador'),
        );
        $this->assertStringContainsString('en el archivo adjunto', $description);
        $this->assertStringNotContainsString('en los archivos adjuntos', $description);
    }

    public function test_physical_or_unspecified_modality_does_not_add_rupe_or_empty_sections(): void
    {
        $physical = (new SicoesDescriptionBuilder)->build([
            'location' => ['texto_normalizado' => 'Departamento de Oruro'],
            'duration' => ['texto_exacto' => ''],
            'modality' => [
                'texto_exacto' => 'Entrega física en sobre cerrado.',
                'tipo' => 'fisica',
            ],
            'cuce' => '',
            'salary' => ['valor' => 0, 'detalle' => []],
            'attachments' => [],
        ]);

        $this->assertStringNotContainsString('RUPE', $physical);
        $this->assertStringNotContainsString('DURACIÓN DEL CONTRATO', $physical);
        $this->assertStringNotContainsString('CUCE:', $physical);
        $this->assertStringNotContainsString('DETALLE DE SUELDOS', $physical);
        $this->assertStringNotContainsString('archivo adjunto', $physical);
    }

    public function test_single_salary_has_no_salary_detail_and_multiple_attachments_use_plural(): void
    {
        $description = (new SicoesDescriptionBuilder)->build([
            'salary' => [
                'valor' => 6500,
                'detalle' => [['cargo' => 'Consultor', 'monto_bob' => 6500]],
            ],
            'attachments' => [
                ['url' => 'uno.docx'],
                ['url' => 'dos.pdf'],
            ],
        ]);

        $this->assertStringNotContainsString('DETALLE DE SUELDOS', $description);
        $this->assertStringContainsString('en los archivos adjuntos', $description);
        $this->assertStringContainsString('¡Impulsa tu futuro profesional!', $description);
    }

    public function test_rupe_phrase_is_not_duplicated_when_exact_modality_already_contains_it(): void
    {
        $description = (new SicoesDescriptionBuilder)->build([
            'modality' => [
                'texto_exacto' => 'De manera digital a través del RUPE en www.sicoes.gob.bo.',
                'tipo' => 'digital_rupe',
            ],
        ]);

        $this->assertSame(1, substr_count($description, 'De manera digital a través del RUPE'));
    }

    public function test_non_rupe_modalities_never_add_the_rupe_phrase(): void
    {
        foreach (['digital_otro', 'fisica', 'correo', 'mixta', 'no_especificada'] as $type) {
            $description = (new SicoesDescriptionBuilder)->build([
                'modality' => [
                    'texto_exacto' => "Modalidad {$type}.",
                    'tipo' => $type,
                ],
            ]);

            $this->assertStringNotContainsString('De manera digital a través del RUPE', $description);
        }
    }
}
