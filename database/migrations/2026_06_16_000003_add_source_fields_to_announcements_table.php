<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->text('source_url')->nullable()->after('company_id');
            $table->string('source_hash', 64)->nullable()->unique()->after('source_url');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique(['source_hash']);
            $table->dropColumn(['source_url', 'source_hash']);
        });
    }
};
