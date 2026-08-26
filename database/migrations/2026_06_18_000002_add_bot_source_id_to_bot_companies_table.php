<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $missingTables = array_values(array_filter(
            ['bot_companies', 'bot_sources'],
            fn (string $table): bool => ! Schema::hasTable($table),
        ));

        if ($missingTables !== []) {
            throw new \RuntimeException(
                'No se puede agregar bot_source_id; faltan tablas: '.implode(', ', $missingTables).'.'
            );
        }

        if (Schema::hasColumn('bot_companies', 'bot_source_id')) {
            throw new \RuntimeException(
                'No se puede agregar bot_source_id: la columna ya existe y debe revisarse manualmente.'
            );
        }

        Schema::table('bot_companies', function (Blueprint $table) {
            $table->foreignId('bot_source_id')
                ->nullable()
                ->after('id')
                ->constrained('bot_sources')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (
            ! Schema::hasTable('bot_companies')
            || ! Schema::hasTable('bot_sources')
            || ! Schema::hasColumn('bot_companies', 'bot_source_id')
        ) {
            throw new \RuntimeException(
                'No se puede revertir bot_source_id: falta una tabla o la columna esperada.'
            );
        }

        Schema::table('bot_companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bot_source_id');
        });
    }
};
