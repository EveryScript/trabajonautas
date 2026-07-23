<?php

namespace Database\Seeders;

use App\Models\BotCompany;
use App\Models\BotSource;
use App\Models\Company;
use App\Support\StoragePath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BotCompanySeeder extends Seeder
{
    public function run(): void
    {
        $evaluarSource = BotSource::updateOrCreate(
            ['slug' => 'evaluar'],
            [
                'name' => 'EVALUAR',
                'description' => 'Empresas con portal Evaluar',
                'icon' => '🤖',
                'scraper_type' => 'evaluar',
                'active' => true,
                'sort_order' => 1,
            ],
        );

        $companies = [
            ['BANCO FIE', 'https://bancofie.evaluar.com/vacantes/'],
            ['BMSC', 'https://postulate.evaluar.com'],
            ['DIACONIA', 'https://diaconia.evaluar.com'],
            ['BANCO FORTALEZA', 'https://grupofortaleza.evaluar.com'],
            ['BANCO SOL', 'https://bancosol.evaluar.com'],
            ['BCP', 'https://bcpbolivia.evaluar.com'],
            ['BANCO UNION', 'https://bancounion.evaluar.com'],
            ['BNB', 'https://bnb.evaluar.com'],
            ['BANCO BISA', 'https://bancobisa.evaluar.com/convocatorias-2/'],
            ['BANCO BDP', 'https://bdp.evaluar.com'],
            ['BANCO ECONOMICO', 'https://bancoeconomico.evaluar.com/ofertas-laborales/'],
            ['ALIANZA SEGUROS', 'https://alianzaseguros.evaluar.com'],
            ['BAGO', 'https://bagobolivia.evaluar.com'],
            ['FARMACORP', 'https://nexocorp.evaluar.com/'],
            ['SINCHI WAYRA', 'https://sinchiwayra.evaluar.com'],
            ['SOBOCE', 'https://soboce.evaluar.com'],
            ['CERVECERIA BOLIVIANA NACIONAL', 'https://cbn.evaluarjobs.com'],
            ['EMBOL', 'https://embol.evaluar.com'],
            ['WORLD VISION BOLIVIA', 'https://wvibolivia.evaluar.com'],
            ['UNIFRANZ', 'https://unifranz.evaluar.com'],
            ['BBO BEBIDAS BOLIVIANAS', 'https://bbo.evaluar.com/'],
            ['TOTTO BOLIVIA', 'https://tottobo.evaluar.com/'],
            ['LA PAPELERA', 'https://lapapelera.evaluar.com/'],
            ['GRUPO KANTUTANI', 'https://grupokantutani.evaluar.com/'],
            ['UNION AGRONEGOCIOS', 'https://unionagronegocios.evaluar.com/'],
            ['LATINA RH', 'https://latinarh.evaluar.com/'],
            ['RED ENLACE', 'https://redenlace.evaluar.com'],
        ];

        foreach ($companies as [$name, $url]) {
            $botCompany = BotCompany::firstOrNew(['slug' => Str::slug($name)]);
            $logo = $botCompany->logo ?: $this->findLogoFor($name) ?: 'empresas/tbn-new-default.webp';

            $botCompany->fill([
                'bot_source_id' => $evaluarSource->id,
                'name' => $name,
                'evaluar_url' => $url,
                'logo' => StoragePath::normalizePublicPath($logo),
                'active' => true,
            ])->save();
        }
    }

    private function findLogoFor(string $name): ?string
    {
        return $this->findLogoFromCompany($name) ?: $this->findLogoFromFiles($name);
    }

    private function findLogoFromCompany(string $name): ?string
    {
        $aliases = $this->aliasesFor($name);

        return Company::withTrashed()
            ->get(['company_name', 'company_image'])
            ->first(function (Company $company) use ($aliases) {
                if (!StoragePath::exists($company->company_image)) {
                    return false;
                }

                $companyName = $this->normalize($company->company_name);

                foreach ($aliases as $alias) {
                    if ($companyName === $alias || Str::contains($companyName, $alias) || Str::contains($alias, $companyName)) {
                        return true;
                    }
                }

                return false;
            })?->company_image;
    }

    private function findLogoFromFiles(string $name): ?string
    {
        $aliases = $this->aliasesFor($name);

        foreach (Storage::disk('public')->files('empresas') as $file) {
            $filename = $this->normalize(pathinfo($file, PATHINFO_FILENAME));

            foreach ($aliases as $alias) {
                if ($filename === $alias || Str::contains($filename, $alias) || Str::contains($alias, $filename)) {
                    return $file;
                }
            }
        }

        return null;
    }

    private function aliasesFor(string $name): array
    {
        $aliases = [
            'BANCO FIE' => ['banco fie', 'bancofie', 'fie'],
            'BMSC' => ['bmsc', 'banco mercantil santa cruz', 'mercantil santa cruz'],
            'DIACONIA' => ['diaconia'],
            'BANCO FORTALEZA' => ['banco fortaleza', 'fortaleza'],
            'BANCO SOL' => ['banco sol', 'bancosol'],
            'BCP' => ['bcp', 'bcp bolivia'],
            'BANCO UNION' => ['banco union', 'union'],
            'BNB' => ['bnb', 'banco nacional de bolivia'],
            'BANCO BISA' => ['banco bisa', 'bisa'],
            'BANCO BDP' => ['banco bdp', 'bdp'],
            'BANCO ECONOMICO' => ['banco economico', 'economico'],
            'ALIANZA SEGUROS' => ['alianza seguros', 'alianza'],
            'BAGO' => ['bago', 'bagobolivia'],
            'FARMACORP' => ['farmacorp', 'nexocorp'],
            'SINCHI WAYRA' => ['sinchi wayra', 'sinchi'],
            'SOBOCE' => ['soboce'],
            'CERVECERIA BOLIVIANA NACIONAL' => ['cerveceria boliviana nacional', 'cbn', 'cerveceria'],
            'EMBOL' => ['embol'],
            'WORLD VISION BOLIVIA' => ['world vision bolivia', 'world vision', 'wvi'],
            'UNIFRANZ' => ['unifranz'],
            'BBO BEBIDAS BOLIVIANAS' => ['bbo bebidas bolivianas', 'bbo'],
            'TOTTO BOLIVIA' => ['totto bolivia', 'totto'],
            'LA PAPELERA' => ['la papelera', 'papelera'],
            'GRUPO KANTUTANI' => ['grupo kantutani', 'kantutani'],
            'UNION AGRONEGOCIOS' => ['union agronegocios'],
            'LATINA RH' => ['latina rh', 'latina'],
            'RED ENLACE' => ['red enlace', 'redenlace'],
        ][$name] ?? [$name];

        return collect([...$aliases, $name])
            ->map(fn(string $alias) => $this->normalize($alias))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }
}
