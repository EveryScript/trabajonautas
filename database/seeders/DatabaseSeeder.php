<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleUserSeeder::class,
            CompanyTypeSeeder::class,
            LocationSeeder::class,
            ProfesionSeeder::class,
            CompanySeeder::class,
            AreaSeeder::class,
            // AnnouncementSeeder::class,
            GradeProfileSeeder::class,
            AccountTypeSeeder::class,
            // ClientSeeder::class,
            // NoticeSeeder::class,
            TbnSettingSeeder::class
        ]);
    }
}
