<?php

namespace Tests\Unit;

use App\Services\Bot\SicoesDocumentAiAnalyzer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SicoesDocumentAiAnalyzerNormalizationTest extends TestCase
{
    #[DataProvider('salaryCases')]
    public function test_salary_uses_storage_contract(array $salary, int $expected): void
    {
        $method = new ReflectionMethod(SicoesDocumentAiAnalyzer::class, 'normalizeResponse');
        $method->setAccessible(true);
        $normalized = $method->invoke(new SicoesDocumentAiAnalyzer, [
            'eligible' => true,
            'contract_type' => 'individual',
            'es_oportunidad_consultor_persona' => true,
            'tipo_oportunidad' => 'consultor_linea',
            'debe_descartarse' => false,
            'sueldo' => $salary,
        ]);

        $this->assertSame($expected, $normalized['sueldo']['valor']);
    }

    public static function salaryCases(): array
    {
        return [
            'not declared' => [
                ['valor' => 0, 'sueldos' => []],
                0,
            ],
            'single monthly salary' => [
                ['valor' => 3045, 'sueldos' => [['cargo' => 'CONTADOR', 'monto' => 'Bs. 3.045,00']]],
                3045,
            ],
            'different roles with same salary' => [
                ['valor' => 3045, 'sueldos' => [
                    ['cargo' => 'CONTADOR', 'monto' => 'Bs. 3.045,00'],
                    ['cargo' => 'AUXILIAR', 'monto' => 'Bs. 3.045,00'],
                ]],
                1,
            ],
            'several vacancies of the same role' => [
                ['valor' => 4567, 'sueldos' => [['cargo' => '5 CONSULTORES DE CAMPO', 'monto' => 'Bs. 4.567,00']]],
                4567,
            ],
            'different salaries' => [
                ['valor' => 2, 'sueldos' => [
                    ['cargo' => 'CONTADOR', 'monto' => 'Bs. 3.045,00'],
                    ['cargo' => 'AUXILIAR', 'monto' => 'Bs. 2.800,00'],
                ]],
                1,
            ],
        ];
    }
}
