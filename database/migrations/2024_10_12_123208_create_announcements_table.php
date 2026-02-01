<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('announce_title');
            $table->text('description');
            $table->dateTime('expiration_time');
            $table->decimal('salary', 10, 0);
            $table->boolean('pro')->default(false);
            $table->unsignedBigInteger('area_id');
            $table->foreignUuid('user_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
