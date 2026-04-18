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
        Announcement::factory()
            ->count(120)
            ->create()
            ->each(function ($announcement) {
                $profesion_ids = Profesion::inRandomOrder()->limit(rand(1, 3))->pluck('id');
                $announcement->profesions()->sync($profesion_ids);
                $locations_ids = Location::inRandomOrder()->limit(rand(1, 2))->pluck('id');
                $announcement->locations()->sync($locations_ids);
            });
    }
}
