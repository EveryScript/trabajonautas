<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            if (!Schema::hasColumn('bot_vacancy_previews', 'scrape_batch_id')) {
                $table->string('scrape_batch_id')->nullable()->index()->after('status');
            }

            if (!Schema::hasColumn('bot_vacancy_previews', 'selected_company_id')) {
                $table->foreignId('selected_company_id')->nullable()->after('convocatoria_id')->constrained('companies')->nullOnDelete();
            }

            if (!Schema::hasColumn('bot_vacancy_previews', 'selected_area_id')) {
                $table->foreignId('selected_area_id')->nullable()->after('selected_company_id')->constrained('areas')->nullOnDelete();
            }

            if (!Schema::hasColumn('bot_vacancy_previews', 'selected_profession_ids')) {
                $table->json('selected_profession_ids')->nullable()->after('selected_area_id');
            }

            if (!Schema::hasColumn('bot_vacancy_previews', 'selected_location_ids')) {
                $table->json('selected_location_ids')->nullable()->after('selected_profession_ids');
            }

            if (!Schema::hasColumn('bot_vacancy_previews', 'is_pro')) {
                $table->boolean('is_pro')->default(false)->after('selected_location_ids');
            }

            if (!Schema::hasColumn('bot_vacancy_previews', 'attachments')) {
                $table->json('attachments')->nullable()->after('is_pro');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            if (Schema::hasColumn('bot_vacancy_previews', 'attachments')) {
                $table->dropColumn('attachments');
            }

            if (Schema::hasColumn('bot_vacancy_previews', 'is_pro')) {
                $table->dropColumn('is_pro');
            }

            if (Schema::hasColumn('bot_vacancy_previews', 'selected_location_ids')) {
                $table->dropColumn('selected_location_ids');
            }

            if (Schema::hasColumn('bot_vacancy_previews', 'selected_profession_ids')) {
                $table->dropColumn('selected_profession_ids');
            }

            if (Schema::hasColumn('bot_vacancy_previews', 'selected_area_id')) {
                $table->dropConstrainedForeignId('selected_area_id');
            }

            if (Schema::hasColumn('bot_vacancy_previews', 'selected_company_id')) {
                $table->dropConstrainedForeignId('selected_company_id');
            }

            if (Schema::hasColumn('bot_vacancy_previews', 'scrape_batch_id')) {
                $table->dropIndex(['scrape_batch_id']);
                $table->dropColumn('scrape_batch_id');
            }
        });
    }
};
