<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('trabajonautas.seed_users', []) as $seedUser) {
            $email = trim((string) ($seedUser['email'] ?? ''));
            $password = (string) ($seedUser['password'] ?? '');

            if ($email === '' || $password === '') {
                continue;
            }

            User::firstOrCreate(
                ['email' => $email],
                [
                    'id' => Str::uuid(),
                    'name' => trim((string) ($seedUser['name'] ?? 'Usuario')) ?: 'Usuario',
                    'password' => Hash::make($password),
                    'phone' => trim((string) ($seedUser['phone'] ?? '')) ?: null,
                    'register_completed' => false,
                    'email_verified_at' => now(),
                    'terms_accepted_at' => now(),
                ],
            );
        }
    }
}
