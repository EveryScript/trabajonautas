<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        $professions = DB::table('profesions')->whereNotNull('area_id')->get();

        foreach ($professions as $profession) {
            DB::table('area_profesion')->insert([
                'profesion_id' => $profession->id,
                'area_id' => $profession->area_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('area_profesion');
    }
};
