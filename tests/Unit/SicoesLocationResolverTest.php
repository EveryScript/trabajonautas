<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Services\Bot\SicoesLocationResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SicoesLocationResolverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('location_name');
            $table->timestamps();
        });

        foreach (['Beni', 'Chuquisaca', 'Cochabamba', 'La Paz', 'Oruro', 'Santa Cruz', 'Tarija', 'No especificado'] as $name) {
            Location::create(['location_name' => $name]);
        }
    }

    public function test_explicit_workplace_has_priority_and_uses_municipality_catalog(): void
    {
        $resolved = app(SicoesLocationResolver::class)->resolve([
            'lugar_trabajo' => $this->workplace([
                'direccion_exacta' => 'Oficina regional, municipio de Guaqui',
                'municipio' => 'Guaqui',
                'departamento' => '',
                'evidencia' => 'El consultor prestará sus servicios en la oficina regional de Guaqui.',
            ]),
        ]);

        $this->assertSame('Guaqui', $resolved['municipio']);
        $this->assertSame('La Paz', $resolved['departamento']);
        $this->assertSame('Oficina regional, municipio de Guaqui', $resolved['direccion_exacta']);
        $this->assertSame([Location::where('location_name', 'La Paz')->value('id')], $resolved['selected_location_ids']);
        $this->assertFalse($resolved['requiere_revision']);
    }

    public function test_delivery_and_consultation_addresses_are_not_treated_as_workplace(): void
    {
        foreach ([
            'Las propuestas serán entregadas en la calle Bolívar 123.',
            'Las consultas escritas se recibirán en la avenida Camacho 456.',
        ] as $evidence) {
            $resolved = app(SicoesLocationResolver::class)->resolve([
                'lugar_trabajo' => $this->workplace([
                    'direccion_exacta' => 'Calle Bolívar 123',
                    'municipio' => 'La Paz',
                    'departamento' => 'La Paz',
                    'evidencia' => $evidence,
                ]),
            ]);

            $this->assertSame('No especificado', $resolved['departamento']);
            $this->assertSame('', $resolved['direccion_exacta']);
            $this->assertTrue($resolved['requiere_revision']);
        }
    }

    public function test_only_municipality_and_only_department_are_kept_separate_from_exact_address(): void
    {
        $municipality = app(SicoesLocationResolver::class)->resolve([
            'lugar_trabajo' => $this->workplace([
                'municipio' => 'San Borja',
                'evidencia' => 'Lugar de funciones: municipio de San Borja.',
            ]),
        ]);
        $department = app(SicoesLocationResolver::class)->resolve([
            'lugar_trabajo' => $this->workplace([
                'departamento' => 'Oruro',
                'evidencia' => 'El servicio será prestado en el departamento de Oruro.',
            ]),
        ]);

        $this->assertSame('Beni', $municipality['departamento']);
        $this->assertSame('San Borja, Beni', $municipality['texto_normalizado']);
        $this->assertSame('', $municipality['direccion_exacta']);
        $this->assertNull($department['municipio']);
        $this->assertSame('Oruro', $department['departamento']);
        $this->assertSame('', $department['direccion_exacta']);
        $this->assertSame('Departamento de Oruro', $department['texto_normalizado']);
    }

    public function test_unspecified_or_contradictory_location_requires_review(): void
    {
        $unspecified = app(SicoesLocationResolver::class)->resolve([]);
        $contradictory = app(SicoesLocationResolver::class)->resolve([
            'lugar_trabajo' => $this->workplace([
                'direccion_exacta' => 'Sede de funciones en Trinidad',
                'municipio' => 'Trinidad',
                'departamento' => 'Beni',
                'evidencia' => 'Sede de funciones: Trinidad.',
                'direcciones_candidatas_descartadas' => [
                    [
                        'direccion_exacta' => 'Oficina de La Paz',
                        'municipio' => 'La Paz',
                        'departamento' => 'La Paz',
                        'tipo' => 'otra',
                        'evidencia' => 'También se menciona una sede en La Paz.',
                        'motivo_descarte' => 'No se pudo determinar el cargo al que corresponde.',
                    ],
                    [
                        'direccion_exacta' => 'Oficina de Santa Cruz',
                        'municipio' => 'Santa Cruz de la Sierra',
                        'departamento' => 'Santa Cruz',
                        'tipo' => 'otra',
                        'evidencia' => 'También se menciona una sede en Santa Cruz.',
                        'motivo_descarte' => 'No se pudo determinar el cargo al que corresponde.',
                    ],
                ],
            ]),
        ]);

        $this->assertSame('No especificado', $unspecified['departamento']);
        $this->assertTrue($unspecified['requiere_revision']);
        $this->assertTrue($contradictory['requiere_revision']);
        $this->assertStringContainsString(
            'varias direcciones',
            implode(' ', $contradictory['motivos_revision']),
        );
    }

    public function test_location_evidence_is_mandatory(): void
    {
        $resolved = app(SicoesLocationResolver::class)->resolve([
            'lugar_trabajo' => $this->workplace([
                'municipio' => 'Tarija',
                'departamento' => 'Tarija',
                'evidencia' => '',
            ]),
        ]);

        $this->assertTrue($resolved['requiere_revision']);
        $this->assertStringContainsString('evidencia textual', implode(' ', $resolved['motivos_revision']));
    }

    public function test_classified_delivery_addresses_do_not_make_workplace_ambiguous(): void
    {
        $resolved = app(SicoesLocationResolver::class)->resolve([
            'lugar_trabajo' => $this->workplace([
                'direccion_exacta' => 'Proyecto Miguillas, Cochabamba',
                'municipio' => '',
                'departamento' => 'Cochabamba',
                'evidencia' => 'El consultor prestará servicios en el Proyecto Miguillas, Cochabamba.',
                'direcciones_candidatas_descartadas' => [
                    [
                        'direccion_exacta' => 'Oficina central La Paz',
                        'municipio' => 'La Paz',
                        'departamento' => 'La Paz',
                        'tipo' => 'entrega_propuestas',
                        'evidencia' => 'Entrega de propuestas en La Paz.',
                        'motivo_descarte' => 'Es lugar de entrega, no de trabajo.',
                    ],
                    [
                        'direccion_exacta' => 'Oficina regional Cochabamba',
                        'municipio' => 'Cochabamba',
                        'departamento' => 'Cochabamba',
                        'tipo' => 'consultas',
                        'evidencia' => 'Consultas en la oficina regional.',
                        'motivo_descarte' => 'Es lugar de consultas, no de trabajo.',
                    ],
                ],
            ]),
        ]);

        $this->assertFalse($resolved['requiere_revision']);
        $this->assertSame('Cochabamba', $resolved['departamento']);
    }

    public function test_structured_process_municipality_is_used_only_as_fallback(): void
    {
        $resolved = app(SicoesLocationResolver::class)->resolve([], [
            'row' => ['municipio' => 'Sucre'],
        ]);

        $this->assertSame('Sucre', $resolved['municipio']);
        $this->assertSame('Chuquisaca', $resolved['departamento']);
        $this->assertSame('sicoes_process_structured_data', $resolved['fuente_resolucion']);
        $this->assertSame('', $resolved['direccion_exacta']);
    }

    private function workplace(array $overrides = []): array
    {
        return array_replace([
            'direccion_exacta' => '',
            'municipio' => '',
            'departamento' => '',
            'evidencia' => '',
            'documento_fuente' => 'Términos de Referencia',
            'confianza' => 0.98,
            'requiere_revision' => false,
            'direcciones_candidatas_descartadas' => [],
        ], $overrides);
    }
}
