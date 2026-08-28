<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcement_profesion', function (Blueprint $table) {
            $table->index('announcement_id');
            $table->index('profesion_id');
        });

        Schema::table('announcement_location', function (Blueprint $table) {
            $table->index('announcement_id');
            $table->index('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('announcement_profesion', function (Blueprint $table) {
            $table->dropIndex(['announcement_id']);
            $table->dropIndex(['profesion_id']);
        });

        Schema::table('announcement_location', function (Blueprint $table) {
            $table->dropIndex(['announcement_id']);
            $table->dropIndex(['location_id']);
        });
    }
};
