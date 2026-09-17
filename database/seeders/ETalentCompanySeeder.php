<?php

namespace Database\Seeders;

use App\Models\BotCompany;
use App\Models\BotSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ETalentCompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $source = BotSource::firstOrNew(['slug' => 'e-talent']);
            $sourceIsNew = ! $source->exists;
            $source->fill([
                'name' => 'E-TALENT',
                'description' => 'Empresas con portal E-Talent',
                'icon' => '💼',
                'scraper_type' => 'etalent',
                'sort_order' => 3,
            ]);
            if ($sourceIsNew) {
                $source->active = true;
            }
            $source->save();

            foreach ($this->companies() as [$name, $url]) {
                $company = BotCompany::firstOrNew([
                    'slug' => 'e-talent-'.Str::slug($name),
                ]);
                $companyIsNew = ! $company->exists;
                $company->fill([
                    'bot_source_id' => $source->id,
                    'name' => $name,
                    'evaluar_url' => $url,
                ]);
                if ($companyIsNew) {
                    $company->active = true;
                }
                $company->save();
            }
        });
    }

    private function companies(): array
    {
        return [
            ['ADIUM BOLIVIA', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=ADIUM'],
            ['BANCO GANADERO', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=BANCO+GANADERO'],
            ['BOLSA BOLIVIANA DE VALORES', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=BOLSA+BOLIVIANA+DE+VALORES'],
            ['AGROPARTNERS', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=AGROPARTNERS'],
            ['COFAR', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=COFAR'],
            ['COMPAÑIA DE ALIMENTOS', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=COMPA%C3%91IA+DE+ALIMENTOS'],
            ['DATEC', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=DATEC'],
            ['DROGUERIA INTI', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=DROGUERIA+INTI'],
            ['LA BOLIVIANA CIACRUZ', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=LA+BOLIVIANA+CIACRUZ'],
            ['LAFAR', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=LAFAR'],
            ['PROESA', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=PROESA'],
            ['CREDINFORM', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=CREDINFORM'],
            ['SOFIA LTDA', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=SOFIA+LTDA'],
            ['TIENDAS 3B', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=TIENDAS+3B'],
            ['YPFB REFINACION', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=YPFB+REFINACION'],
            ['WILDLIFE CONSERVATION SOCIETY', 'https://e-talent.jobs/bolsa-de-trabajo/?search_keywords=WILDLIFE+CONSERVATION+SOCIETY'],
        ];
    }
}
