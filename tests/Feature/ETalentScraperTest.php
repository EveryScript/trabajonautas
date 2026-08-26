<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\BotCompany;
use App\Models\BotVacancyPreview;
use App\Models\Location;
use App\Models\Profesion;
use App\Services\Bot\ETalentScraperService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ETalentScraperTest extends TestCase
{
    private BotCompany $company;

    private Area $area;

    private Profesion $profession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();

        config()->set('services.gemini.key', 'test-key');
        config()->set('services.etalent.allowed_host_suffixes', ['e-talent.test']);
        config()->set('services.etalent.feed_per_page', 100);

        $this->company = BotCompany::create([
            'name' => 'BANCO GANADERO',
            'slug' => 'e-talent-banco-ganadero',
            'evaluar_url' => 'https://e-talent.test/bolsa-de-trabajo/?search_keywords=BANCO+GANADERO',
            'active' => true,
        ]);
        $this->area = Area::create([
            'area_name' => 'Economía y Administración',
            'description' => 'Área administrativa',
            'user_id' => null,
        ]);
        $this->profession = Profesion::create([
            'profesion_name' => 'Ingeniería Comercial',
            'user_id' => null,
        ]);
        $this->area->profesions()->sync([$this->profession->id]);
        Location::create(['location_name' => 'Santa Cruz']);
    }

    public function test_feed_url_keeps_company_filter_and_requests_up_to_one_hundred_jobs(): void
    {
        $feeds = app(ETalentScraperService::class)->feedUrls(
            'https://e-talent.test/bolsa-de-trabajo/?search_keywords=BANCO+GANADERO',
        );

        $this->assertCount(1, $feeds);
        $this->assertStringContainsString('feed=job_feed', $feeds[0]);
        $this->assertStringContainsString('search_keywords=BANCO%20GANADERO', $feeds[0]);
        $this->assertStringContainsString('posts_per_page=100', $feeds[0]);
    }

    public function test_unsafe_or_unfiltered_url_is_rejected(): void
    {
        $service = app(ETalentScraperService::class);

        try {
            $service->feedUrls('https://evil.test/bolsa-de-trabajo/?search_keywords=DATEC');
            $this->fail('Un host no autorizado no debe aceptarse.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertStringContainsString('portal de empleos permitido', $exception->getMessage());
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('search_keywords');
        $service->feedUrls('https://e-talent.test/bolsa-de-trabajo/');
    }

    public function test_complete_scrape_reuses_evaluar_classification_location_and_preview_flow(): void
    {
        $this->fakePortalAndGemini();

        $summary = app(ETalentScraperService::class)->scrapeCompany(
            company: $this->company,
            startDate: '2026-07-27',
            endDate: '2026-07-29',
            batchId: 'e-talent-batch-1',
        );

        $this->assertSame('OK', $summary['status']);
        $this->assertSame(1, $summary['total_items_feed']);
        $this->assertSame(1, $summary['saved']);
        $this->assertSame(1, $summary['shown_in_batch']);
        $this->assertSame(1, $summary['gemini_calls']);

        $preview = BotVacancyPreview::sole();
        $this->assertSame('preview', $preview->status);
        $this->assertSame([$this->profession->id], $preview->selected_profession_ids);
        $this->assertSame($this->area->id, $preview->selected_area_id);
        $this->assertSame('Santa Cruz', $preview->department);
        $this->assertSame('SANTA CRUZ DE LA SIERRA', $preview->location);
        $this->assertSame('2026-08-17 23:59:00', $preview->expiration_date);
        $this->assertSame('etalent', data_get($preview->raw_data, 'source'));
        $this->assertSame(
            'Ingeniería Comercial',
            data_get($preview->raw_data, 'profession_resolution.profesiones_resueltas.0.profesion_name'),
        );
        $this->assertStringContainsString(
            'https://e-talent.test/trabajo/lider-experiencia-cliente/',
            $preview->description,
        );

        Http::assertSent(
            fn (Request $request): bool => str_contains($request->url(), 'feed=job_feed')
                && str_contains($request->url(), 'posts_per_page=100'),
        );
        Http::assertSent(
            fn (Request $request): bool => $request->url()
                === 'https://e-talent.test/trabajo/lider-experiencia-cliente/',
        );
    }

    public function test_unchanged_preview_is_reused_without_a_second_gemini_call(): void
    {
        $this->fakePortalAndGemini();
        $service = app(ETalentScraperService::class);

        $first = $service->scrapeCompany(
            $this->company,
            '2026-07-27',
            '2026-07-29',
            'e-talent-batch-1',
        );
        $second = $service->scrapeCompany(
            $this->company,
            '2026-07-27',
            '2026-07-29',
            'e-talent-batch-2',
        );

        $this->assertSame(1, $first['gemini_calls']);
        $this->assertSame(0, $second['gemini_calls']);
        $this->assertSame(1, $second['already_previewed']);
        $this->assertSame(1, $second['gemini_skipped_existing_preview']);
        $this->assertSame(1, BotVacancyPreview::count());

        Http::assertSentCount(4);
        Http::assertSent(
            fn (Request $request): bool => str_contains($request->url(), 'generativelanguage.googleapis.com'),
        );
    }

    public function test_out_of_range_job_does_not_call_detail_or_gemini(): void
    {
        Http::fake([
            'https://e-talent.test/*' => Http::response($this->rss(), 200, [
                'Content-Type' => 'application/rss+xml',
            ]),
        ]);

        $summary = app(ETalentScraperService::class)->scrapeCompany(
            $this->company,
            '2026-07-01',
            '2026-07-10',
            'e-talent-old-range',
        );

        $this->assertSame(1, $summary['skipped_out_of_range']);
        $this->assertSame(0, $summary['gemini_calls']);
        $this->assertSame(0, BotVacancyPreview::count());
        Http::assertSentCount(1);
    }

    private function fakePortalAndGemini(): void
    {
        Http::fake(function (Request $request) {
            if (str_contains($request->url(), 'generativelanguage.googleapis.com')) {
                return Http::response([
                    'candidates' => [[
                        'content' => ['parts' => [[
                            'text' => json_encode([
                                'profesiones_encontradas' => [[
                                    'nombre_original' => 'Ingeniería Comercial',
                                    'nombre_catalogo' => 'Ingeniería Comercial',
                                    'evidencia' => 'Licenciatura en Ingeniería Comercial.',
                                    'tipo_requisito' => 'alternativa',
                                    'confianza' => 0.99,
                                ]],
                                'acepta_carreras_afines' => false,
                                'evidencia_carreras_afines' => '',
                                'area_principal_catalogo' => 'Economía y Administración',
                                'evidencia_area_principal' => 'Licenciatura en Ingeniería Comercial.',
                                'confianza_area_principal' => 0.99,
                                'ubicacion_departamento' => 'Santa Cruz',
                                'ubicacion' => 'Santa Cruz de la Sierra',
                                'sueldo' => '0',
                                'fecha_expiracion' => '2026-08-17',
                            ], JSON_UNESCAPED_UNICODE),
                        ]]],
                    ]],
                    'usageMetadata' => [
                        'promptTokenCount' => 100,
                        'candidatesTokenCount' => 50,
                        'totalTokenCount' => 150,
                    ],
                ]);
            }

            if (str_contains($request->url(), 'feed=job_feed')) {
                return Http::response($this->rss(), 200, [
                    'Content-Type' => 'application/rss+xml',
                ]);
            }

            if ($request->url() === 'https://e-talent.test/trabajo/lider-experiencia-cliente/') {
                return Http::response(
                    '<html><head><script type="application/ld+json">'
                    .json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'JobPosting',
                        'validThrough' => '2026-08-17T23:59:59+00:00',
                        'jobLocation' => [
                            '@type' => 'Place',
                            'address' => [
                                '@type' => 'PostalAddress',
                                'addressLocality' => 'SANTA CRUZ DE LA SIERRA',
                                'addressCountry' => 'BO',
                            ],
                        ],
                    ], JSON_UNESCAPED_UNICODE)
                    .'</script></head></html>',
                    200,
                    ['Content-Type' => 'text/html'],
                );
            }

            return Http::response('', 404);
        });
    }

    private function rss(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
    <title>E-Talent</title>
    <item>
      <title>LÍDER EXPERIENCIA DEL CLIENTE</title>
      <link>https://e-talent.test/trabajo/lider-experiencia-cliente/</link>
      <pubDate>Tue, 28 Jul 2026 13:58:00 +0000</pubDate>
      <content:encoded><![CDATA[
        <p>Buscamos liderazgo para experiencia del cliente.</p>
        <p>Base: Santa Cruz.</p>
        <p>Licenciatura en Ingeniería Comercial.</p>
      ]]></content:encoded>
    </item>
  </channel>
</rss>
XML;
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
        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->string('source_url')->nullable();
            $table->string('source_hash')->nullable();
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
