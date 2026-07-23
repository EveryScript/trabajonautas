<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bot_companies') || Schema::hasColumn('bot_companies', 'bot_source_id')) {
            return;
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
        if (!Schema::hasTable('bot_companies') || !Schema::hasColumn('bot_companies', 'bot_source_id')) {
            return;
        }

        Schema::table('bot_companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bot_source_id');
        });
    }
};
