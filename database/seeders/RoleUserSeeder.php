<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Admin role
        $role_admin = Role::create(['name' => env('ADMIN_ROLE')]);
        $user_admin = User::where('email', 'ricardooropeza15@gmail.com')->first();
        $user_admin->assignRole($role_admin);

        // User role
        $role_user = Role::create(['name' => env('USER_ROLE')]);
        $user = User::where('email', 'carlyxime@gmail.com')->first();
        $user->assignRole($role_user);

        // Client role
        $role_client = Role::create(['name' => env('CLIENT_ROLE')]);
        $user_client = User::where('email', 'cliente@email.com')->first();
        $user_client->assignRole($role_client);
    }
}
