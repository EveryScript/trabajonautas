<?php

namespace Database\Seeders;

use App\Models\TbnSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TbnSettingSeeder extends Seeder
{
    public function run(): void
    {
        TbnSetting::create([
            'id' => 1,
            'key' => 'qr_image',
            'value' => 'img/tbn-qr.webp'
        ]);
    }
}
