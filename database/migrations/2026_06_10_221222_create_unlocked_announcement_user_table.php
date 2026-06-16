<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlocked_announcement_user', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade'); // Foreign key
            $table->unsignedBigInteger('announcement_id')->constrained()->onDelete('cascade'); // Foreign key
            $table->timestamps();
            $table->unique(['user_id', 'announcement_id'], 'user_announcement_unique'); // Unique
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlocked_announcement_user');
    }
};
