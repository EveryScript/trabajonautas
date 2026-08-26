<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
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
        ];

        DB::transaction(function () use ($departments): void {
            foreach ($departments as $department) {
                $normalized = $this->normalize($department);
                $existing = Location::query()
                    ->get(['id', 'location_name'])
                    ->first(fn (Location $location): bool => $this->normalize($location->location_name) === $normalized);

                if ($existing) {
                    if ($existing->location_name !== $department) {
                        $existing->update(['location_name' => $department]);
                    }

                    continue;
                }

                Location::firstOrCreate(['location_name' => $department]);
            }
        });

        Cache::forget('locations');
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }
}
