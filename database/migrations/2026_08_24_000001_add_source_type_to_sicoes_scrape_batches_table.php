<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sicoes_scrape_batches', function (Blueprint $table) {
            $table->string('source_type', 40)
                ->default('consulting_services')
                ->after('requested_date');
            $table->index(
                ['bot_company_id', 'source_type', 'requested_date', 'status'],
                'sicoes_batches_source_date_status_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::table('sicoes_scrape_batches', function (Blueprint $table) {
            $table->dropIndex('sicoes_batches_source_date_status_idx');
            $table->dropColumn('source_type');
        });
    }
};
