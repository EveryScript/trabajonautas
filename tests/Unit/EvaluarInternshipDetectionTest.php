<?php

namespace Tests\Unit;

use App\Services\Bot\EvaluarScraperService;
use ReflectionMethod;
use Tests\TestCase;

class EvaluarInternshipDetectionTest extends TestCase
{
    public function test_operational_good_practices_are_not_treated_as_an_internship(): void
    {
        $isInternship = $this->detect(
            'JEFE DE ALMACEN Y DISTRIBUCIÓN 2026',
            'Promover el cumplimiento de las Buenas Prácticas de Almacenamiento, normas de seguridad y estándares corporativos.',
        );

        $this->assertFalse($isInternship);
    }

    public function test_explicit_internship_language_is_still_detected(): void
    {
        $cases = [
            ['PASANTÍA DE ALMACENES', 'Oportunidad dirigida a estudiantes.'],
            ['PRACTICANTE DE RECURSOS HUMANOS', 'Apoyo al área de personas.'],
            ['Programa de prácticas', 'Prácticas profesionales remuneradas.'],
            ['Asistente temporal', 'La persona realizará sus prácticas en la empresa.'],
            ['Finance trainee', 'Training program.'],
        ];

        foreach ($cases as [$title, $description]) {
            $this->assertTrue(
                $this->detect($title, $description),
                "No se detectó como pasantía: {$title}",
            );
        }
    }

    private function detect(string ...$texts): bool
    {
        $method = new ReflectionMethod(EvaluarScraperService::class, 'isInternshipOrPractice');

        return (bool) $method->invoke(app(EvaluarScraperService::class), ...$texts);
    }
}
