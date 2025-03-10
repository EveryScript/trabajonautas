<?php

namespace Database\Seeders;

use App\Models\GradeProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeProfileSeeder extends Seeder
{
    public function run(): void
    {
        GradeProfile::create([
            'id' => 1,
            'profile_name' => 'Estudiante'
        ]);
        GradeProfile::create([
            'id' => 2,
            'profile_name' => 'Tecnico medio'
        ]);
        GradeProfile::create([
            'id' => 3,
            'profile_name' => 'Tecnico superior'
        ]);
        GradeProfile::create([
            'id' => 4,
            'profile_name' => 'Egresado (terminó todas las materias y solamente le falta la tesis)'
        ]);
        GradeProfile::create([
            'id' => 5,
            'profile_name' => 'Titulado'
        ]);
    }
}
