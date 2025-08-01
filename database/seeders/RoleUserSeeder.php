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

        // PERMISSIONS (routes)
        // Permission::create(['name' => 'panel.announcement.list']);
        // Permission::create(['name' => 'panel.announcement.form']);
        // Permission::create(['name' => 'panel.area.list']);
        // Permission::create(['name' => 'panel.area.form']);
        // Permission::create(['name' => 'panel.company.list']);
        // Permission::create(['name' => 'panel.company.form']);
        // Permission::create(['name' => 'panel.user.list']);
        // Permission::create(['name' => 'panel.user.form']);

        // Admin role
        $role_admin = Role::create(['name' => env('ADMIN_ROLE')]);
        $user_rick = User::where('email', 'admin@email.com')->first();
        $user_rick->assignRole($role_admin);
        // $role_admin->givePermissionTo([
        //     'panel.announcement.list',
        //     'panel.announcement.form',
        //     'panel.area.list',
        //     'panel.area.form',
        //     'panel.company.list',
        //     'panel.company.form',
        //     'panel.user.list',
        //     'panel.user.form'
        // ]);

        // User role
        $role_user = Role::create(['name' => env('USER_ROLE')]);
        $user_daryl = User::where('email', 'user@email.com')->first();
        $user_daryl->assignRole($role_user);
        // $role_user->givePermissionTo([
        //     'panel.announcement.list',
        //     'panel.announcement.form',
        //     'panel.area.list',
        //     'panel.area.form',
        //     'panel.company.list',
        //     'panel.company.form',
        // ]);

        $role_client = Role::create(['name' => env('CLIENT_ROLE')]);
        $user_carol = User::where('email', 'pro@email.com')->first();
        $user_carol->assignRole($role_client);
        $user_glenn = User::where('email', 'free@email.com')->first();
        $user_glenn->assignRole($role_client);
        $user_glenn = User::where('email', 'eugene@email.com')->first();
        $user_glenn->assignRole($role_client);
        $user_glenn = User::where('email', 'carl@email.com')->first();
        $user_glenn->assignRole($role_client);
        $user_glenn = User::where('email', 'maggie@email.com')->first();
        $user_glenn->assignRole($role_client);
    }
}
