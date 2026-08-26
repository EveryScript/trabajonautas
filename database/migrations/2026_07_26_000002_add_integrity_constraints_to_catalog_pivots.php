<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertSafeToConstrain();

        Schema::table('area_profesion', function (Blueprint $table): void {
            $table->unique(['area_id', 'profesion_id'], 'area_profesion_area_profesion_unique');
            $table->foreign('area_id', 'area_profesion_area_fk')
                ->references('id')->on('areas')->cascadeOnDelete();
            $table->foreign('profesion_id', 'area_profesion_profesion_fk')
                ->references('id')->on('profesions')->cascadeOnDelete();
        });

        Schema::table('announcement_profesion', function (Blueprint $table): void {
            $table->unique(
                ['announcement_id', 'profesion_id'],
                'announcement_profesion_announcement_profesion_unique',
            );
            $table->foreign('announcement_id', 'announcement_profesion_announcement_fk')
                ->references('id')->on('announcements')->cascadeOnDelete();
            $table->foreign('profesion_id', 'announcement_profesion_profesion_fk')
                ->references('id')->on('profesions')->cascadeOnDelete();
        });

        Schema::table('announcement_location', function (Blueprint $table): void {
            $table->unique(
                ['announcement_id', 'location_id'],
                'announcement_location_announcement_location_unique',
            );
            $table->foreign('announcement_id', 'announcement_location_announcement_fk')
                ->references('id')->on('announcements')->cascadeOnDelete();
            $table->foreign('location_id', 'announcement_location_location_fk')
                ->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('announcement_location', function (Blueprint $table): void {
            $table->dropForeign('announcement_location_location_fk');
            $table->dropForeign('announcement_location_announcement_fk');
            $table->dropUnique('announcement_location_announcement_location_unique');
        });

        Schema::table('announcement_profesion', function (Blueprint $table): void {
            $table->dropForeign('announcement_profesion_profesion_fk');
            $table->dropForeign('announcement_profesion_announcement_fk');
            $table->dropUnique('announcement_profesion_announcement_profesion_unique');
        });

        Schema::table('area_profesion', function (Blueprint $table): void {
            $table->dropForeign('area_profesion_profesion_fk');
            $table->dropForeign('area_profesion_area_fk');
            $table->dropUnique('area_profesion_area_profesion_unique');
        });
    }

    private function assertSafeToConstrain(): void
    {
        $checks = [
            'area_profesion duplicados' => DB::table('area_profesion')
                ->select(['area_id', 'profesion_id'])
                ->groupBy('area_id', 'profesion_id')
                ->havingRaw('COUNT(*) > 1')
                ->count(),
            'announcement_profesion duplicados' => DB::table('announcement_profesion')
                ->select(['announcement_id', 'profesion_id'])
                ->groupBy('announcement_id', 'profesion_id')
                ->havingRaw('COUNT(*) > 1')
                ->count(),
            'announcement_location duplicados' => DB::table('announcement_location')
                ->select(['announcement_id', 'location_id'])
                ->groupBy('announcement_id', 'location_id')
                ->havingRaw('COUNT(*) > 1')
                ->count(),
            'area_profesion con area huerfana' => DB::table('area_profesion as pivot')
                ->leftJoin('areas', 'areas.id', '=', 'pivot.area_id')
                ->whereNull('areas.id')
                ->count(),
            'area_profesion con profesion huerfana' => DB::table('area_profesion as pivot')
                ->leftJoin('profesions', 'profesions.id', '=', 'pivot.profesion_id')
                ->whereNull('profesions.id')
                ->count(),
            'announcement_profesion con convocatoria huerfana' => DB::table('announcement_profesion as pivot')
                ->leftJoin('announcements', 'announcements.id', '=', 'pivot.announcement_id')
                ->whereNull('announcements.id')
                ->count(),
            'announcement_profesion con profesion huerfana' => DB::table('announcement_profesion as pivot')
                ->leftJoin('profesions', 'profesions.id', '=', 'pivot.profesion_id')
                ->whereNull('profesions.id')
                ->count(),
            'announcement_location con convocatoria huerfana' => DB::table('announcement_location as pivot')
                ->leftJoin('announcements', 'announcements.id', '=', 'pivot.announcement_id')
                ->whereNull('announcements.id')
                ->count(),
            'announcement_location con ubicacion huerfana' => DB::table('announcement_location as pivot')
                ->leftJoin('locations', 'locations.id', '=', 'pivot.location_id')
                ->whereNull('locations.id')
                ->count(),
        ];

        $problems = collect($checks)
            ->filter(fn (int $count): bool => $count > 0)
            ->map(fn (int $count, string $label): string => "{$label}: {$count}")
            ->values();

        if ($problems->isNotEmpty()) {
            throw new RuntimeException(
                "No se aplicaron restricciones. Corrige y documenta primero:\n".$problems->implode("\n"),
            );
        }
    }
};
