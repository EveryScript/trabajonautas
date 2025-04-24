<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('announce_title');
            $table->text('description');
            $table->dateTime('expiration_time');
            $table->bigInteger('salary');
            $table->boolean('pro')->default(false);
            $table->string('announce_file')->nullable();
            $table->unsignedBigInteger('area_id');
            $table->foreignUuid('user_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
