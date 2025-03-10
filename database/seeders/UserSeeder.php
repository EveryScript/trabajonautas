<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public $user_password = '$2y$12$QBSXragtc3GoveP.au5IuuAr.E6brMpXSyKTruzmqoANh1VIGwOU.'; // 123456789

    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'name' => 'Rick Grimes',
            'email' => 'admin@email.com',
            'password' => $this->user_password,
            'phone' => '72215498',
            'gender' => 'M',
            'age' => 32
        ]);
        User::create([
            'id' => Str::uuid(),
            'name' => 'Daryl Dixon',
            'email' => 'user@email.com',
            'password' => $this->user_password,
            'phone' => '72215498',
            'gender' => 'M',
            'age' => 32
        ]);
        User::create([
            'id' => Str::uuid(),
            'name' => 'Carol Peletier',
            'email' => 'pro@email.com',
            'password' => $this->user_password,
            'phone' => '72215498',
            'gender' => 'M',
            'age' => 32
        ]);
        User::create([
            'id' => Str::uuid(),
            'name' => 'Glenn Rhee',
            'email' => 'free@email.com',
            'password' => $this->user_password,
            'phone' => '72215498',
            'gender' => 'M',
            'age' => 32
        ]);
    }
}
