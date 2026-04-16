<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'ricardooropeza15@gmail.com')->first();
        if (!$admin) return;

        $account_types = AccountType::all();

        User::factory()->count(20)->create()->each(function (User $user) use ($account_types, $admin) {
            $user->assignRole(config('app.client_role'));
            $type = $account_types->random();
            $user->account()->create([
                'account_type_id' => $type->id,
                'limit_time' => $type->id === 2 || $type->id === 3 ? fake()->dateTimeBetween('10 days', '30 days') : null
            ]);
            $user->subscriptions()->create([
                'account_type_id' => $type->id,
                'price' => $type->price,
                'verified_payment' => true,
                'verified_by_user_id' => $admin->id
            ]);
        });
    }
}
