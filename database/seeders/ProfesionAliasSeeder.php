<?php

namespace Database\Seeders;

use App\Services\ProfessionCatalogSynchronizer;
use Illuminate\Database\Seeder;

class ProfesionAliasSeeder extends Seeder
{
    public function run(ProfessionCatalogSynchronizer $synchronizer): void
    {
        $synchronizer->synchronize(
            areas: AreaSeeder::catalog(),
            professions: ProfesionSeeder::catalog(),
            aliases: self::catalog(),
        );
    }

    public static function catalog(): array
    {
        return [
            ['profesion_name' => 'Trabajo Social', 'alias' => 'Licenciatura en Trabajo Social'],
            ['profesion_name' => 'Trabajo Social', 'alias' => 'Trabajador Social'],
            ['profesion_name' => 'Trabajo Social', 'alias' => 'Trabajadora Social'],
            ['profesion_name' => 'Ingeniería Financiera', 'alias' => 'Ing. Financiera'],
            ['profesion_name' => 'Ingeniería Financiera', 'alias' => 'Ing. Financiero'],
            ['profesion_name' => 'Ingeniería Financiera', 'alias' => 'Ingeniero Financiero'],
            ['profesion_name' => 'Ingeniería Financiera', 'alias' => 'Ingeniera Financiera'],
            ['profesion_name' => 'Contaduría', 'alias' => 'Contaduría Pública'],
            ['profesion_name' => 'Contaduría', 'alias' => 'Contador Público'],
            ['profesion_name' => 'Contaduría', 'alias' => 'Contadora Pública'],
            ['profesion_name' => 'Sistemas e Informática', 'alias' => 'Ingeniería de Sistemas'],
            ['profesion_name' => 'Sistemas e Informática', 'alias' => 'Ingeniero de Sistemas'],
            ['profesion_name' => 'Sistemas e Informática', 'alias' => 'Ingeniera de Sistemas'],
            ['profesion_name' => 'Administración de Empresas', 'alias' => 'Administrador de Empresas'],
            ['profesion_name' => 'Administración de Empresas', 'alias' => 'Administradora de Empresas'],
            ['profesion_name' => 'Psicología', 'alias' => 'Licenciatura en Psicología'],
            ['profesion_name' => 'Psicología', 'alias' => 'Psicólogo'],
            ['profesion_name' => 'Psicología', 'alias' => 'Psicóloga'],
            ['profesion_name' => 'Civil y Construcciones Civiles', 'alias' => 'Ingeniería Civil'],
            ['profesion_name' => 'Civil y Construcciones Civiles', 'alias' => 'Ingeniero Civil'],
            ['profesion_name' => 'Civil y Construcciones Civiles', 'alias' => 'Ingeniera Civil'],
            ['profesion_name' => 'Eléctrico/a', 'alias' => 'Ingeniería Eléctrica'],
            ['profesion_name' => 'Eléctrico/a', 'alias' => 'Ingeniero Eléctrico'],
            ['profesion_name' => 'Eléctrico/a', 'alias' => 'Ingeniera Eléctrica'],
            ['profesion_name' => 'Administración de Empresas', 'alias' => 'Licenciado de carreras Administrativas'],
            ['profesion_name' => 'Economía', 'alias' => 'Licenciado de carreras Económicas'],
            ['profesion_name' => 'Ingeniería Financiera', 'alias' => 'Licenciado de carreras Financieras'],
            ['profesion_name' => 'Contaduría', 'alias' => 'Licenciado de carreras Contables'],
        ];
    }
}
