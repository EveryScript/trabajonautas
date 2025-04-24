<?php

namespace Database\Seeders;

use App\Models\Area;
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
        $this->admin_id = User::where('email', 'admin@email.com')->first()->id;

        Area::create([
            'id' => 1,
            'area_name' => 'Administración y Finanzas',
            'description' => 'Ciencias de la computación y tecnología de la información',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 2,
            'area_name' => 'Ingeniería',
            'description' => 'Ciencias de la abogacia y las otras leyes.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 3,
            'area_name' => 'Tecnología y Desarrollo de Software',
            'description' => 'Trabajo con muchos planos y maquetas tridimensionales.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 4,
            'area_name' => 'Salud y Medicina',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 5,
            'area_name' => 'Educación y Formación',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 6,
            'area_name' => 'Construcción y Arquitectura',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 7,
            'area_name' => 'Ciencias Sociales y Humanidades',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 8,
            'area_name' => 'Ciencias Naturales y Medio Ambiente',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 9,
            'area_name' => 'Comunicación y Marketing',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
        Area::create([
            'id' => 10,
            'area_name' => 'Derecho y Ciencias Jurídicas',
            'description' => 'Ciencia de la geologia y otras ramas que estudian piedras preciosas.',
            'user_id' => $this->admin_id
        ]);
    }
}
