<?php

namespace Tests\Feature;

use App\Jobs\ProcessSicoesJob;
use App\Livewire\Admin\Bot\BotPreview;
use App\Models\Area;
use App\Models\BotCompany;
use App\Models\BotSource;
use App\Models\BotVacancyPreview;
use App\Models\CompanyType;
use App\Models\Profesion;
use App\Models\SicoesScrapeBatch;
use App\Models\SicoesScrapeBatchItem;
use App\Models\User;
use App\Services\Bot\SicoesDocumentImporterService;
use App\Services\Bot\SicoesRunnerService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use ReflectionMethod;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SicoesBatchIsolationTest extends TestCase
{
    private ?string $basePath = null;

    private BotSource $source;

    private BotCompany $botCompany;

    private Area $area;

    private Profesion $profession;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->basePath = storage_path('framework/testing/sicoes-batch-isolation-'.Str::uuid());
        $this->deleteTemporaryRunDirectory();
        $this->createSchema();
        $this->authenticateAdministrator();

        $this->source = BotSource::create([
            'name' => 'SICOES',
            'slug' => 'sicoes',
            'scraper_type' => 'sicoes',
            'active' => true,
        ]);
        $this->botCompany = BotCompany::create([
            'bot_source_id' => $this->source->id,
            'name' => 'SICOES',
            'slug' => 'sicoes',
            'active' => true,
        ]);
        $this->area = Area::create([
            'area_name' => 'Area SOCIAL',
            'description' => 'Area SOCIAL',
        ]);
        $this->profession = Profesion::create(['profesion_name' => 'Trabajo Social']);
        $this->area->profesions()->sync([$this->profession->id]);
        CompanyType::create(['company_type_name' => 'Publica']);

        config()->set('sicoes.ai.provider', 'anthropic');
        config()->set('services.anthropic.api_key', 'test-key');
        config()->set('services.anthropic.model', 'claude-haiku-4-5-20251001');
    }

    protected function tearDown(): void
    {
        $this->deleteTemporaryRunDirectory();
        Storage::disk('public')->deleteDirectory('convocatorias/sicoes');

        parent::tearDown();
    }

    private function deleteTemporaryRunDirectory(): void
    {
        if ($this->basePath === null) {
            return;
        }

        $testingRoot = str_replace('\\', '/', storage_path('framework/testing')).'/';
        $candidate = str_replace('\\', '/', $this->basePath);

        if (! str_starts_with($candidate, $testingRoot.'sicoes-batch-isolation-')) {
            throw new \RuntimeException('Se rechazó limpiar una ruta fuera del directorio temporal de pruebas.');
        }

        File::deleteDirectory($this->basePath);
    }

    private function authenticateAdministrator(): void
    {
        $role = (new Role)->forceFill([
            'name' => 'ADMIN',
            'guard_name' => 'web',
        ]);
        $user = (new User)->forceFill([
            'id' => (string) Str::uuid(),
            'name' => 'BOT Test Administrator',
            'email' => 'bot-admin@example.test',
            'email_verified_at' => now(),
        ]);
        $user->setRelation('roles', collect([$role]));

        $this->actingAs($user);
    }

    public function test_same_document_is_cached_and_batches_do_not_mix_previews(): void
    {
        $run = $this->writeRun(
            cuce: '26-0291-07-1669139-1-2',
            title: 'Equipo PRP Construccion Carretera Okinawa',
            text: 'Consultoria Individual de Linea. ITEM 1 Coordinador Bs. 14.402 mensual. ITEM 2 Especialista Social Bs. 12.788 mensual. Persona natural adjudicada como consultor.',
        );
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response($this->acceptedClaudeResponse()),
        ]);

        $firstBatch = $this->batch('00000000-0000-4000-8000-000000000001');
        $first = app(SicoesDocumentImporterService::class)->importRun(
            run: $run,
            botCompanyId: $this->botCompany->id,
            userId: 'test-user',
            batchId: $firstBatch->id,
        );

        $this->assertSame(1, $first['saved']);
        $this->assertSame(1, $first['ai_calls']);
        $this->assertSame(0, $first['ai_cache_hits']);
        $preview = BotVacancyPreview::sole();
        $this->assertSame('1', $preview->salary);
        $this->assertSame('2026-07-20 23:59:00', $preview->expiration_date);
        $this->assertSame([$this->profession->id], $preview->selected_profession_ids);
        $this->assertStringContainsString('#doc-', $preview->source_url);

        $secondBatch = $this->batch('00000000-0000-4000-8000-000000000002');
        $second = app(SicoesDocumentImporterService::class)->importRun(
            run: $run,
            botCompanyId: $this->botCompany->id,
            userId: 'test-user',
            batchId: $secondBatch->id,
        );

        $this->assertSame(0, $second['ai_calls']);
        $this->assertSame(1, $second['ai_cache_hits']);
        $this->assertSame(1, BotVacancyPreview::count());
        $this->assertSame($preview->id, SicoesScrapeBatchItem::where('batch_id', $secondBatch->id)->value('preview_id'));
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request->hasHeader('x-api-key', 'test-key'));

        $component = $this->mountedPreview($secondBatch->id);
        $this->assertSame([$preview->id], $this->visiblePreviewIds($component));

        $emptyBatch = $this->batch(
            '00000000-0000-4000-8000-000000000003',
            ['created_at' => now()->addMinute(), 'updated_at' => now()->addMinute()],
        );
        $component = $this->mountedPreview();
        $this->assertSame($emptyBatch->id, $component->currentBatchId);
        $this->assertSame([], $this->visiblePreviewIds($component));
    }

    public function test_strong_company_document_is_recorded_as_discarded_without_preview_or_ai_call(): void
    {
        $run = $this->writeRun(
            cuce: '26-1510-00-1672333-1-1',
            title: 'Plan Autonomico de Desarrollo',
            text: 'La empresa consultora debe presentar matricula de comercio vigente y poder del representante legal.',
        );
        Http::fake();
        $batch = $this->batch('00000000-0000-4000-8000-000000000010');

        $result = app(SicoesDocumentImporterService::class)->importRun(
            run: $run,
            botCompanyId: $this->botCompany->id,
            userId: 'test-user',
            batchId: $batch->id,
        );

        $this->assertSame(1, $result['document_discarded']);
        $this->assertSame(0, $result['ai_calls']);
        $this->assertSame(0, BotVacancyPreview::count());
        $item = SicoesScrapeBatchItem::sole();
        $this->assertSame('discarded', $item->status);
        $this->assertSame('rejected_company', $item->contract_type);
        $this->assertFalse($item->eligible);
        Http::assertNothingSent();
    }

    public function test_changed_prompt_version_does_not_reuse_cached_analysis(): void
    {
        $run = $this->writeRun(
            cuce: '26-0291-07-1669139-1-2',
            title: 'Consultoria Individual de Linea',
            text: 'Consultoria Individual de Linea para persona natural.',
        );
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response($this->acceptedClaudeResponse()),
        ]);
        $importer = app(SicoesDocumentImporterService::class);
        $firstBatch = $this->batch('00000000-0000-4000-8000-000000000011');

        $first = $importer->importRun($run, $this->botCompany->id, 'test-user', $firstBatch->id);
        $cachedItem = SicoesScrapeBatchItem::where('batch_id', $firstBatch->id)->sole();
        $keyMethod = new ReflectionMethod(SicoesDocumentImporterService::class, 'analysisKey');
        $keyMethod->setAccessible(true);
        $cachedItem->update([
            'analysis_key' => $keyMethod->invoke(
                $importer,
                ['document_hash' => $cachedItem->document_hash],
                'previous-prompt-version',
            ),
        ]);

        $secondBatch = $this->batch('00000000-0000-4000-8000-000000000012');
        $second = $importer->importRun($run, $this->botCompany->id, 'test-user', $secondBatch->id);

        $this->assertSame(1, $first['ai_calls']);
        $this->assertSame(1, $second['ai_calls']);
        $this->assertSame(0, $second['ai_cache_hits']);
        Http::assertSentCount(2);
    }

    public function test_missing_batch_does_not_fall_back_to_latest_batch(): void
    {
        $latestBatch = $this->batch('00000000-0000-4000-8000-000000000020');
        $preview = $this->preview('Preview from latest batch', 'https://example.test/latest');
        $this->batchItem($latestBatch, $preview, 'latest-document');

        $component = $this->mountedPreview('00000000-0000-4000-8000-999999999999');

        $this->assertTrue($component->batchNotFound);
        $this->assertNull($component->currentBatchId);
        $this->assertSame([], $this->visiblePreviewIds($component));
    }

    public function test_batch_status_messages_are_explicit(): void
    {
        $queued = $this->batch('00000000-0000-4000-8000-000000000031', ['status' => 'queued']);
        $running = $this->batch('00000000-0000-4000-8000-000000000032', ['status' => 'running']);
        $finished = $this->batch('00000000-0000-4000-8000-000000000033', [
            'status' => 'completed',
            'documents_processed' => 5,
            'discarded_count' => 5,
            'previews_count' => 0,
        ]);
        $failed = $this->batch('00000000-0000-4000-8000-000000000034', ['status' => 'failed']);

        $this->livewirePreview($queued->id)->assertSee('La ejecucion esta en espera.');
        $this->livewirePreview($running->id)->assertSee('La ejecucion esta procesando documentos.');
        $this->livewirePreview($finished->id)
            ->assertSee('Se procesaron 5 documentos. 5 fueron descartados. No existen convocatorias publicables en este lote.');
        $this->livewirePreview($failed->id)->assertSee('La ejecucion finalizo con error.');
    }

    public function test_visible_batch_query_excludes_previews_from_other_batches(): void
    {
        $oldBatch = $this->batch('00000000-0000-4000-8000-000000000041');
        $visibleBatch = $this->batch('00000000-0000-4000-8000-000000000042');
        $oldPreview = $this->preview('Old preview', 'https://example.test/old');
        $visiblePreview = $this->preview('Visible preview', 'https://example.test/visible');
        $this->batchItem($oldBatch, $oldPreview, 'old-document');
        $this->batchItem($visibleBatch, $visiblePreview, 'visible-document');

        $component = $this->mountedPreview($visibleBatch->id);

        $this->assertSame([$visiblePreview->id], $this->visiblePreviewIds($component));
        $this->assertNotContains($oldPreview->id, $this->visiblePreviewIds($component));
    }

    public function test_job_persists_real_document_count_instead_of_heuristic_count(): void
    {
        $batch = $this->batch('00000000-0000-4000-8000-000000000050', ['status' => 'queued']);
        $runner = \Mockery::mock(SicoesRunnerService::class);
        $runner->shouldReceive('run')->once()->andReturn([
            'status' => 'OK',
            'date' => '07/07/2099',
            'slug' => '07-07-2099',
            'json_path' => $this->basePath.'/fichas-finales/07-07-2099.json',
            'sicoes_items' => 1,
        ]);
        $importer = \Mockery::mock(SicoesDocumentImporterService::class);
        $importer->shouldReceive('importRun')->once()->andReturn([
            'total_items_feed' => 5,
            'document_processed' => 5,
            'shown_in_batch' => 1,
            'document_discarded' => 4,
            'document_errors' => 0,
            'saved' => 0,
            'updated' => 1,
            'ai_calls' => 0,
            'ai_cache_hits' => 1,
            'ai_errors' => 0,
            'errors' => [],
        ]);

        (new ProcessSicoesJob('2099-07-07', $this->botCompany->id, 'test-user', $batch->id))
            ->handle($runner, $importer);

        $batch->refresh();
        $this->assertSame(5, $batch->documents_found);
        $this->assertSame(5, $batch->documents_downloaded);
        $this->assertSame(5, $batch->documents_processed);
    }

    private function acceptedClaudeResponse(): array
    {
        return [
            'content' => [[
                'type' => 'text',
                'text' => json_encode([
                    'eligible' => true,
                    'contract_type' => 'multiple_individual',
                    'es_oportunidad_consultor_persona' => true,
                    'tipo_oportunidad' => 'consultor_linea',
                    'debe_descartarse' => false,
                    'motivo_descarte' => null,
                    'evidencia_clasificacion' => 'Consultoria Individual de Linea por items.',
                    'cuce' => '26-0291-07-1669139-1-2',
                    'titulo_objeto' => 'Equipo PRP Construccion Carretera Okinawa',
                    'area_ids' => [$this->area->id],
                    'ubicacion_detectada' => [
                        'texto' => 'Warnes, Santa Cruz',
                        'municipio' => 'Warnes',
                        'departamento' => 'Santa Cruz',
                        'confianza' => 'alta',
                    ],
                    'lugar_trabajo' => 'Area del proyecto en Warnes, Santa Cruz.',
                    'duracion_contrato' => 'Hasta el 31 de diciembre de 2099.',
                    'modalidad_postulacion' => 'Presentacion electronica mediante RUPE.',
                    'sueldo' => [
                        'valor' => 1,
                        'detalle' => 'Varios cargos con montos diferentes.',
                        'sueldos' => [
                            ['cargo' => 'Coordinador', 'monto' => 'Bs. 14.402,00', 'periodicidad' => 'mensual', 'evidencia' => 'Bs. 14.402 mensual'],
                            ['cargo' => 'Especialista Social', 'monto' => 'Bs. 12.788,00', 'periodicidad' => 'mensual', 'evidencia' => 'Bs. 12.788 mensual'],
                        ],
                        'revision_manual' => false,
                    ],
                    'detalle_sueldos' => "COORDINADOR: Bs. 14.402 mensual\nESPECIALISTA SOCIAL: Bs. 12.788 mensual",
                    'descripcion' => 'Convocatoria para consultores individuales.',
                    'evidencias' => [
                        'cuce' => '26-0291-07-1669139-1-2',
                        'area_profesional' => 'Especialista Social.',
                        'lugar_trabajo' => 'Warnes, Santa Cruz.',
                        'duracion_contrato' => 'Hasta el 31 de diciembre de 2099.',
                        'modalidad_postulacion' => 'RUPE.',
                        'sueldo' => 'Montos mensuales por item.',
                    ],
                    'advertencias' => [],
                ]),
            ]],
            'stop_reason' => 'end_turn',
            'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
        ];
    }

    private function writeRun(string $cuce, string $title, string $text): array
    {
        $slug = '07-07-2099';
        $words = $this->basePath.'/entrada/words/'.$slug;
        $rows = $this->basePath.'/salida/convocatorias';
        $texts = $this->basePath.'/salida/resultados/'.$slug.'/textos-extraidos';
        File::ensureDirectoryExists($words);
        File::ensureDirectoryExists($rows);
        File::ensureDirectoryExists($texts);
        File::ensureDirectoryExists($this->basePath.'/fichas-finales');

        $filename = "01_{$cuce}_01_Documento_Base_de_Contratacion.docx";
        File::put($words.'/'.$filename, 'stable-document-binary-'.$cuce);
        File::put($texts.'/01_'.$cuce.'.txt', $text);
        File::put($rows.'/'.$slug.'.json', json_encode([[
            'cuce' => $cuce,
            'entidad' => 'Administradora Boliviana de Carreteras',
            'objetoContratacion' => $title,
            'fechaPublicacion' => '07/07/2099',
            'fechaPresentacion' => '20/07/2026',
            'ficha' => "https://www.sicoes.gob.bo/portal/contrataciones/ficha/fichaProceso.php?cp={$cuce}",
        ]]));

        return [
            'status' => 'OK',
            'date' => '07/07/2099',
            'slug' => $slug,
            'json_path' => $this->basePath.'/fichas-finales/'.$slug.'.json',
            'sicoes_items' => 1,
        ];
    }

    private function batch(string $id, array $extra = []): SicoesScrapeBatch
    {
        return SicoesScrapeBatch::create(array_merge([
            'id' => $id,
            'bot_company_id' => $this->botCompany->id,
            'requested_date' => '2099-07-07',
            'status' => 'finished',
        ], $extra));
    }

    private function mountedPreview(?string $batchId = null): BotPreview
    {
        $query = $batchId ? ['batch' => $batchId] : [];
        app()->instance('request', LaravelRequest::create('/admin/bot/sicoes/empresa/sicoes', 'GET', $query));
        $component = new BotPreview;
        $component->mount($this->source, $this->botCompany);

        return $component;
    }

    private function livewirePreview(string $batchId)
    {
        return Livewire::withQueryParams(['batch' => $batchId])->test(BotPreview::class, [
            'source' => $this->source,
            'company' => $this->botCompany,
        ]);
    }

    private function preview(string $title, string $sourceUrl): BotVacancyPreview
    {
        return BotVacancyPreview::create([
            'bot_company_id' => $this->botCompany->id,
            'title' => $title,
            'source_url' => $sourceUrl,
            'status' => 'preview',
        ]);
    }

    private function batchItem(SicoesScrapeBatch $batch, BotVacancyPreview $preview, string $identity): SicoesScrapeBatchItem
    {
        $documentId = hash('sha256', $identity);

        return SicoesScrapeBatchItem::create([
            'batch_id' => $batch->id,
            'preview_id' => $preview->id,
            'document_id' => $documentId,
            'document_hash' => hash('sha256', $identity.'-content'),
            'source_hash' => hash('sha256', $preview->source_url),
            'source_url' => $preview->source_url,
            'cuce' => '26-0000-00-0000000-1-1',
            'filename' => $identity.'.docx',
            'status' => 'preview',
            'eligible' => true,
            'contract_type' => 'individual',
        ]);
    }

    private function visiblePreviewIds(BotPreview $component): array
    {
        $method = new ReflectionMethod($component, 'batchPreviewsQuery');
        $method->setAccessible(true);

        return $method->invoke($component)->pluck('id')->map(fn ($id): int => (int) $id)->all();
    }

    private function createSchema(): void
    {
        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->string('area_name');
            $table->string('description')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
        Schema::create('profesions', function (Blueprint $table): void {
            $table->id();
            $table->string('profesion_name');
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
        Schema::create('area_profesion', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('profesion_id');
            $table->timestamps();
        });
        Schema::create('bot_sources', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('scraper_type');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('bot_companies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('bot_source_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('evaluar_url')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('company_types', function (Blueprint $table): void {
            $table->id();
            $table->string('company_type_name');
            $table->timestamps();
        });
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name');
            $table->text('description')->nullable();
            $table->string('company_image')->nullable();
            $table->string('user_id')->nullable();
            $table->unsignedBigInteger('company_type_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('location_name');
            $table->timestamps();
        });
        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->text('source_url')->nullable();
            $table->string('source_hash', 64)->nullable();
            $table->timestamps();
        });
        Schema::create('bot_vacancy_previews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('bot_company_id');
            $table->string('title');
            $table->text('source_url');
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
        Schema::create('sicoes_scrape_batches', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('bot_company_id');
            $table->date('requested_date');
            $table->string('status');
            $table->unsignedInteger('documents_found')->default(0);
            $table->unsignedInteger('documents_downloaded')->default(0);
            $table->unsignedInteger('documents_processed')->default(0);
            $table->unsignedInteger('previews_count')->default(0);
            $table->unsignedInteger('discarded_count')->default(0);
            $table->unsignedInteger('errors_count')->default(0);
            $table->unsignedInteger('ai_calls')->default(0);
            $table->unsignedInteger('ai_cache_hits')->default(0);
            $table->json('summary')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
        Schema::create('sicoes_scrape_batch_items', function (Blueprint $table): void {
            $table->id();
            $table->uuid('batch_id');
            $table->unsignedBigInteger('preview_id')->nullable();
            $table->string('document_id', 64);
            $table->string('document_hash', 64);
            $table->string('analysis_key', 64)->nullable();
            $table->string('source_hash', 64);
            $table->text('source_url');
            $table->string('cuce');
            $table->string('filename');
            $table->string('status');
            $table->boolean('eligible')->nullable();
            $table->string('contract_type')->nullable();
            $table->text('discard_reason')->nullable();
            $table->text('classification_evidence')->nullable();
            $table->json('analysis_result')->nullable();
            $table->json('ai_metadata')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();
            $table->unique(['batch_id', 'document_id']);
        });
    }
}
