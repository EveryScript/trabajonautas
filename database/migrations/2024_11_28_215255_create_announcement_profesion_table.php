<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_profesion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id'); // Foreign key
            $table->unsignedBigInteger('profesion_id'); // Foreign key
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_profesion');
    }
};
