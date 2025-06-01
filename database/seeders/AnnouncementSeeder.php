<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public $now;
    public $admin_id;

    public function run(): void
    {
        Announcement::factory()
            ->count(20)
            ->create()
            ->each(function ($announcement) {
                $profesion_ids = Profesion::inRandomOrder()->limit(rand(1, 3))->pluck('id');
                $announcement->profesions()->sync($profesion_ids);
                $locations_ids = Location::inRandomOrder()->limit(rand(1, 2))->pluck('id');
                $announcement->locations()->sync($locations_ids);
            });

        /*$this->now = Carbon::now()->format('Y-m-d H:i:s');
        $this->admin_id = User::where('email', 'admin@email.com')->first()->id;
        Announcement::create([
            'id' => 1,
            'announce_title' => 'Se requiere un abogado',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad dolor atque et nesciunt fugiat molestias inventore iusto pariatur quisquam beatae?',
            'expiration_time' => $this->now,
            'salary' => 6500,
            'announce_file' => 'convocatorias/document.pdf',
            'company_id' => 2,
            'user_id' => $this->admin_id,
            'area_id' => 3
        ]);
        Announcement::create([
            'id' => 2,
            'announce_title' => 'Se requiere un ingeniero',
            'description' => 'Lorem ipsum dolor sit amet dolor atque et nesciunt fugiat molestias inventore iusto pariatur quisquam beatae?',
            'expiration_time' => $this->now,
            'salary' => 5600,
            'announce_file' => 'convocatorias/document.pdf',
            'company_id' => 4,
            'user_id' => $this->admin_id,
            'area_id' => 5
        ]);
        Announcement::create([
            'id' => 3,
            'announce_title' => 'Se requiere un arquitecto',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad dolor atque et nesciunt fugiat molestias inventore iusto pariatur',
            'expiration_time' => $this->now,
            'salary' => 9800,
            'announce_file' => 'convocatorias/document.pdf',
            'company_id' => 5,
            'user_id' => $this->admin_id,
            'area_id' => 7
        ]);
        Announcement::create([
            'id' => 4,
            'announce_title' => 'Se requiere un consultor',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad dolor atque et nesciunt fugiat molestias inventore iusto pariatur',
            'expiration_time' => $this->now,
            'salary' => 3544,
            'announce_file' => 'convocatorias/document.pdf',
            'company_id' => 5,
            'user_id' => $this->admin_id,
            'area_id' => 2
        ]);
        Announcement::create([
            'id' => 5,
            'announce_title' => 'Se requiere un doctor',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad dolor atque et nesciunt fugiat molestias inventore iusto pariatur',
            'expiration_time' => $this->now,
            'salary' => 4870,
            'announce_file' => 'convocatorias/document.pdf',
            'company_id' => 5,
            'user_id' => $this->admin_id,
            'area_id' => 6
        ]);
        DB::table('announcement_location')->insert([
            'id' => 1,
            'announcement_id' => 1,
            'location_id' => 4,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 2,
            'announcement_id' => 1,
            'location_id' => 5,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 3,
            'announcement_id' => 2,
            'location_id' => 2,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 4,
            'announcement_id' => 2,
            'location_id' => 3,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 5,
            'announcement_id' => 2,
            'location_id' => 4,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 6,
            'announcement_id' => 3,
            'location_id' => 6,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 7,
            'announcement_id' => 4,
            'location_id' => 3,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 8,
            'announcement_id' => 4,
            'location_id' => 4,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);
        DB::table('announcement_location')->insert([
            'id' => 9,
            'announcement_id' => 5,
            'location_id' => 6,
            'created_at' => $this->now,
            'updated_at' => $this->now
        ]);*/
    }
}
