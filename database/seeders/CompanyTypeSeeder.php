<?php

namespace Database\Seeders;

use App\Models\CompanyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyTypeSeeder extends Seeder
{
    public function run(): void
    {
        CompanyType::create([
            'id' => 1,
            'company_type_name' => 'Pública'
        ]);
        CompanyType::create([
            'id' => 2,
            'company_type_name' => 'Privada'
        ]);
        CompanyType::create([
            'id' => 3,
            'company_type_name' => 'ONG'
        ]);
    }
}
