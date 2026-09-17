<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bot_vacancy_previews')) {
            throw new \RuntimeException(
                'No se puede crear bot_vacancy_previews: la tabla ya existe y debe revisarse manualmente.'
            );
        }

        $missingTables = array_values(array_filter(
            ['bot_companies', 'announcements'],
            fn (string $table): bool => ! Schema::hasTable($table),
        ));

        if ($missingTables !== []) {
            throw new \RuntimeException(
                'No se puede crear bot_vacancy_previews; faltan tablas: '.implode(', ', $missingTables).'.'
            );
        }

        Schema::create('bot_vacancy_previews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_company_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('source_url')->unique();
            $table->longText('original_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('area')->nullable();
            $table->text('professions')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('expiration_date')->nullable();
            $table->string('salary')->nullable();
            $table->json('raw_data')->nullable();
            $table->string('status')->default('preview');
            $table->foreignId('convocatoria_id')->nullable()->constrained('announcements')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bot_vacancy_previews')) {
            throw new \RuntimeException(
                'No se puede revertir bot_vacancy_previews: la tabla esperada no existe.'
            );
        }

        Schema::drop('bot_vacancy_previews');
    }
};
