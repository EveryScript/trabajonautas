<?php

namespace Database\Seeders;

use App\Models\AnnouncementType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Bachiller',
            'Pasantía',
            'Voluntariado',
        ];

        foreach ($types as $typeName) {
            AnnouncementType::firstOrCreate(
                ['slug' => Str::slug($typeName)],
                [
                    'name' => $typeName,
                    'is_active' => true,
                ]
            );
        }
    }
}
