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
            CompanyTypeSeeder::class,
            LocationSeeder::class,
            ProfesionSeeder::class,
            CompanySeeder::class,
            AreaSeeder::class,
            // AnnouncementSeeder::class,
            RoleUserSeeder::class,
            GradeProfileSeeder::class,
            AccountTypeSeeder::class,
            // NoticeSeeder::class,
            TbnSettingSeeder::class
        ]);
    }
}
