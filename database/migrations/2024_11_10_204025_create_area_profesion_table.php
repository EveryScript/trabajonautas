<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_profesion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id'); // Foreign key
            $table->unsignedBigInteger('profesion_id'); // Foreign key
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_profesion');
    }
};
