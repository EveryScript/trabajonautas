<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->index('expiration_time');
            $table->index('updated_at');
            $table->index('created_at');
            $table->index('company_id');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex(['expiration_time']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['scheduled_at']);
        });
    }
};
