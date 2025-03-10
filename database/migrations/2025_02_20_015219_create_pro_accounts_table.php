<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_accounts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('purchased_at');
            $table->smallInteger('duration_days');
            $table->boolean('verified_payment')->default(false);
            $table->text('device_token')->nullable();
            $table->foreignUuid('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_accounts');
    }
};
