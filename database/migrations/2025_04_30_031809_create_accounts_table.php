<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('limit_time')->nullable();
            $table->boolean('verified_payment')->default(false);
            $table->text('device_token')->nullable();
            $table->foreignUuid('user_id'); // Foreign key
            $table->unsignedBigInteger('account_type_id'); // Foreign key
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
