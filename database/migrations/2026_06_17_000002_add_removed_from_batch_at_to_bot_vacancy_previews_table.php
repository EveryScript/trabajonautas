<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            if (!Schema::hasColumn('bot_vacancy_previews', 'removed_from_batch_at')) {
                $table->timestamp('removed_from_batch_at')->nullable()->after('scrape_batch_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bot_vacancy_previews', function (Blueprint $table) {
            if (Schema::hasColumn('bot_vacancy_previews', 'removed_from_batch_at')) {
                $table->dropColumn('removed_from_batch_at');
            }
        });
    }
};
