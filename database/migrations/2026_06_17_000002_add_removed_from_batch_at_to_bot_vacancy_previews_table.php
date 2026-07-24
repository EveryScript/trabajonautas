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
                'No se puede agregar removed_from_batch_at: la tabla bot_vacancy_previews no existe.'
            );
        }

        if (! Schema::hasColumn('bot_vacancy_previews', 'scrape_batch_id')) {
            throw new \RuntimeException(
                'No se puede agregar removed_from_batch_at: falta la columna scrape_batch_id.'
            );
        }

        if (Schema::hasColumn('bot_vacancy_previews', 'removed_from_batch_at')) {
            throw new \RuntimeException(
                'No se puede agregar removed_from_batch_at: la columna ya existe y debe revisarse manualmente.'
            );
        }

        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            $table->timestamp('removed_from_batch_at')->nullable()->after('scrape_batch_id');
        });
    }

    public function down(): void
    {
        if (
            ! Schema::hasTable('bot_vacancy_previews')
            || ! Schema::hasColumn('bot_vacancy_previews', 'removed_from_batch_at')
        ) {
            throw new \RuntimeException(
                'No se puede revertir removed_from_batch_at: falta la tabla o la columna esperada.'
            );
        }

        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            $table->dropColumn('removed_from_batch_at');
        });
    }
};
