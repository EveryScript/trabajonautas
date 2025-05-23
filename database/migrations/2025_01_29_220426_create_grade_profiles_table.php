<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_profiles', function (Blueprint $table) {
            $table->id();
            $table->char('profile_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_profiles');
    }
};
