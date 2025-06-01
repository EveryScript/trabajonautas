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
        $user_password = '$2y$12$QBSXragtc3GoveP.au5IuuAr.E6brMpXSyKTruzmqoANh1VIGwOU.'; // 123456789

        $users = [
            [
                'name' => 'Rick Grimes',
                'email' => 'admin@email.com',
            ],
            [
                'name' => 'Daryl Dixon',
                'email' => 'user@email.com',
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
    }
}
