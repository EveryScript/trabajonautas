<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delete column area_id
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('area_id');
        });
    }

    public function down(): void
    {
        // Restore column area_id
        Schema::table('announcements', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->after('pro');
        });
    }
};
