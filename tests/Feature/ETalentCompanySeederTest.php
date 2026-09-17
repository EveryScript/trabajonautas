<?php

namespace Tests\Feature;

use App\Models\BotCompany;
use App\Models\BotSource;
use Database\Seeders\ETalentCompanySeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ETalentCompanySeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('bot_sources', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('scraper_type');
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
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
    }

    public function test_seeder_is_idempotent_and_loads_the_sixteen_requested_companies(): void
    {
        $seeder = app(ETalentCompanySeeder::class);
        $seeder->run();
        BotSource::where('slug', 'e-talent')->update(['active' => false]);
        BotCompany::where('slug', 'e-talent-datec')->update(['active' => false]);
        $seeder->run();

        $source = BotSource::where('slug', 'e-talent')->sole();
        $companies = BotCompany::where('bot_source_id', $source->id)->orderBy('name')->get();

        $this->assertSame('etalent', $source->scraper_type);
        $this->assertFalse($source->active);
        $this->assertCount(16, $companies);
        $this->assertSame(16, $companies->pluck('slug')->unique()->count());
        $this->assertTrue($companies->every(
            fn (BotCompany $company): bool => str_starts_with(
                $company->evaluar_url,
                'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=',
            ),
        ));
        $this->assertSame(
            'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=DATEC',
            $companies->firstWhere('name', 'DATEC')?->evaluar_url,
        );
        $this->assertFalse((bool) $companies->firstWhere('name', 'DATEC')?->active);
        $this->assertSame(
            'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=COMPA%C3%91IA+DE+ALIMENTOS',
            $companies->firstWhere('name', 'COMPAÑIA DE ALIMENTOS')?->evaluar_url,
        );
    }
}
