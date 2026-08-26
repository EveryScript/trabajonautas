<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\BotCompany;
use App\Models\BotVacancyPreview;
use App\Models\Profesion;
use App\Services\Bot\EvaluarScraperService;
use App\Services\ProfessionAssignmentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EvaluarReanalysisTest extends TestCase
{
    private BotCompany $company;

    private Area $area;

    private Profesion $detectedProfession;

    private Profesion $manualProfession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
        config()->set('services.gemini.key', 'test-key');
        config()->set('services.evaluar.allowed_host_suffixes', ['evaluar.test']);

        $this->company = BotCompany::create([
            'name' => 'BANCO UNION',
            'slug' => 'banco-union-reanalysis',
            'evaluar_url' => 'https://bancounion.evaluar.test',
            'active' => true,
        ]);
        $this->area = Area::create([
            'area_name' => 'Ciencias Sociales',
            'description' => 'Área social',
            'user_id' => null,
        ]);
        $this->detectedProfession = Profesion::create([
            'profesion_name' => 'Trabajo Social',
            'user_id' => null,
        ]);
        $this->manualProfession = Profesion::create([
            'profesion_name' => 'Psicología',
            'user_id' => null,
        ]);
        $this->area->profesions()->sync([
            $this->detectedProfession->id,
            $this->manualProfession->id,
        ]);
    }

    public function test_error_preview_can_be_reanalyzed_and_selects_only_the_detected_profession(): void
    {
        $this->fakeSuccessfulGemini();
        $preview = $this->preview(['status' => 'error']);

        $result = app(EvaluarScraperService::class)->reanalyzePreview($preview);
        $preview->refresh();

        $this->assertTrue($result['gemini_success']);
        $this->assertSame('preview', $preview->status);
        $this->assertSame([$this->detectedProfession->id], $preview->selected_profession_ids);
        $this->assertSame($this->area->id, $preview->selected_area_id);
        $this->assertSame(
            'Trabajo Social',
            data_get($preview->raw_data, 'profession_resolution.profesiones_resueltas.0.profesion_name'),
        );
        $this->assertSame('Tarija', $preview->department);
        $this->assertSame('Tarija', $preview->location);
        $this->assertSame('page_json_ld', data_get($preview->raw_data, 'location_source'));
    }

    public function test_edited_preview_keeps_manual_changes_during_explicit_reanalysis(): void
    {
        $this->fakeSuccessfulGemini();
        $preview = $this->preview([
            'status' => 'edited',
            'selected_profession_ids' => [$this->manualProfession->id],
            'selected_area_id' => $this->area->id,
            'raw_data' => ['manual_professions_locked' => true],
        ]);

        $result = app(EvaluarScraperService::class)->reanalyzePreview($preview);
        $preview->refresh();

        $this->assertTrue($result['manual_changes_preserved']);
        $this->assertSame('edited', $preview->status);
        $this->assertSame([$this->manualProfession->id], $preview->selected_profession_ids);
        $this->assertTrue((bool) data_get($preview->raw_data, 'manual_changes_preserved'));
    }

    public function test_published_preview_is_not_sent_to_gemini(): void
    {
        Http::fake();
        $preview = $this->preview(['status' => 'published']);

        $result = app(EvaluarScraperService::class)->reanalyzePreview($preview);

        $this->assertSame('published_preview', $result['reason']);
        $this->assertFalse($result['gemini_used']);
        Http::assertNothingSent();
    }

    public function test_missing_location_can_be_refreshed_from_the_detail_page_without_calling_gemini(): void
    {
        Http::fake([
            'https://bancounion.evaluar.test/*' => Http::response(
                '<script type="application/ld+json">'
                .json_encode([
                    '@type' => 'JobPosting',
                    'jobLocation' => [
                        '@type' => 'Place',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => 'LA PAZ',
                            'addressCountry' => 'BO',
                        ],
                    ],
                ])
                .'</script>',
                200,
            ),
        ]);
        $preview = $this->preview([
            'status' => 'preview',
            'department' => 'No especificado',
            'location' => 'No especificado',
            'original_description' => 'Se requiere Licenciatura en Trabajo Social.',
        ]);

        $result = app(EvaluarScraperService::class)->refreshPreviewLocationFromPage($preview);
        $preview->refresh();

        $this->assertTrue($result['updated']);
        $this->assertSame('La Paz', $preview->department);
        $this->assertSame('LA PAZ', $preview->location);
        $this->assertSame('page_json_ld', data_get($preview->raw_data, 'location_source'));
        $this->assertNotEmpty($preview->selected_location_ids);
        Http::assertSentCount(1);
        Http::assertSent(
            fn ($request): bool => str_contains($request->url(), 'bancounion.evaluar.test'),
        );
    }

    public function test_preview_without_a_valid_location_is_sent_to_manual_review(): void
    {
        Http::fake([
            'https://bancounion.evaluar.test/*' => Http::response('<html></html>', 200),
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'profesiones_encontradas' => [[
                                'nombre_original' => 'Trabajo Social',
                                'nombre_catalogo' => 'Trabajo Social',
                                'evidencia' => 'Se requiere Licenciatura en Trabajo Social.',
                                'tipo_requisito' => 'obligatoria',
                                'confianza' => 0.98,
                            ]],
                            'acepta_carreras_afines' => false,
                            'evidencia_carreras_afines' => '',
                            'area_principal_catalogo' => 'Ciencias Sociales',
                            'evidencia_area_principal' => 'Licenciatura en Trabajo Social.',
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
        $preview = $this->preview([
            'status' => 'error',
            'original_description' => 'Se requiere Licenciatura en Trabajo Social.',
        ]);

        $result = app(EvaluarScraperService::class)->reanalyzePreview($preview);
        $preview->refresh();

        $this->assertTrue($result['gemini_success']);
        $this->assertSame('error', $preview->status);
        $this->assertContains(
            'La ubicación es obligatoria y no pudo obtenerse de la publicación de Evaluar.',
            data_get($preview->raw_data, 'motivos_revision', []),
        );
        $this->assertTrue((bool) data_get($preview->raw_data, 'manual_review_required'));
    }

    public function test_title_municipality_is_written_in_the_description_and_department_is_selected(): void
    {
        $this->fakeSuccessfulGemini('LA PAZ', 'La Paz');
        $preview = $this->preview([
            'status' => 'error',
            'title' => 'OFICIAL DE NEGOCIOS - GUAQUI',
            'original_description' => 'Se requiere Licenciatura en Trabajo Social.',
        ]);

        $result = app(EvaluarScraperService::class)->reanalyzePreview($preview);
        $preview->refresh();

        $this->assertTrue($result['gemini_success']);
        $this->assertSame('La Paz', $preview->department);
        $this->assertSame('Guaqui', $preview->location);
        $this->assertStringContainsString('Guaqui, La Paz', $preview->description);
        $this->assertSame(['La Paz'], data_get($preview->raw_data, 'location_departments'));
        $this->assertNotEmpty($preview->selected_location_ids);
    }

    public function test_explicit_department_capital_is_also_written_in_the_description(): void
    {
        $this->fakeSuccessfulGemini('BENI', 'Beni');
        $preview = $this->preview([
            'status' => 'error',
            'title' => 'OFICIAL DE MICROCREDITOS (TRINIDAD)',
            'original_description' => 'Se requiere Licenciatura en Trabajo Social.',
        ]);

        app(EvaluarScraperService::class)->reanalyzePreview($preview);
        $preview->refresh();

        $this->assertSame('Beni', $preview->department);
        $this->assertSame('Trinidad', $preview->location);
        $this->assertStringContainsString('Trinidad, Beni', $preview->description);
    }

    public function test_automatic_reanalysis_rules_skip_unchanged_and_allow_changed_content(): void
    {
        $service = app(EvaluarScraperService::class);
        $fingerprint = app(ProfessionAssignmentService::class)->catalogFingerprint();
        $preview = $this->preview([
            'status' => 'error',
            'raw_data' => [
                'content_hash' => 'same-hash',
                'classifier_version' => config('profession_matching.classifier_version'),
                'prompt_version' => config('profession_matching.prompt_version'),
                'catalog_fingerprint' => $fingerprint,
            ],
        ]);

        $this->assertNull($service->determineAutomaticReanalysisReason($preview, 'same-hash'));
        $this->assertSame(
            'source_content_changed',
            $service->determineAutomaticReanalysisReason($preview, 'new-hash'),
        );

        $preview->update(['status' => 'edited']);
        $this->assertNull($service->determineAutomaticReanalysisReason($preview->fresh(), 'new-hash'));
        $preview->update(['status' => 'published']);
        $this->assertNull($service->determineAutomaticReanalysisReason($preview->fresh(), 'new-hash'));
    }

    private function fakeSuccessfulGemini(
        string $pageLocation = 'Tarija',
        string $geminiLocation = 'Tarija',
    ): void
    {
        Http::fake([
            'https://bancounion.evaluar.test/*' => Http::response(
                '<html><head><script type="application/ld+json">'
                .json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'JobPosting',
                    'validThrough' => '2026-07-29T23:59:59+00:00',
                    'jobLocation' => [
                        '@type' => 'Place',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => $pageLocation,
                            'addressCountry' => 'BO',
                        ],
                    ],
                ])
                .'</script></head><body></body></html>',
                200,
                ['Content-Type' => 'text/html'],
            ),
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'profesiones_encontradas' => [[
                                'nombre_original' => 'Trabajo Social',
                                'nombre_catalogo' => 'Trabajo Social',
                                'evidencia' => 'Se requiere Licenciatura en Trabajo Social.',
                                'tipo_requisito' => 'obligatoria',
                                'confianza' => 0.98,
                            ]],
                            'acepta_carreras_afines' => false,
                            'evidencia_carreras_afines' => '',
                            'area_principal_catalogo' => 'Ciencias Sociales',
                            'evidencia_area_principal' => 'Se requiere Licenciatura en Trabajo Social.',
                            'confianza_area_principal' => 0.98,
                            'ubicacion_departamento' => $geminiLocation,
                            'ubicacion' => $geminiLocation,
                            'sueldo' => '0',
                            'fecha_expiracion' => '2026-07-29',
                        ]),
                    ]]],
                ]],
                'usageMetadata' => [
                    'promptTokenCount' => 100,
                    'candidatesTokenCount' => 50,
                    'totalTokenCount' => 150,
                ],
            ]),
        ]);
    }

    private function preview(array $attributes = []): BotVacancyPreview
    {
        return BotVacancyPreview::create(array_merge([
            'bot_company_id' => $this->company->id,
            'title' => 'TRABAJADOR SOCIAL',
            'source_url' => 'https://bancounion.evaluar.test/vacante/'.uniqid('', true),
            'original_description' => 'Se requiere Licenciatura en Trabajo Social. Lugar: Tarija.',
            'description' => 'Descripción manual',
            'status' => 'error',
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
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('location_name');
            $table->timestamps();
        });
        Schema::create('bot_companies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('bot_source_id')->nullable();
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
