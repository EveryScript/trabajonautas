<?php

namespace Tests\Unit;

use App\Services\ProfessionNameNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfessionNameNormalizerTest extends TestCase
{
    #[DataProvider('equivalentFinancialEngineeringNames')]
    public function test_safe_financial_engineering_variants_include_the_official_name(string $input): void
    {
        $normalizer = app(ProfessionNameNormalizer::class);

        $this->assertContains('ingenieria financiera', $normalizer->variants($input));
    }

    public static function equivalentFinancialEngineeringNames(): array
    {
        return [
            ['Ingeniería Financiera'],
            ['Ingenieria Financiera'],
            ['Ing. Financiera'],
            ['Ingeniero Financiero'],
            ['Ingeniera Financiera'],
        ];
    }

    public function test_normalization_does_not_turn_an_unknown_public_management_degree_into_another_profession(): void
    {
        $normalizer = app(ProfessionNameNormalizer::class);

        $variants = $normalizer->variants('Licenciatura en Gestión Pública Municipal');

        $this->assertContains('gestion publica municipal', $variants);
        $this->assertNotContains('administracion de empresas', $variants);
    }
}
