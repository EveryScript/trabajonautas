<?php

namespace Database\Seeders;

use App\Models\BotCompany;
use App\Models\BotSource;
use Illuminate\Database\Seeder;

class BotSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'EVALUAR',
                'slug' => 'evaluar',
                'description' => 'Empresas con portal Evaluar',
                'icon' => '🤖',
                'scraper_type' => 'evaluar',
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'SICOES',
                'slug' => 'sicoes',
                'description' => 'Convocatorias públicas desde SICOES',
                'icon' => '🏛️',
                'scraper_type' => 'sicoes',
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'E-TALENT',
                'slug' => 'e-talent',
                'description' => 'Empresas con portal E-Talent',
                'icon' => '💼',
                'scraper_type' => 'etalent',
                'active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($sources as $source) {
            BotSource::updateOrCreate(
                ['slug' => $source['slug']],
                $source,
            );
        }

        $sicoesSource = BotSource::where('slug', 'sicoes')->first();

        if ($sicoesSource) {
            BotCompany::updateOrCreate(
                ['slug' => 'sicoes'],
                [
                    'bot_source_id' => $sicoesSource->id,
                    'name' => 'SICOES',
                    'evaluar_url' => 'https://www.sicoes.gob.bo',
                    'logo' => 'empresas/tbn-new-default.webp',
                    'active' => true,
                ],
            );
        }
    }
}
