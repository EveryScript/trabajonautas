<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sicoes_scrape_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('bot_company_id')->constrained()->cascadeOnDelete();
            $table->date('requested_date');
            $table->string('status', 32)->default('queued');
            $table->unsignedInteger('documents_found')->default(0);
            $table->unsignedInteger('documents_downloaded')->default(0);
            $table->unsignedInteger('documents_processed')->default(0);
            $table->unsignedInteger('previews_count')->default(0);
            $table->unsignedInteger('discarded_count')->default(0);
            $table->unsignedInteger('errors_count')->default(0);
            $table->unsignedInteger('ai_calls')->default(0);
            $table->unsignedInteger('ai_cache_hits')->default(0);
            $table->json('summary')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['bot_company_id', 'created_at'], 'sicoes_batches_company_created_idx');
            $table->index(['bot_company_id', 'requested_date'], 'sicoes_batches_company_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sicoes_scrape_batches');
    }
};
