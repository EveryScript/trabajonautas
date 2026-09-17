<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('announcements')) {
            throw new \RuntimeException(
                'No se pueden agregar campos de origen: la tabla announcements no existe.'
            );
        }

        if (! Schema::hasColumn('announcements', 'company_id')) {
            throw new \RuntimeException(
                'No se pueden agregar campos de origen: falta announcements.company_id.'
            );
        }

        $collisions = array_values(array_filter(
            ['source_url', 'source_hash'],
            fn (string $column): bool => Schema::hasColumn('announcements', $column),
        ));

        if ($collisions !== []) {
            throw new \RuntimeException(
                'No se pueden agregar campos de origen; ya existen columnas: '.implode(', ', $collisions).'.'
            );
        }

        Schema::table('announcements', function (Blueprint $table) {
            $table->text('source_url')->nullable()->after('company_id');
            $table->char('source_hash', 64)->nullable()->after('source_url');
            $table->unique('source_hash', 'announcements_source_hash_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('announcements')) {
            throw new \RuntimeException(
                'No se pueden revertir campos de origen: la tabla announcements no existe.'
            );
        }

        $missingColumns = array_values(array_filter(
            ['source_url', 'source_hash'],
            fn (string $column): bool => ! Schema::hasColumn('announcements', $column),
        ));

        if ($missingColumns !== []) {
            throw new \RuntimeException(
                'No se pueden revertir campos de origen; faltan columnas: '.implode(', ', $missingColumns).'.'
            );
        }

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique('announcements_source_hash_unique');
            $table->dropColumn(['source_url', 'source_hash']);
        });
    }
};
