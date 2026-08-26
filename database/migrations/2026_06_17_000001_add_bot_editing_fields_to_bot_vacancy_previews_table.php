<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bot_vacancy_previews')) {
            throw new \RuntimeException(
                'No se pueden agregar campos de edición: la tabla bot_vacancy_previews no existe.'
            );
        }

        $missingTables = array_values(array_filter(
            ['companies', 'areas'],
            fn (string $table): bool => ! Schema::hasTable($table),
        ));

        if ($missingTables !== []) {
            throw new \RuntimeException(
                'No se pueden agregar campos de edición; faltan tablas: '.implode(', ', $missingTables).'.'
            );
        }

        $missingBaseColumns = array_values(array_filter(
            ['status', 'convocatoria_id'],
            fn (string $column): bool => ! Schema::hasColumn('bot_vacancy_previews', $column),
        ));

        if ($missingBaseColumns !== []) {
            throw new \RuntimeException(
                'No se pueden agregar campos de edición; faltan columnas base: '.implode(', ', $missingBaseColumns).'.'
            );
        }

        $newColumns = [
            'scrape_batch_id',
            'selected_company_id',
            'selected_area_id',
            'selected_profession_ids',
            'selected_location_ids',
            'is_pro',
            'attachments',
        ];
        $collisions = array_values(array_filter(
            $newColumns,
            fn (string $column): bool => Schema::hasColumn('bot_vacancy_previews', $column),
        ));

        if ($collisions !== []) {
            throw new \RuntimeException(
                'No se pueden agregar campos de edición; ya existen columnas: '.implode(', ', $collisions).'.'
            );
        }

        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            $table->string('scrape_batch_id')->nullable()->after('status');
            $table->index('scrape_batch_id', 'bot_previews_scrape_batch_idx');
            $table->foreignId('selected_company_id')->nullable()->after('convocatoria_id')->constrained('companies')->nullOnDelete();
            $table->foreignId('selected_area_id')->nullable()->after('selected_company_id')->constrained('areas')->nullOnDelete();
            $table->json('selected_profession_ids')->nullable()->after('selected_area_id');
            $table->json('selected_location_ids')->nullable()->after('selected_profession_ids');
            $table->boolean('is_pro')->default(false)->after('selected_location_ids');
            $table->json('attachments')->nullable()->after('is_pro');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bot_vacancy_previews')) {
            throw new \RuntimeException(
                'No se pueden revertir campos de edición: la tabla bot_vacancy_previews no existe.'
            );
        }

        $columns = [
            'scrape_batch_id',
            'selected_company_id',
            'selected_area_id',
            'selected_profession_ids',
            'selected_location_ids',
            'is_pro',
            'attachments',
        ];
        $missingColumns = array_values(array_filter(
            $columns,
            fn (string $column): bool => ! Schema::hasColumn('bot_vacancy_previews', $column),
        ));

        if ($missingColumns !== []) {
            throw new \RuntimeException(
                'No se pueden revertir campos de edición; faltan columnas: '.implode(', ', $missingColumns).'.'
            );
        }

        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            $table->dropForeign(['selected_area_id']);
            $table->dropForeign(['selected_company_id']);
            $table->dropIndex('bot_previews_scrape_batch_idx');
            $table->dropColumn([
                'attachments',
                'is_pro',
                'selected_location_ids',
                'selected_profession_ids',
                'selected_area_id',
                'selected_company_id',
                'scrape_batch_id',
            ]);
        });
    }
};
