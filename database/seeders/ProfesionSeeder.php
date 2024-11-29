<?php

namespace Database\Seeders;

use App\Models\Profesion;
use Carbon\Carbon;
use Database\Factories\ProfesionUserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfesionSeeder extends Seeder
{
    public $now;

    public function run(): void
    {
        $this->now = Carbon::now()->format('Y-m-d H:i:s');

        Profesion::create([
            'id' => 1,
            'profesion_name' => 'Administrador de Empresas',
        ]);
        Profesion::create([
            'id' => 2,
            'profesion_name' => 'Contador Público',
        ]);
        Profesion::create([
            'id' => 3,
            'profesion_name' => 'Auditor',
        ]);
        Profesion::create([
            'id' => 4,
            'profesion_name' => 'Asesor Financiero',
        ]);
        Profesion::create([
            'id' => 5,
            'profesion_name' => 'Economista',
        ]);
        Profesion::create([
            'id' => 6,
            'profesion_name' => 'Analista de Riesgos',
        ]);
        Profesion::create([
            'id' => 7,
            'profesion_name' => 'Ingeniero Civil',
        ]);
        Profesion::create([
            'id' => 8,
            'profesion_name' => 'Ingeniero Industrial',
        ]);
        Profesion::create([
            'id' => 9,
            'profesion_name' => 'Ingeniero de Sistemas',
        ]);
        Profesion::create([
            'id' => 10,
            'profesion_name' => 'Ingeniero Eléctrico',
        ]);
        Profesion::create([
            'id' => 11,
            'profesion_name' => 'Ingeniero en Telecomunicaciones',
        ]);
        Profesion::create([
            'id' => 12,
            'profesion_name' => 'Ingeniero Ambiental',
        ]);
        Profesion::create([
            'id' => 13,
            'profesion_name' => 'Ingeniero Mecánico',
        ]);
        Profesion::create([
            'id' => 14,
            'profesion_name' => 'Ingeniero en Petróleo y Gas',
        ]);

        Profesion::create([
            'id' => 15,
            'profesion_name' => 'Desarrollador de Software',
        ]);
        Profesion::create([
            'id' => 16,
            'profesion_name' => 'Programador',
        ]);
        Profesion::create([
            'id' => 17,
            'profesion_name' => 'Especialista en Ciberseguridad',
        ]);
        Profesion::create([
            'id' => 18,
            'profesion_name' => 'Administrador de Redes',
        ]);
        Profesion::create([
            'id' => 19,
            'profesion_name' => 'Analista de Sistemas',
        ]);
        Profesion::create([
            'id' => 20,
            'profesion_name' => 'Desarrollador Web',
        ]);
        Profesion::create([
            'id' => 21,
            'profesion_name' => 'Especialista en Inteligencia Artificial',
        ]);
        Profesion::create([
            'id' => 22,
            'profesion_name' => 'Diseñador UX/UI',
        ]);

        Profesion::create([
            'id' => 23,
            'profesion_name' => 'Médico General',
        ]);
        Profesion::create([
            'id' => 24,
            'profesion_name' => 'Enfermero',
        ]);
        Profesion::create([
            'id' => 25,
            'profesion_name' => 'Odontólogo',
        ]);
        Profesion::create([
            'id' => 26,
            'profesion_name' => 'Psicólogo',
        ]);
        Profesion::create([
            'id' => 27,
            'profesion_name' => 'Fisioterapeuta',
        ]);
        Profesion::create([
            'id' => 28,
            'profesion_name' => 'Nutricionista',
        ]);
        Profesion::create([
            'id' => 29,
            'profesion_name' => 'Veterinario',
        ]);
        Profesion::create([
            'id' => 30,
            'profesion_name' => 'Farmacéutico',
        ]);
        Profesion::create([
            'id' => 34,
            'profesion_name' => 'Técnico en Laboratorio',
        ]);

        Profesion::create([
            'id' => 35,
            'profesion_name' => 'Profesor de Educación Primaria',
        ]);
        Profesion::create([
            'id' => 36,
            'profesion_name' => 'Profesor de Educación Secundaria',
        ]);
        Profesion::create([
            'id' => 37,
            'profesion_name' => 'Docente Universitario',
        ]);
        Profesion::create([
            'id' => 38,
            'profesion_name' => 'Psicopedagogo',
        ]);
        Profesion::create([
            'id' => 39,
            'profesion_name' => 'Orientador Educativo',
        ]);
        Profesion::create([
            'id' => 40,
            'profesion_name' => 'Tutor de Idiomas',
        ]);
        Profesion::create([
            'id' => 41,
            'profesion_name' => 'Educador Especial',
        ]);

        Profesion::create([
            'id' => 42,
            'profesion_name' => 'Arquitecto',
        ]);
        Profesion::create([
            'id' => 43,
            'profesion_name' => 'Ingeniero en Construcción',
        ]);
        Profesion::create([
            'id' => 44,
            'profesion_name' => 'Maestro de Obras',
        ]);
        Profesion::create([
            'id' => 45,
            'profesion_name' => 'Carpintero',
        ]);
        Profesion::create([
            'id' => 46,
            'profesion_name' => 'Electricista',
        ]);
        Profesion::create([
            'id' => 47,
            'profesion_name' => 'Albañil',
        ]);
        Profesion::create([
            'id' => 48,
            'profesion_name' => 'Plomero',
        ]);

        Profesion::create([
            'id' => 49,
            'profesion_name' => 'Sociólogo',
        ]);
        Profesion::create([
            'id' => 50,
            'profesion_name' => 'Historiador',
        ]);
        Profesion::create([
            'id' => 51,
            'profesion_name' => 'Antropólogo',
        ]);
        Profesion::create([
            'id' => 52,
            'profesion_name' => 'Trabajador Social',
        ]);
        Profesion::create([
            'id' => 53,
            'profesion_name' => 'Politólogo',
        ]);
        Profesion::create([
            'id' => 54,
            'profesion_name' => 'Investigador',
        ]);

        Profesion::create([
            'id' => 55,
            'profesion_name' => 'Biólogo',
        ]);
        Profesion::create([
            'id' => 56,
            'profesion_name' => 'Geólogo',
        ]);
        Profesion::create([
            'id' => 57,
            'profesion_name' => 'Químico',
        ]);
        Profesion::create([
            'id' => 58,
            'profesion_name' => 'Ingeniero Agrónomo',
        ]);
        Profesion::create([
            'id' => 59,
            'profesion_name' => 'Ambientalista',
        ]);
        Profesion::create([
            'id' => 60,
            'profesion_name' => 'Técnico Forestal',
        ]);

        Profesion::create([
            'id' => 61,
            'profesion_name' => 'Comunicador Social',
        ]);
        Profesion::create([
            'id' => 62,
            'profesion_name' => 'Periodista',
        ]);
        Profesion::create([
            'id' => 63,
            'profesion_name' => 'Relacionista Público',
        ]);
        Profesion::create([
            'id' => 64,
            'profesion_name' => 'Especialista en Marketing Digital',
        ]);
        Profesion::create([
            'id' => 65,
            'profesion_name' => 'Diseñador Gráfico',
        ]);
        Profesion::create([
            'id' => 66,
            'profesion_name' => 'Community Manager',
        ]);
        Profesion::create([
            'id' => 67,
            'profesion_name' => 'Publicista',
        ]);

        Profesion::create([
            'id' => 68,
            'profesion_name' => 'Abogado',
        ]);
        Profesion::create([
            'id' => 69,
            'profesion_name' => 'Notario',
        ]);
        Profesion::create([
            'id' => 70,
            'profesion_name' => 'Asesor Legal',
        ]);
        Profesion::create([
            'id' => 71,
            'profesion_name' => 'Juez',
        ]);
        Profesion::create([
            'id' => 72,
            'profesion_name' => 'Fiscal',
        ]);
        Profesion::create([
            'id' => 73,
            'profesion_name' => 'Procurador',
        ]);
        Profesion::create([
            'id' => 74,
            'profesion_name' => 'Defensor Público',
        ]);
    }
}
