<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ProfessionCatalogSynchronizer;
use App\Services\ProfessionNameNormalizer;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfessionCatalogSynchronizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_catalog_sync_is_idempotent_creates_relations_last_and_clears_caches(): void
    {
        $service = $this->synchronizer();
        $areas = [[
            'id' => 9001,
            'area_name' => 'Área de prueba',
            'description' => 'Catálogo de prueba',
        ]];
        $professions = [[
            'id' => 9001,
            'profesion_name' => 'Profesión de prueba',
            'area_id' => 9001,
        ]];
        $aliases = [[
            'profesion_name' => 'Profesión de prueba',
            'alias' => 'Profesional de prueba',
        ]];
        foreach ([
            'areas',
            'profesions',
            'announcement_profesions_with_areas',
            'profesions_with_areas',
            'profesions_list',
        ] as $cacheKey) {
            Cache::put($cacheKey, 'stale', 60);
        }

        Carbon::setTestNow('2026-07-26 10:00:00');
        $first = $service->synchronize($areas, $professions, $aliases);
        $timestamps = [
            'area' => \App\Models\Area::query()->findOrFail(9001)->updated_at->toDateTimeString(),
            'profession' => \App\Models\Profesion::query()->findOrFail(9001)->updated_at->toDateTimeString(),
            'relation' => DB::table('area_profesion')->where('area_id', 9001)->value('updated_at'),
            'alias' => DB::table('profesion_aliases')->value('updated_at'),
        ];
        Carbon::setTestNow('2026-07-26 11:00:00');
        $second = $service->synchronize($areas, $professions, $aliases);

        $this->assertSame(1, $first['areas_created']);
        $this->assertSame(1, $first['professions_created']);
        $this->assertSame(1, $first['relations_created']);
        $this->assertSame(1, $first['aliases_created']);
        $this->assertSame(0, $second['areas_created']);
        $this->assertSame(0, $second['professions_created']);
        $this->assertSame(0, $second['relations_created']);
        $this->assertSame(0, $second['aliases_created']);
        $this->assertDatabaseCount('areas', 1);
        $this->assertDatabaseCount('profesions', 1);
        $this->assertDatabaseCount('area_profesion', 1);
        $this->assertDatabaseCount('profesion_aliases', 1);
        $this->assertDatabaseHas('area_profesion', [
            'area_id' => 9001,
            'profesion_id' => 9001,
        ]);
        $this->assertNull(Cache::get('areas'));
        $this->assertNull(Cache::get('profesions_with_areas'));
        $this->assertSame(
            $timestamps['area'],
            \App\Models\Area::query()->findOrFail(9001)->updated_at->toDateTimeString(),
        );
        $this->assertSame(
            $timestamps['profession'],
            \App\Models\Profesion::query()->findOrFail(9001)->updated_at->toDateTimeString(),
        );
        $this->assertSame(
            $timestamps['relation'],
            DB::table('area_profesion')->where('area_id', 9001)->value('updated_at'),
        );
        $this->assertSame($timestamps['alias'], DB::table('profesion_aliases')->value('updated_at'));
    }

    public function test_dry_run_reports_changes_without_writing(): void
    {
        $result = $this->synchronizer()->synchronize(
            areas: [[
                'id' => 9010,
                'area_name' => 'Área dry run',
                'description' => 'No debe persistir',
            ]],
            professions: [[
                'id' => 9010,
                'profesion_name' => 'Profesión dry run',
                'area_id' => 9010,
            ]],
            dryRun: true,
        );

        $this->assertTrue($result['dry_run']);
        $this->assertSame(1, $result['areas_created']);
        $this->assertSame(1, $result['professions_created']);
        $this->assertDatabaseMissing('areas', ['id' => 9010]);
        $this->assertDatabaseMissing('profesions', ['id' => 9010]);
    }

    private function synchronizer(): ProfessionCatalogSynchronizer
    {
        $owner = new User;
        $owner->id = (string) Str::uuid();

        return new class(app(ProfessionNameNormalizer::class), $owner) extends ProfessionCatalogSynchronizer
        {
            public function __construct(ProfessionNameNormalizer $normalizer, private User $owner)
            {
                parent::__construct($normalizer);
            }

            public function resolveAdministrator(?string $userId = null): User
            {
                return $this->owner;
            }
        };
    }

    private function createSchema(): void
    {
        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->string('area_name')->unique();
            $table->string('description');
            $table->uuid('user_id');
            $table->timestamps();
        });
        Schema::create('profesions', function (Blueprint $table): void {
            $table->id();
            $table->string('profesion_name')->unique();
            $table->uuid('user_id');
            $table->timestamps();
        });
        Schema::create('area_profesion', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('profesion_id');
            $table->timestamps();
        });
        Schema::create('profesion_aliases', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('profesion_id');
            $table->string('alias');
            $table->string('alias_normalizado')->unique();
            $table->timestamps();
        });
    }
}
