<?php

namespace Tests\Unit;

use App\Services\Bot\BotVacancyNormalizer;
use Tests\TestCase;

class BotVacancyNormalizerTest extends TestCase
{
    public function test_title_prefix_takes_precedence_over_an_unsupported_ai_location(): void
    {
        $normalized = (new BotVacancyNormalizer())->normalize(
            title: 'TJ-014-2026 OFICIAL DE MICROCREDITOS COMERCIALES (URBANO)',
            description: 'Objetivo y requisitos de la vacante.',
            analysis: [
                'department' => 'Cochabamba',
                'location' => 'Cochabamba',
            ],
        );

        $this->assertSame('Tarija', $normalized['department']);
        $this->assertSame('Tarija', $normalized['location']);
        $this->assertSame('title_prefix', $normalized['location_source']);
    }

    public function test_structured_page_location_takes_precedence_over_title_and_ai(): void
    {
        $normalized = (new BotVacancyNormalizer())->normalize(
            title: 'TJ-014-2026 OFICIAL DE MICROCREDITOS',
            description: 'Objetivo y requisitos de la vacante.',
            analysis: [
                'department' => 'Cochabamba',
                'location' => 'Cochabamba',
            ],
            rawData: [
                'page_location' => 'SANTA CRUZ DE LA SIERRA',
                'page_department' => null,
            ],
        );

        $this->assertSame('Santa Cruz', $normalized['department']);
        $this->assertSame('SANTA CRUZ DE LA SIERRA', $normalized['location']);
        $this->assertSame('page_json_ld', $normalized['location_source']);
    }

    public function test_title_municipality_is_combined_with_the_json_ld_department(): void
    {
        $normalized = (new BotVacancyNormalizer)->normalize(
            title: 'OFICIAL DE NEGOCIOS - GUAQUI',
            description: 'Objetivo y requisitos de la vacante.',
            rawData: [
                'page_location' => 'LA PAZ',
                'page_department' => null,
                'page_locations' => [[
                    'location' => 'LA PAZ',
                    'department' => null,
                ]],
            ],
        );

        $this->assertSame('La Paz', $normalized['department']);
        $this->assertSame('Guaqui', $normalized['location']);
        $this->assertSame('Guaqui', $normalized['municipality']['municipality']);
        $this->assertSame(['La Paz'], $normalized['departments']);
        $this->assertSame(
            'title_municipality_with_page_department',
            $normalized['location_source'],
        );
    }

    public function test_multiple_explicit_municipalities_keep_all_their_departments(): void
    {
        $normalized = (new BotVacancyNormalizer)->normalize(
            title: 'OFICIAL DE NEGOCIOS - GUAQUI Y SANTA CRUZ DE LA SIERRA',
            description: 'Objetivo y requisitos de la vacante.',
            rawData: [
                'page_location' => 'LA PAZ',
                'page_locations' => [
                    ['location' => 'LA PAZ', 'department' => null],
                    ['location' => 'SANTA CRUZ DE LA SIERRA', 'department' => null],
                ],
            ],
        );

        $this->assertSame('Guaqui', $normalized['location']);
        $this->assertEqualsCanonicalizing(
            ['La Paz', 'Santa Cruz'],
            $normalized['departments'],
        );
        $this->assertEqualsCanonicalizing(
            ['Guaqui', 'LA PAZ', 'Santa Cruz De La Sierra'],
            collect($normalized['municipalities'])->pluck('municipality')->all(),
        );
    }

    public function test_abbreviated_ambiguous_municipality_uses_the_json_ld_department(): void
    {
        $normalized = (new BotVacancyNormalizer)->normalize(
            title: 'ASESOR FARMACEUTICO - SAN IGNACIO',
            description: 'Objetivo y requisitos de la vacante.',
            rawData: [
                'page_location' => 'SANTA CRUZ DE LA SIERRA',
                'page_locations' => [[
                    'location' => 'SANTA CRUZ DE LA SIERRA',
                    'department' => null,
                ]],
            ],
        );

        $this->assertSame('Santa Cruz', $normalized['department']);
        $this->assertSame('San Ignacio De Velasco', $normalized['location']);
    }

    public function test_compact_municipality_variant_matches_the_official_catalog_name(): void
    {
        $normalized = (new BotVacancyNormalizer)->normalize(
            title: 'EJECUTIVO DE SERVICIOS AL CLIENTE AG VILLAMONTES',
            description: 'Objetivo y requisitos de la vacante.',
            rawData: [
                'page_location' => 'TARIJA',
                'page_locations' => [[
                    'location' => 'TARIJA',
                    'department' => null,
                ]],
            ],
        );

        $this->assertSame('Tarija', $normalized['department']);
        $this->assertSame('Villa Montes', $normalized['location']);
        $this->assertSame('title', $normalized['municipality']['source']);
    }
}
