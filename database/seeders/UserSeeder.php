<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'name' => 'Ricardo Oropeza',
            'email' => 'ricardo@email.com',
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
            'email' => 'carla@email.com',
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
            'register_completed' => false,
            'location_id' => 1,
            'grade_profile_id' => 1,
            'area_id' => 1,
        ]);

        /*
        $user_password = '$2y$12$QBSXragtc3GoveP.au5IuuAr.E6brMpXSyKTruzmqoANh1VIGwOU.'; // 123456789

        $users = [
            [
                'name' => 'Ricardo Oropeza',
                'email' => 'ricardo@email.com',
            ],
            [
                'name' => 'Carla Vargas',
                'email' => 'carla@email.com',
            ],
            [
                'name' => 'Carol Peletier',
                'email' => 'pro@email.com',
            ],
            [
                'name' => 'Glenn Rhee',
                'email' => 'free@email.com',
            ],
            [
                'name' => 'Eugene Porter',
                'email' => 'eugene@email.com',
            ],
            [
                'name' => 'Carl Grimes',
                'email' => 'carl@email.com',
            ],
            [
                'name' => 'Maggie Rhee',
                'email' => 'maggie@email.com',
            ],
        ];

        foreach ($users as $user_data) {
            $user = User::create([
                'id' => Str::uuid(),
                'name' => $user_data['name'],
                'email' => $user_data['email'],
                'password' => $user_password,
                'phone' => '72215498',
                'gender' => 'M',
                'age' => rand(1, 3),
                'register_completed' => true,
                'location_id' => rand(1, 9),
                'grade_profile_id' => rand(1, 5),
                'area_id' => rand(1, 10),
            ]);
        }
        */
    }
}
