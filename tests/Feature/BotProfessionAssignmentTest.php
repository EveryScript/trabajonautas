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

    public function test_gemini_returns_explicit_professions_and_database_resolves_the_ids(): void
    {
        config()->set('services.gemini.key', 'test-key');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'profesiones_encontradas' => [[
                                'nombre_original' => 'Ingenieria Electrica',
                                'nombre_catalogo' => 'Ingenieria Electrica',
                                'evidencia' => 'Se requiere Ingeniería Eléctrica.',
                                'tipo_requisito' => 'obligatoria',
                                'confianza' => 0.98,
                            ]],
                            'acepta_carreras_afines' => false,
                            'evidencia_carreras_afines' => '',
                            'area_principal_catalogo' => 'Area ELECTRICA',
                            'evidencia_area_principal' => 'Se requiere Ingeniería Eléctrica.',
                            'confianza_area_principal' => 0.98,
                            'ubicacion_departamento' => 'La Paz',
                            'ubicacion' => 'La Paz / El Alto',
                            'sueldo' => '0',
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
        $assignment = $this->assignments->resolveDetectedProfessions(
            $result['data']['profesiones_encontradas'],
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Ingenieria Electrica', $result['data']['profesiones_encontradas'][0]['nombre_original']);
        $this->assertSame([$this->unrelatedProfessionIds[0]], $assignment['profession_ids']);
        $this->assertSame([$this->electricalArea->id], $assignment['area_ids']);
        $this->assertTrue($assignment['valid']);
        Http::assertSent(function (Request $request): bool {
            $prompt = (string) data_get($request->data(), 'contents.0.parts.0.text');
            $allowedNames = (array) data_get(
                $request->data(),
                'generationConfig.responseSchema.properties.profesiones_encontradas.items.properties.nombre_catalogo.enum',
                [],
            );

            return str_contains($prompt, 'Ingenieria Electrica')
                && ! str_contains($prompt, '"id":')
                && in_array('Ingenieria Electrica', $allowedNames, true);
        });
    }

    public function test_gemini_duplicate_catalog_profession_is_deduplicated_without_failing_the_contract(): void
    {
        config()->set('services.gemini.key', 'test-key');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'profesiones_encontradas' => [
                                [
                                    'nombre_original' => 'Ingenieria Electrica',
                                    'nombre_catalogo' => 'Ingenieria Electrica',
                                    'evidencia' => 'Se requiere Ingeniería Eléctrica.',
                                    'tipo_requisito' => 'obligatoria',
                                    'confianza' => 0.98,
                                ],
                                [
                                    'nombre_original' => 'carrera eléctrica',
                                    'nombre_catalogo' => 'Ingenieria Electrica',
                                    'evidencia' => 'También se admite carrera eléctrica.',
                                    'tipo_requisito' => 'alternativa',
                                    'confianza' => 0.95,
                                ],
                            ],
                            'acepta_carreras_afines' => false,
                            'evidencia_carreras_afines' => '',
                            'area_principal_catalogo' => 'Area ELECTRICA',
                            'evidencia_area_principal' => 'Se requiere Ingeniería Eléctrica.',
                            'confianza_area_principal' => 0.98,
                            'ubicacion_departamento' => '',
                            'ubicacion' => '',
                            'sueldo' => '0',
                            'fecha_expiracion' => 'No especificado',
                        ]),
                    ]]],
                ]],
            ]),
        ]);

        $analysis = app(GeminiVacancyAnalyzer::class)->analyzeWithMeta(
            'Ingeniero eléctrico',
            $this->company,
            'Se requiere Ingeniería Eléctrica; también se admite carrera eléctrica.',
        );
        $resolution = $this->assignments->resolveDetectedProfessions(
            $analysis['data']['profesiones_encontradas'],
            false,
            null,
            $analysis['data']['area_principal_catalogo'],
            $analysis['data']['confianza_area_principal'],
            $analysis['data']['evidencia_area_principal'],
        );

        $this->assertTrue($analysis['success']);
        $this->assertSame([$this->unrelatedProfessionIds[0]], $resolution['profession_ids']);
        $this->assertCount(2, $resolution['profesiones_resueltas'][0]['evidencias']);
        $this->assertSame([], $resolution['profesiones_no_identificadas']);
    }

    public function test_anthropic_returns_explicit_professions_and_mysql_resolves_ids(): void
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
                        'titulo_objeto' => 'Consultor financiero',
                        'cargos' => [
                            ['nombre' => 'Consultor financiero', 'evidencia' => 'Cargo: Consultor financiero'],
                        ],
                        'profesiones_encontradas' => [[
                            'nombre_original' => 'Ingeniería Financiera',
                            'nombre_catalogo' => 'Ingenieria Financiera',
                            'evidencia' => 'Formación académica: Ingeniería Financiera.',
                            'tipo_requisito' => 'obligatoria',
                            'confianza' => 0.99,
                        ]],
                        'acepta_carreras_afines' => false,
                        'evidencia_carreras_afines' => '',
                        'area_principal_catalogo' => 'Area ECONOMICA, ADMINISTRATIVA Y FINANCIERA',
                        'evidencia_area_principal' => 'Consultor financiero.',
                        'confianza_area_principal' => 0.98,
                        'lugar_trabajo' => [
                            'direccion_exacta' => '',
                            'municipio' => '',
                            'departamento' => 'La Paz',
                            'evidencia' => 'Lugar de trabajo: La Paz.',
                            'documento_fuente' => 'TDR',
                            'confianza' => 0.99,
                            'requiere_revision' => false,
                            'direcciones_candidatas_descartadas' => [],
                        ],
                        'duracion_contrato' => [
                            'texto_exacto' => '',
                            'evidencia' => '',
                            'confianza' => 0,
                        ],
                        'modalidad_postulacion' => [
                            'texto_exacto' => 'Presentación digital.',
                            'tipo' => 'digital_otro',
                            'evidencia' => 'La propuesta será enviada digitalmente.',
                            'confianza' => 0.95,
                        ],
                        'cuce' => [
                            'valor' => 'TEST-CUCE',
                            'evidencia' => 'CUCE: TEST-CUCE',
                        ],
                        'salarios' => [
                            'tipo' => 'no_declarado',
                            'cantidad' => 0,
                            'detalle' => [],
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

        $resolution = $this->assignments->resolveDetectedProfessions(
            $result['data']['profesiones_encontradas'] ?? [],
        );

        $this->assertTrue($result['success']);
        $this->assertArrayNotHasKey('area_ids', $result['data']);
        $this->assertSame([$this->economicProfessionIds[5]], $resolution['profession_ids']);
        Http::assertSent(function (Request $request): bool {
            $prompt = (string) data_get($request->data(), 'messages.0.content.0.text');

            return str_contains($prompt, '"nombre":"Ingenieria Financiera"')
                && ! str_contains($prompt, '"id":'.$this->economicArea->id);
        });
    }

    public function test_gemini_contract_expands_the_validated_area_when_affine_careers_are_accepted(): void
    {
        config()->set('services.gemini.key', 'test-key');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'profesiones_encontradas' => [[
                                'nombre_original' => 'Administracion de Empresas',
                                'nombre_catalogo' => 'Administracion de Empresas',
                                'evidencia' => 'Administración de Empresas o carreras afines.',
                                'tipo_requisito' => 'alternativa',
                                'confianza' => 0.95,
                            ]],
                            'acepta_carreras_afines' => true,
                            'evidencia_carreras_afines' => 'Administración de Empresas o carreras afines.',
                            'area_principal_catalogo' => 'Area ECONOMICA, ADMINISTRATIVA Y FINANCIERA',
                            'evidencia_area_principal' => 'Administración de Empresas',
                            'confianza_area_principal' => 0.98,
                            'ubicacion_departamento' => '',
                            'ubicacion' => '',
                            'sueldo' => '0',
                            'fecha_expiracion' => 'No especificado',
                        ]),
                    ]]],
                ]],
            ]),
        ]);

        $result = app(GeminiVacancyAnalyzer::class)->analyzeWithMeta(
            'Analista',
            $this->company,
            'Administración de Empresas o carreras afines.',
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['data']['acepta_carreras_afines']);
        $this->assertCount(1, $result['data']['profesiones_encontradas']);
        $assignment = $this->assignments->resolveDetectedProfessions(
            $result['data']['profesiones_encontradas'],
            $result['data']['acepta_carreras_afines'],
            $result['data']['evidencia_carreras_afines'],
            $result['data']['area_principal_catalogo'],
            $result['data']['confianza_area_principal'],
            $result['data']['evidencia_area_principal'],
        );
        $this->assertEqualsCanonicalizing($this->economicProfessionIds, $assignment['profession_ids']);
        $this->assertTrue($assignment['expansion_afinidad_aplicada']);
        $this->assertSame($this->economicArea->id, $assignment['selected_area_id']);
    }

    public function test_invalid_gemini_profession_contract_returns_a_safe_error_instead_of_breaking(): void
    {
        config()->set('services.gemini.key', 'test-key');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'profesiones_encontradas' => [[
                                'nombre_original' => 'Administracion de Empresas',
                                'nombre_catalogo' => 'Profesión inventada',
                                'evidencia' => '',
                                'tipo_requisito' => 'inventada',
                                'confianza' => 2,
                            ]],
                            'acepta_carreras_afines' => false,
                            'evidencia_carreras_afines' => '',
                            'area_principal_catalogo' => 'SIN_AREA',
                            'evidencia_area_principal' => '',
                            'confianza_area_principal' => 0,
                            'ubicacion_departamento' => '',
                            'ubicacion' => '',
                            'sueldo' => '0',
                            'fecha_expiracion' => 'No especificado',
                        ]),
                    ]]],
                ]],
            ]),
        ]);

        $result = app(GeminiVacancyAnalyzer::class)->analyzeWithMeta(
            'Analista',
            $this->company,
            'Administración de Empresas.',
        );

        $this->assertFalse($result['success']);
        $this->assertSame('invalid_schema', $result['error_type']);
        $this->assertNotEmpty($result['gemini_validation_errors']);
        $this->assertTrue(collect($result['gemini_validation_errors'])->contains(
            fn (string $error): bool => str_contains($error, 'nombre_catalogo no pertenece'),
        ));
        $this->assertSame([], $result['data']['profesiones_encontradas']);
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
