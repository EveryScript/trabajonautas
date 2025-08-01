<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_types', function (Blueprint $table) {
            $table->id();
            $table->string('company_type_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_types');
    }
};
