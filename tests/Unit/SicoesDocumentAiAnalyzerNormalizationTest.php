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
            'salarios' => $salary,
        ]);

        $this->assertSame($expected, $normalized['salarios']['valor']);
    }

    public static function salaryCases(): array
    {
        return [
            'not declared' => [
                ['tipo' => 'no_declarado', 'cantidad' => 0, 'detalle' => []],
                0,
            ],
            'role without declared salary' => [
                ['tipo' => 'no_declarado', 'cantidad' => 1, 'detalle' => [
                    ['cargo' => 'CONSULTOR', 'monto_bob' => 0, 'evidencia' => 'Cargo: Consultor'],
                ]],
                0,
            ],
            'single monthly salary' => [
                ['tipo' => 'unico', 'cantidad' => 1, 'detalle' => [['cargo' => 'CONTADOR', 'monto_bob' => 3045, 'evidencia' => 'Bs. 3.045,00']]],
                3045,
            ],
            'different roles with same salary' => [
                ['tipo' => 'multiple', 'cantidad' => 2, 'detalle' => [
                    ['cargo' => 'CONTADOR', 'monto_bob' => 3045, 'evidencia' => 'Bs. 3.045,00'],
                    ['cargo' => 'AUXILIAR', 'monto_bob' => 3045, 'evidencia' => 'Bs. 3.045,00'],
                ]],
                1,
            ],
            'several vacancies of the same role' => [
                ['tipo' => 'unico', 'cantidad' => 5, 'detalle' => [['cargo' => '5 CONSULTORES DE CAMPO', 'monto_bob' => 4567, 'evidencia' => 'Bs. 4.567,00']]],
                4567,
            ],
            'different salaries' => [
                ['tipo' => 'multiple', 'cantidad' => 2, 'detalle' => [
                    ['cargo' => 'CONTADOR', 'monto_bob' => 3045, 'evidencia' => 'Bs. 3.045,00'],
                    ['cargo' => 'AUXILIAR', 'monto_bob' => 2800, 'evidencia' => 'Bs. 2.800,00'],
                ]],
                1,
            ],
        ];
    }
}
