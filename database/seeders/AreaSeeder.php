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
        $this->admin_id = User::where('email', 'ricardo@email.com')->first()->id;

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

        $area_1 = Area::find(1);
        $profesions_1 = Profesion::whereBetween('id', [1, 31])->get()->pluck('id');
        $area_1->profesions()->sync($profesions_1);

        $area_2 = Area::find(2);
        $profesions_2 = Profesion::whereBetween('id', [32, 38])->get()->pluck('id');
        $area_2->profesions()->sync($profesions_2);

        $area_3 = Area::find(3);
        $profesions_3 = Profesion::whereBetween('id', [39, 61])->get()->pluck('id');
        $area_3->profesions()->sync($profesions_3);

        $area_4 = Area::find(4);
        $profesions_4 = Profesion::whereBetween('id', [62, 81])->get()->pluck('id');
        $area_4->profesions()->sync($profesions_4);

        $area_5 = Area::find(5);
        $profesions_5 = Profesion::whereBetween('id', [82, 139])->get()->pluck('id');
        $area_5->profesions()->sync($profesions_5);

        $area_6 = Area::find(6);
        $profesions_6 = Profesion::whereBetween('id', [140, 149])->get()->pluck('id');
        $area_6->profesions()->sync($profesions_6);
    }
}
