<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sicoes_scrape_batch_items')) {
            throw new \RuntimeException(
                'No se puede crear sicoes_scrape_batch_items: la tabla ya existe y debe revisarse manualmente.'
            );
        }

        $missingTables = array_values(array_filter(
            ['sicoes_scrape_batches', 'bot_vacancy_previews'],
            fn (string $table): bool => ! Schema::hasTable($table),
        ));

        if ($missingTables !== []) {
            throw new \RuntimeException(
                'No se puede crear sicoes_scrape_batch_items; faltan tablas: '.implode(', ', $missingTables).'.'
            );
        }

        Schema::create('sicoes_scrape_batch_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id');
            $table->foreign('batch_id')->references('id')->on('sicoes_scrape_batches')->cascadeOnDelete();
            $table->foreignId('preview_id')->nullable()->constrained('bot_vacancy_previews')->nullOnDelete();
            $table->char('document_id', 64);
            $table->char('document_hash', 64);
            $table->char('analysis_key', 64)->nullable();
            $table->char('source_hash', 64);
            $table->text('source_url');
            $table->string('cuce', 80);
            $table->string('filename');
            $table->string('status', 32);
            $table->boolean('eligible')->nullable();
            $table->string('contract_type', 64)->nullable();
            $table->text('discard_reason')->nullable();
            $table->text('classification_evidence')->nullable();
            $table->json('analysis_result')->nullable();
            $table->json('ai_metadata')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'document_id'], 'sicoes_batch_document_unique');
            $table->index(['analysis_key', 'id'], 'sicoes_items_analysis_id_idx');
            $table->index(['batch_id', 'status', 'removed_at'], 'sicoes_items_batch_status_removed_idx');
            $table->index(['batch_id', 'preview_id', 'status'], 'sicoes_items_batch_preview_status_idx');
            $table->index('source_hash', 'sicoes_items_source_hash_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sicoes_scrape_batch_items')) {
            throw new \RuntimeException(
                'No se puede revertir sicoes_scrape_batch_items: la tabla esperada no existe.'
            );
        }

        Schema::drop('sicoes_scrape_batch_items');
    }
};
