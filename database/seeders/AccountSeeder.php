<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $profesion_ids = Profesion::pluck('id')->toArray();
        foreach ($users as $user) {
            if ($user->email != 'ricardo@email.com' && $user->email != 'carla@email.com') {
                Account::create([
                    'user_id' => $user->id,
                    'account_type_id' => rand(1, 3),
                ]);

                $user_profesions = collect($profesion_ids)->random(rand(1, 3))->toArray();
                $user->myProfesions()->sync($user_profesions);
            }
        }
    }
}
