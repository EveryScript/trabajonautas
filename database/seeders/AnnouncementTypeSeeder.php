<?php

namespace Database\Seeders;

use App\Models\AnnouncementType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementTypeSeeder extends Seeder
{
    public function run(): void
    {
        AnnouncementType::create([
            'id' => 1,
            'name' => 'Pasantía',
            'description' => 'Este tipo de convocatorias son para profesionales junior sin experiencia.',
            'slug' => 'pasantia',
        ]);
        AnnouncementType::create([
            'id' => 2,
            'name' => 'Voluntariado',
            'description' => 'Este tipo de convocatorias son para profesionales que buscan contribuir a una causa social, comunitaria o ambiental (NO siempre remunerada).',
            'slug' => 'voluntariado',
        ]);
        AnnouncementType::create([
            'id' => 3,
            'name' => 'Bachiller',
            'description' => 'Este tipo de convocatorias son para trabajos de corta duración, en las que solo se requiere ser bachiller para asumir el cargo, por ejemplo: Encuestador.',
            'slug' => 'bachiller',
        ]);
    }
}
