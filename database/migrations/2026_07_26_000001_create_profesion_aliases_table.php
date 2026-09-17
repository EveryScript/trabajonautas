<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesion_aliases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profesion_id')
                ->constrained('profesions')
                ->cascadeOnDelete();
            $table->string('alias');
            $table->string('alias_normalizado')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesion_aliases');
    }
};
