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
            'key' => 'qr_pro',
            'value' => 'img/tbn-qr-pro.webp'
        ]);
        TbnSetting::create([
            'id' => 2,
            'key' => 'qr_promax',
            'value' => 'img/tbn-qr-promax.webp'
        ]);
        TbnSetting::create([
            'id' => 3,
            'key' => 'bg_web_image',
            'value' => 'img/tbn-space-reverse.webp'
        ]);
        TbnSetting::create([
            'id' => 4,
            'key' => 'thumb_web_image',
            'value' => 'img/tbn-astro-belt.webp'
        ]);
    }
}
