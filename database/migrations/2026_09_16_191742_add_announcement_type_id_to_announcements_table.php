<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('announcement_type_id')
                ->nullable()
                ->after('id')
                ->constrained('announcement_types')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['announcement_type_id']);
            $table->dropColumn('announcement_type_id');
        });
    }
};
