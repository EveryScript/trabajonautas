<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bot_sources')) {
            throw new \RuntimeException(
                'No se puede crear bot_sources: la tabla ya existe y debe revisarse manualmente.'
            );
        }

        Schema::create('bot_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('scraper_type');
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bot_sources')) {
            throw new \RuntimeException(
                'No se puede revertir bot_sources: la tabla esperada no existe.'
            );
        }

        Schema::drop('bot_sources');
    }
};
