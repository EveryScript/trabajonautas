<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        AccountType::create([
            'id' => 1,
            'name' => 'FREE',
            'price' => 0,
            'duration_days' => 0
        ]);
        AccountType::create([
            'id' => 2,
            'name' => 'PRO',
            'price' => 27,
            'duration_days' => 30
        ]);
        AccountType::create([
            'id' => 3,
            'name' => 'PRO-MAX',
            'price' => 30,
            'duration_days' => 35
        ]);
    }
}
