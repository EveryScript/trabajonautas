<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\BotCompany;
use App\Models\BotVacancyPreview;
use App\Models\Profesion;
use App\Services\Bot\BotVacancyNormalizer;
use App\Services\Bot\GeminiVacancyAnalyzer;
use App\Services\Bot\SicoesDocumentAiAnalyzer;
use App\Services\ProfessionAssignmentService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BotProfessionAssignmentTest extends TestCase
{
    private ProfessionAssignmentService $assignments;

    private BotCompany $company;

    private Area $economicArea;

    private Area $electricalArea;

    private array $economicProfessionIds;

    private array $unrelatedProfessionIds;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
        $this->assignments = app(ProfessionAssignmentService::class);
        $this->company = BotCompany::create([
            'name' => 'BANCO ECONOMICO',
            'slug' => 'banco-economico-test',
            'evaluar_url' => 'https://example.test/feed',
            'active' => true,
        ]);
        $this->economicArea = $this->area('Area ECONOMICA, ADMINISTRATIVA Y FINANCIERA');
        $this->electricalArea = $this->area('Area ELECTRICA');
        $socialArea = $this->area('Area SOCIAL');
        $systemsArea = $this->area('Area SISTEMAS E INFORMATICA');
        $civilArea = $this->area('Area CIVIL Y CONSTRUCCION');
        $environmentalArea = $this->area('Area AMBIENTAL');

        $this->economicProfessionIds = collect([
            'Administracion de Empresas',
            'Contaduria',
            'Economia',
            'Auditoria',
            'Ingenieria Comercial',
            'Ingenieria Financiera',
        ])->map(fn (string $name): int => $this->profession($name)->id)->all();
        $this->economicArea->profesions()->sync($this->economicProfessionIds);

        $unrelatedByArea = [
            [$this->electricalArea, ['Ingenieria Electrica', 'Electronica, Electromecanica y Mecatronica']],
            [$civilArea, ['Ingenieria Civil']],
            [$environmentalArea, ['Ingenieria Ambiental']],
            [$systemsArea, ['Sistemas e Informatica']],
            [$socialArea, ['Sociologia']],
        ];
        $this->unrelatedProfessionIds = [];

        foreach ($unrelatedByArea as [$area, $names]) {
            $ids = collect($names)->map(fn (string $name): int => $this->profession($name)->id)->all();
            $area->profesions()->sync($ids);
            $this->unrelatedProfessionIds = [...$this->unrelatedProfessionIds, ...$ids];
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_professions_are_derived_only_from_selected_areas(): void
    {
        $assignment = $this->assignments->resolve([$this->economicArea->id]);

        $this->assertTrue($assignment['valid']);
        $this->assertSame($this->economicProfessionIds, $assignment['profession_ids']);
        $this->assertSame([], array_values(array_intersect($this->unrelatedProfessionIds, $assignment['profession_ids'])));
    }

    public function test_processing_twice_replaces_instead_of_accumulating_professions(): void
    {
        $preview = $this->preview([
            'selected_profession_ids' => [...$this->economicProfessionIds, ...$this->unrelatedProfessionIds],
        ]);
        $assignment = $this->assignments->resolve([$this->economicArea->id]);

        $first = $this->assignments->applyToPreview($preview, $assignment);
        $second = $this->assignments->applyToPreview($preview->fresh(), $assignment);

        $this->assertSame($this->economicProfessionIds, $first['professions_after']);
        $this->assertSame($this->economicProfessionIds, $second['professions_after']);
        $this->assertSame($this->economicProfessionIds, $preview->fresh()->selected_profession_ids);
    }

    public function test_unknown_area_does_not_select_any_profession(): void
    {
        $preview = $this->preview([
            'selected_profession_ids' => $this->economicProfessionIds,
        ]);
        $assignment = $this->assignments->resolve([999999]);

        $result = $this->assignments->applyToPreview($preview, $assignment);

        $this->assertFalse($assignment['valid']);
        $this->assertSame([], $result['professions_after']);
        $this->assertSame([], $preview->fresh()->selected_profession_ids);
        $this->assertSame('error', $preview->fresh()->status);
    }

    public function test_manually_removed_professions_are_not_restored(): void
    {
        $keptProfessionId = $this->economicProfessionIds[0];
        $preview = $this->preview([
            'status' => 'edited',
            'selected_profession_ids' => [$keptProfessionId],
            'raw_data' => ['manual_professions_locked' => true],
        ]);
        $assignment = $this->assignments->resolve([$this->economicArea->id]);

        $result = $this->assignments->applyToPreview($preview, $assignment);

        $this->assertTrue($result['preserved_manual_selection']);
        $this->assertSame([$keptProfessionId], $preview->fresh()->selected_profession_ids);
    }

    public function test_banco_economico_case_has_expected_professions_location_salary_and_expiration(): void
    {
        Carbon::setTestNow('2026-07-10 12:00:00');
        $assignment = $this->assignments->resolve([$this->economicArea->id]);
        $professionNames = collect($assignment['profession_names'])->map(fn (string $name): string => mb_strtolower($name))->all();
        $normalizer = app(BotVacancyNormalizer::class);
        $normalized = $normalizer->normalize(
            title: 'EJECUTIVO DE NEGOCIOS BANCA MYPE LA PAZ - EL ALTO',
            description: 'Titulado o egresado en carreras financieras - empresariales o afines al rubro.',
            analysis: [
                'department' => 'La Paz',
                'location' => 'La Paz / El Alto',
                'salary' => 0,
                'expiration_date' => '2026-07-26',
            ],
        );

        foreach (['ingenieria electrica', 'ingenieria civil', 'ingenieria ambiental', 'sistemas e informatica', 'sociologia', 'mecatronica'] as $unexpected) {
            $this->assertFalse(collect($professionNames)->contains(fn (string $name): bool => str_contains($name, $unexpected)));
        }

        $this->assertSame('La Paz', $normalized['department']);
        $this->assertSame(0, $normalized['salary']);
        $this->assertSame('2026-07-26 23:59:00', $normalized['expiration_date']);
    }

    public function test_gemini_keeps_unknown_area_ids_visible_for_strict_rejection(): void
    {
        config()->set('services.gemini.key', 'test-key');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'area_ids' => [$this->economicArea->id, 999999],
                            'profesiones_sugeridas' => ['Ingenieria Electrica'],
                            'ubicacion_departamento' => 'La Paz',
                            'ubicacion' => 'La Paz / El Alto',
                            'sueldo' => 0,
                            'fecha_expiracion' => '2026-07-26',
                        ]),
                    ]]],
                ]],
            ]),
        ]);

        $result = app(GeminiVacancyAnalyzer::class)->analyzeWithMeta(
            'EJECUTIVO DE NEGOCIOS BANCA MYPE LA PAZ - EL ALTO',
            $this->company,
            'Carreras financieras y empresariales.',
        );
        $assignment = $this->assignments->resolve($result['data']['area_ids']);

        $this->assertTrue($result['success']);
        $this->assertSame([$this->economicArea->id, 999999], $result['data']['area_ids']);
        $this->assertSame([], $result['data']['profesiones_sugeridas']);
        $this->assertSame('No especificado', $result['data']['professions']);
        $this->assertFalse($assignment['valid']);
        $this->assertSame([], $assignment['profession_ids']);
        Http::assertSent(fn (Request $request): bool => str_contains((string) data_get($request->data(), 'contents.0.parts.0.text'), '"id":'.$this->economicArea->id));
    }

    public function test_anthropic_returns_only_catalog_area_ids_for_professional_assignment(): void
    {
        config()->set('sicoes.ai.provider', 'anthropic');
        config()->set('services.anthropic.api_key', 'test-key');
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [[
                    'type' => 'text',
                    'text' => json_encode([
                        'eligible' => true,
                        'contract_type' => 'individual',
                        'es_oportunidad_consultor_persona' => true,
                        'tipo_oportunidad' => 'consultor_linea',
                        'debe_descartarse' => false,
                        'motivo_descarte' => null,
                        'evidencia_clasificacion' => 'Consultor individual de linea.',
                        'cuce' => 'TEST-CUCE',
                        'titulo_objeto' => 'Consultor financiero',
                        'area_ids' => [$this->economicArea->id],
                        'profesiones_sugeridas' => ['Ingenieria Electrica'],
                        'ubicacion_detectada' => [
                            'texto' => 'La Paz',
                            'municipio' => '',
                            'departamento' => 'La Paz',
                            'confianza' => 'alta',
                        ],
                        'lugar_trabajo' => 'La Paz',
                        'duracion_contrato' => 'No especificado',
                        'modalidad_postulacion' => 'Digital',
                        'sueldo' => [
                            'valor' => 0,
                            'detalle' => 'No especificado',
                            'sueldos' => [],
                            'revision_manual' => true,
                        ],
                        'detalle_sueldos' => 'No especificado',
                        'descripcion' => 'No especificado',
                        'evidencias' => [
                            'cuce' => 'TEST-CUCE',
                            'area_profesional' => 'Carreras financieras.',
                            'lugar_trabajo' => 'La Paz',
                            'duracion_contrato' => '',
                            'modalidad_postulacion' => 'Digital',
                            'sueldo' => '',
                        ],
                        'advertencias' => [],
                    ]),
                ]],
                'stop_reason' => 'end_turn',
                'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
            ]),
        ]);

        $result = app(SicoesDocumentAiAnalyzer::class)->analyze([
            'cuce' => 'TEST-CUCE',
            'title' => 'Consultor financiero',
            'entity' => 'Entidad publica',
            'filename' => 'tdr.pdf',
            'published_at' => '2026-07-10',
        ], 'Terminos de referencia para consultor individual de linea.');

        $this->assertTrue($result['success']);
        $this->assertSame([$this->economicArea->id], $result['data']['area_ids']);
        $this->assertArrayNotHasKey('profesiones_sugeridas', $result['data']);
        Http::assertSent(fn (Request $request): bool => str_contains((string) data_get($request->data(), 'messages.0.content.0.text'), '"id":'.$this->economicArea->id));
    }

    private function area(string $name): Area
    {
        return Area::create([
            'area_name' => $name,
            'description' => $name,
            'user_id' => null,
        ]);
    }

    private function profession(string $name): Profesion
    {
        return Profesion::create([
            'profesion_name' => $name,
            'user_id' => null,
        ]);
    }

    private function preview(array $attributes = []): BotVacancyPreview
    {
        return BotVacancyPreview::create(array_merge([
            'bot_company_id' => $this->company->id,
            'title' => 'EJECUTIVO DE NEGOCIOS BANCA MYPE LA PAZ - EL ALTO',
            'source_url' => 'https://example.test/jobs/'.uniqid('', true),
            'area' => $this->economicArea->area_name,
            'professions' => 'No especificado',
            'status' => 'preview',
            'scrape_batch_id' => 'batch-test',
            'selected_area_id' => $this->economicArea->id,
            'selected_profession_ids' => [],
            'raw_data' => [],
        ], $attributes));
    }

    private function createSchema(): void
    {
        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->string('area_name')->unique();
            $table->string('description');
            $table->uuid('user_id')->nullable();
            $table->timestamps();
        });
        Schema::create('profesions', function (Blueprint $table): void {
            $table->id();
            $table->string('profesion_name')->unique();
            $table->uuid('user_id')->nullable();
            $table->timestamps();
        });
        Schema::create('area_profesion', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('profesion_id');
            $table->timestamps();
        });
        Schema::create('bot_companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('evaluar_url');
            $table->string('logo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('bot_vacancy_previews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('bot_company_id');
            $table->string('title');
            $table->string('source_url')->unique();
            $table->longText('original_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('area')->nullable();
            $table->text('professions')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('expiration_date')->nullable();
            $table->string('salary')->nullable();
            $table->json('raw_data')->nullable();
            $table->string('status')->default('preview');
            $table->string('scrape_batch_id')->nullable();
            $table->timestamp('removed_from_batch_at')->nullable();
            $table->unsignedBigInteger('convocatoria_id')->nullable();
            $table->unsignedBigInteger('selected_company_id')->nullable();
            $table->unsignedBigInteger('selected_area_id')->nullable();
            $table->json('selected_profession_ids')->nullable();
            $table->json('selected_location_ids')->nullable();
            $table->boolean('is_pro')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }
}
