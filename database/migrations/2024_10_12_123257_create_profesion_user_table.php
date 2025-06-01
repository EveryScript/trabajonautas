<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesion_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profesion_id');
            $table->foreignUuid('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesion_user');
    }
};
