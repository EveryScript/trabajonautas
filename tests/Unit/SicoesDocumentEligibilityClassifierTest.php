<?php

namespace Tests\Unit;

use App\Services\Bot\SicoesDocumentEligibilityClassifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SicoesDocumentEligibilityClassifierTest extends TestCase
{
    #[DataProvider('classificationCases')]
    public function test_classifies_sicoes_documents_before_ai(
        string $title,
        string $text,
        string $decision,
        string $contractType,
    ): void {
        $result = (new SicoesDocumentEligibilityClassifier)->classify(['title' => $title], $text);

        $this->assertSame($decision, $result['decision']);
        $this->assertSame($contractType, $result['contract_type']);
    }

    public static function classificationCases(): array
    {
        return [
            'individual line' => [
                'Consultor individual de linea',
                'Documento Base de Contratacion de Servicios de Consultoria Individual de Linea para una persona natural.',
                'eligible',
                'individual',
            ],
            'individual product' => [
                'Consultor por producto',
                'Terminos de referencia para consultoria individual por producto.',
                'eligible',
                'individual_product',
            ],
            'consulting company' => [
                'Plan de desarrollo municipal',
                'La empresa consultora debe presentar matricula de comercio vigente y poder del representante legal.',
                'rejected',
                'rejected_company',
            ],
            'goods purchase' => [
                'Adquisicion de equipamiento medico',
                'Compra de bienes para establecimientos de salud.',
                'rejected',
                'rejected_goods',
            ],
            'individuals working on a road project' => [
                'Equipo PRP Construccion Carretera Okinawa',
                'Consultoria Individual de Linea. ITEM 1 Coordinador. ITEM 2 Especialista. Persona natural adjudicada como consultor.',
                'eligible',
                'multiple_individual',
            ],
            'general individual consulting on road construction' => [
                'Consultoria Individual para proyecto de construccion de carretera',
                'La persona natural prestara servicios profesionales en el proyecto.',
                'eligible',
                'individual',
            ],
            'individual line supervising works' => [
                'Consultoria Individual de Linea para supervision de obra',
                'El consultor individual realizara la supervision tecnica.',
                'eligible',
                'individual',
            ],
            'road construction by construction company' => [
                'Construccion de carretera mediante empresa constructora',
                'Ejecucion directa de la obra.',
                'rejected',
                'rejected_works',
            ],
            'equipment acquisition' => [
                'Adquisicion de equipamiento',
                'Proceso de compra de bienes.',
                'rejected',
                'rejected_goods',
            ],
            'explicit consulting company' => [
                'Empresa consultora para estudio institucional',
                'La firma consultora presentara su experiencia institucional.',
                'rejected',
                'rejected_company',
            ],
        ];
    }

    public function test_individual_and_company_signals_require_ai(): void
    {
        $result = (new SicoesDocumentEligibilityClassifier)->classify(
            ['title' => 'Consultoria Individual'],
            'La empresa consultora debe presentar matricula de comercio vigente.',
        );

        $this->assertSame('needs_ai', $result['decision']);
        $this->assertNull($result['eligible']);
        $this->assertNull($result['contract_type']);
    }
}
