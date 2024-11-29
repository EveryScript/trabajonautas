<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::create([
            'id' => 1,
            'location_name' => 'La Paz'
        ]);
        Location::create([
            'id' => 2,
            'location_name' => 'Cochabamba'
        ]);
        Location::create([
            'id' => 3,
            'location_name' => 'Santa Cruz'
        ]);
        Location::create([
            'id' => 4,
            'location_name' => 'Beni'
        ]);
        Location::create([
            'id' => 5,
            'location_name' => 'Pando'
        ]);
        Location::create([
            'id' => 6,
            'location_name' => 'Tarija'
        ]);
        Location::create([
            'id' => 7,
            'location_name' => 'Oruro'
        ]);
        Location::create([
            'id' => 8,
            'location_name' => 'Potosi'
        ]);
        Location::create([
            'id' => 9,
            'location_name' => 'Chuquisaca'
        ]);
    }
}
