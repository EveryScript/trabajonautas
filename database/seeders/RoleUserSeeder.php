<?php

namespace Database\Seeders;

use App\Models\User;
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
        $role_admin = Role::firstOrCreate([
            'name' => config('trabajonautas.roles.admin', 'ADMIN'),
        ]);
        $adminEmail = config('trabajonautas.seed_users.admin.email');
        $user_admin = $adminEmail ? User::where('email', $adminEmail)->first() : null;
        $user_admin?->assignRole($role_admin);

        // User role
        $role_user = Role::firstOrCreate([
            'name' => config('trabajonautas.roles.user', 'USER'),
        ]);
        $userEmail = config('trabajonautas.seed_users.user.email');
        $user = $userEmail ? User::where('email', $userEmail)->first() : null;
        $user?->assignRole($role_user);

        // Client role
        Role::firstOrCreate([
            'name' => config('trabajonautas.roles.client', 'CLIENT'),
        ]);

        // Create permission
        Permission::firstOrCreate(['name' => 'support-permission']);
    }
}
