<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Profesion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public $now;
    public $admin_id;

    public function run(): void
    {
        $this->now = Carbon::now()->format('Y-m-d H:i:s');
        $this->admin_id = User::where('email', 'ricardooropeza15@gmail.com')->first()->id;

        Area::create([
            'id' => 1,
            'area_name' => 'Área Económica, Administrativa y Financiera',
            'description' => 'Gestión de recursos, análisis de mercados y optimización de procesos para alcanzar objetivos empresariales y maximizar la rentabilidad.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 2,
            'area_name' => 'Área Legal',
            'description' => 'Conjunto de normas y regulaciones que rigen las interacciones sociales, garantizando derechos y obligaciones dentro de un marco jurídico.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 3,
            'area_name' => 'Área Social',
            'description' => 'Estudia las relaciones humanas y estructuras sociales, abordando problemas comunitarios y promoviendo el bienestar y la cohesión social.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 4,
            'area_name' => 'Área Salud',
            'description' => 'Promoción y mantenimiento del bienestar físico, mental y social, a través de servicios médicos y políticas de salud pública.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 5,
            'area_name' => 'Área Ingeniería',
            'description' => 'Aplicación de principios científicos y matemáticos para diseñar, construir y mejorar infraestructuras, productos y sistemas tecnológicos.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 6,
            'area_name' => 'Áreas poco frecuentes',
            'description' => 'Ámbitos especializados como que combinan múltiples disciplinas para abordar profesiones únicas y complejas.',
            'user_id' => $this->admin_id
        ]);
    }
}
