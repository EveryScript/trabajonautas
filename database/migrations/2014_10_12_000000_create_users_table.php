<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable(); // New field
            $table->char('gender')->nullable(); // New field
            $table->char('age')->nullable(); // New field
            $table->boolean('register_completed')->default(false); // New field
            $table->boolean('actived')->default(true); // New field
            $table->unsignedBigInteger('location_id')->nullable(); // Foreign
            $table->unsignedBigInteger('profesion_id')->nullable(); // Foreign
            $table->unsignedBigInteger('grade_profile_id')->nullable(); // Foreign
            $table->string('provider')->nullable(); // Google Auth
            $table->string('provider_id')->nullable(); // Google Auth
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
