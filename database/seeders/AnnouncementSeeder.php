<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\Profesion;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public $now;
    public $admin_id;

    public function run(): void
    {
        $announcement = Announcement::create([
            'announce_title' => 'Se solicita trabajador',
            'description' => 'Bienvenido a trabajonautas.com, el portal de convocatorias de empleo más grande de toda Bolivia',
            'expiration_time' => now()->addDays(30),
            'salary' => fake()->numberBetween(1500, 60000),
            'pro' => false,
            'area_id' => 1,
            'user_id' => 1,
            'company_id' => 1,
        ]);
        $profesion_ids = Profesion::inRandomOrder()->limit(rand(1, 3))->pluck('id');
        $announcement->profesions()->sync($profesion_ids);
        $locations_ids = Location::inRandomOrder()->limit(rand(1, 2))->pluck('id');
        $announcement->locations()->sync($locations_ids);
        /*
        Announcement::factory()
            ->count(20)
            ->create()
            ->each(function ($announcement) {
                $profesion_ids = Profesion::inRandomOrder()->limit(rand(1, 3))->pluck('id');
                $announcement->profesions()->sync($profesion_ids);
                $locations_ids = Location::inRandomOrder()->limit(rand(1, 2))->pluck('id');
                $announcement->locations()->sync($locations_ids);
            });
        */
    }
}
