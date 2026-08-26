<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Models\Profesion;
use App\Services\Bot\SicoesDocumentAiAnalyzer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SicoesDocumentAiAnalyzerContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->string('area_name');
            $table->timestamps();
        });
        Schema::create('profesions', function (Blueprint $table): void {
            $table->id();
            $table->string('profesion_name');
            $table->timestamps();
        });
        Schema::create('area_profesion', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('profesion_id');
            $table->timestamps();
        });

        $area = Area::create(['area_name' => 'Área SOCIAL']);
        $profession = Profesion::create(['profesion_name' => 'Trabajo Social']);
        $area->profesions()->attach($profession->id);

        config()->set('sicoes.ai.provider', 'anthropic');
        config()->set('sicoes.ai.retries', 1);
        config()->set('services.anthropic.api_key', 'test-key');
        config()->set('services.anthropic.model', 'claude-test');
        config()->set('services.anthropic.verify_ssl', true);
    }

    public function test_valid_contract_returns_explicit_professions_and_never_ids(): void
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response($this->response($this->validPayload())),
        ]);

        $result = app(SicoesDocumentAiAnalyzer::class)->analyze(
            $this->document(),
            'Formación académica: Licenciatura en Trabajo Social.',
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Trabajo Social', data_get($result, 'data.profesiones_encontradas.0.nombre_catalogo'));
        $this->assertSame('Licenciatura en Trabajo Social', data_get($result, 'data.profesiones_encontradas.0.nombre_original'));
        $this->assertArrayNotHasKey('area_ids', $result['data']);
        $this->assertArrayNotHasKey('descripcion', $result['data']);

        Http::assertSent(function (Request $request): bool {
            $payload = $request->data();
            $prompt = (string) data_get($payload, 'messages.0.content.0.text');

            return ! str_contains($prompt, '"area_ids"')
                && str_contains($prompt, '"profesiones_encontradas"')
                && str_contains($prompt, 'No sumes, promedies ni intercambies montos')
                && str_contains($prompt, 'validez de propuesta');
        });
    }

    public function test_invalid_json_contract_is_rejected_with_sanitized_validation_errors(): void
    {
        $invalid = $this->validPayload();
        $invalid['profesiones_encontradas'][0]['evidencia'] = '';
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response($this->response($invalid)),
        ]);

        $result = app(SicoesDocumentAiAnalyzer::class)->analyze($this->document(), 'Texto de prueba');

        $this->assertFalse($result['success']);
        $this->assertSame('invalid_json', $result['error_type']);
        $this->assertNotEmpty($result['ai_validation_errors']);
        $this->assertStringNotContainsString('test-key', json_encode($result));
    }

    public function test_unavailable_claude_does_not_send_a_request(): void
    {
        config()->set('services.anthropic.api_key', null);
        Http::fake();

        $result = app(SicoesDocumentAiAnalyzer::class)->analyze($this->document(), 'Texto de prueba');

        $this->assertFalse($result['success']);
        $this->assertSame('missing_api_key', $result['error_type']);
        Http::assertNothingSent();
    }

    public function test_malformed_utf8_from_pdf_is_repaired_before_sending_request(): void
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response($this->response($this->validPayload())),
        ]);

        $result = app(SicoesDocumentAiAnalyzer::class)->analyze(
            $this->document(),
            "Texto extraido del PDF \xC3\x28 con consultoria individual.",
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['document_text_utf8_repaired']);
        $this->assertSame(1, $result['ai_attempts']);

        Http::assertSent(function (Request $request): bool {
            $prompt = (string) data_get($request->data(), 'messages.0.content.0.text');

            return mb_check_encoding($prompt, 'UTF-8')
                && str_contains($prompt, 'Texto extraido del PDF');
        });
    }

    public function test_scanned_personnel_pdf_is_sent_as_visual_document(): void
    {
        $payload = $this->validPayload();
        $payload['tipo_oportunidad'] = 'requerimiento_personal';
        $pdf = tempnam(sys_get_temp_dir(), 'sicoes-personnel-').'.pdf';
        file_put_contents($pdf, "%PDF-1.4\nscanned-image-placeholder\n%%EOF");

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response($this->response($payload)),
        ]);

        try {
            $result = app(SicoesDocumentAiAnalyzer::class)->analyze([
                ...$this->document(),
                'cuce' => 'GAM-REF-12/2026',
                'reference' => 'GAM-REF-12/2026',
                'source_type' => 'personnel_requirements',
                'visual_pdf_path' => $pdf,
            ], 'Requerimiento de personal SICOES.');

            $this->assertTrue($result['success']);
            $this->assertTrue($result['document_visual_pdf_sent']);
            $this->assertSame('requerimiento_personal', data_get($result, 'data.tipo_oportunidad'));

            Http::assertSent(function (Request $request): bool {
                $content = data_get($request->data(), 'messages.0.content', []);

                return data_get($content, '0.type') === 'document'
                    && data_get($content, '0.source.media_type') === 'application/pdf'
                    && data_get($content, '0.source.type') === 'base64'
                    && data_get($content, '1.type') === 'text'
                    && str_contains((string) data_get($content, '1.text'), 'personnel_requirements');
            });
        } finally {
            @unlink($pdf);
        }
    }

    private function validPayload(): array
    {
        return [
            'eligible' => true,
            'contract_type' => 'individual',
            'es_oportunidad_consultor_persona' => true,
            'tipo_oportunidad' => 'consultor_linea',
            'debe_descartarse' => false,
            'motivo_descarte' => null,
            'evidencia_clasificacion' => 'Consultoría individual de línea.',
            'titulo_objeto' => 'Consultor social',
            'cargos' => [
                ['nombre' => 'Consultor social', 'evidencia' => 'Cargo: Consultor social'],
            ],
            'profesiones_encontradas' => [[
                'nombre_original' => 'Licenciatura en Trabajo Social',
                'nombre_catalogo' => 'Trabajo Social',
                'evidencia' => 'Formación académica: Licenciatura en Trabajo Social.',
                'tipo_requisito' => 'obligatoria',
                'confianza' => 0.99,
            ]],
            'acepta_carreras_afines' => false,
            'evidencia_carreras_afines' => '',
            'area_principal_catalogo' => 'Área SOCIAL',
            'evidencia_area_principal' => 'Cargo: Consultor social',
            'confianza_area_principal' => 0.98,
            'lugar_trabajo' => [
                'direccion_exacta' => 'Oficina regional de Tarija',
                'municipio' => 'Tarija',
                'departamento' => 'Tarija',
                'evidencia' => 'Lugar de trabajo: Tarija.',
                'documento_fuente' => 'TDR',
                'confianza' => 0.99,
                'requiere_revision' => false,
                'direcciones_candidatas_descartadas' => [],
            ],
            'duracion_contrato' => [
                'texto_exacto' => 'Seis meses',
                'evidencia' => 'Duración del contrato: seis meses.',
                'confianza' => 0.99,
            ],
            'modalidad_postulacion' => [
                'texto_exacto' => 'Presentación física.',
                'tipo' => 'fisica',
                'evidencia' => 'La propuesta deberá entregarse en sobre cerrado.',
                'confianza' => 0.98,
            ],
            'cuce' => [
                'valor' => '26-0000-00-0000001-1-1',
                'evidencia' => 'CUCE 26-0000-00-0000001-1-1',
            ],
            'salarios' => [
                'tipo' => 'no_declarado',
                'cantidad' => 0,
                'detalle' => [],
            ],
            'advertencias' => [],
        ];
    }

    private function response(array $payload): array
    {
        return [
            'content' => [[
                'type' => 'text',
                'text' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]],
            'stop_reason' => 'end_turn',
            'usage' => ['input_tokens' => 100, 'output_tokens' => 100],
        ];
    }

    private function document(): array
    {
        return [
            'cuce' => '26-0000-00-0000001-1-1',
            'title' => 'Consultor social',
            'entity' => 'Entidad pública',
            'filename' => 'tdr.docx',
            'published_at' => '28/07/2026',
        ];
    }
}
