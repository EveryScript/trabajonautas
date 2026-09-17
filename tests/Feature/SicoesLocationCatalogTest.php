<?php

namespace Tests\Feature;

use Database\Seeders\LocationSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SicoesLocationCatalogTest extends TestCase
{
    public function test_location_catalog_is_idempotent_and_contains_all_departments(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('location_name');
            $table->timestamps();
        });

        $seeder = new LocationSeeder;
        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('locations', 10);
        foreach ([
            'Beni',
            'Chuquisaca',
            'Cochabamba',
            'La Paz',
            'Oruro',
            'Pando',
            'Potosí',
            'Santa Cruz',
            'Tarija',
            'No especificado',
        ] as $name) {
            $this->assertDatabaseHas('locations', ['location_name' => $name]);
        }
    }
}
