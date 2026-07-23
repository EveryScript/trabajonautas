<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sicoes_scrape_batch_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id');
            $table->foreign('batch_id')->references('id')->on('sicoes_scrape_batches')->cascadeOnDelete();
            $table->foreignId('preview_id')->nullable()->constrained('bot_vacancy_previews')->nullOnDelete();
            $table->string('document_id', 64);
            $table->string('document_hash', 64);
            $table->string('analysis_key', 64)->nullable();
            $table->string('source_hash', 64);
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
            $table->index(['analysis_key', 'status'], 'sicoes_items_analysis_status_idx');
            $table->index(['batch_id', 'status'], 'sicoes_items_batch_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sicoes_scrape_batch_items');
    }
};
