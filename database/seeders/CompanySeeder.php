<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'company_name' => 'Impuestos Nacionales',
            'description' => 'El Servicio de Impuestos Nacionales (SIN) tiene la misión de recaudar los recursos provenientes de los impuestos nacionales que el Estado Plurinacional requiere para consolidar el Modelo Económico Social Comunitario Productivo para el Vivir Bien.',
            'company_image' => 'empresas/tbn-new-default.webp',
            'user_id' => 1,
            'company_type_id' => 1
        ]);
        /*
        Company::factory()->count(20)->create();
        */
    }
}
