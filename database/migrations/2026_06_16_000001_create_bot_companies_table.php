<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bot_companies')) {
            throw new \RuntimeException(
                'No se puede crear bot_companies: la tabla ya existe y debe revisarse manualmente.'
            );
        }

        Schema::create('bot_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('evaluar_url');
            $table->string('logo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bot_companies')) {
            throw new \RuntimeException(
                'No se puede revertir bot_companies: la tabla esperada no existe.'
            );
        }

        Schema::drop('bot_companies');
    }
};
