<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public $now;
    public $admin_id;

    public function run(): void
    {
        /*
        $user = User::where('email', 'ricardo@email.com')->first();
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

        DB::table('announcement_user')->insert([
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
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
