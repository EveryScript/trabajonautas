<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'name' => 'Ricardo Oropeza',
            'email' => 'ricardooropeza15@gmail.com',
            'password' => '$2y$12$BS2F7EhZnvwMMEThXrBefuAkAbvYR59RWqR.BF/S0kju0fV2p131O', // 6z0CuhgqQ)£4#uL
            'phone' => '73858162',
            'gender' => 'M',
            'age' => 3,
            'register_completed' => true,
            'location_id' => 1,
            'grade_profile_id' => 5,
            'area_id' => 1,
        ]);

        User::create([
            'id' => Str::uuid(),
            'name' => 'Carla Vargas',
            'email' => 'carlyxime@gmail.com',
            'password' => '$2y$12$YbRwCBUo3YcvRkNuRfSfmu22Xtrf4za5NFYIpAgjPxOsg23dFWSy2', // c0=6:3WFYR6FGNB
            'phone' => '69616052',
            'gender' => 'F',
            'age' => 3,
            'register_completed' => true,
            'location_id' => 1,
            'grade_profile_id' => 5,
            'area_id' => 1,
        ]);

        User::create([
            'id' => Str::uuid(),
            'name' => 'Cliente Prueba',
            'email' => 'cliente@email.com',
            'password' => '$2y$12$QBSXragtc3GoveP.au5IuuAr.E6brMpXSyKTruzmqoANh1VIGwOU.', // 123456789
            'phone' => '69616052',
            'gender' => 'M',
            'age' => 1,
            'register_completed' => true,
            'location_id' => 1,
            'grade_profile_id' => 1,
            'area_id' => 1,
        ]);
    }
}
