<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id'); // Foreign key
            $table->unsignedBigInteger('account_type_id'); // Foreign key
            $table->decimal('price', 4, 0);
            $table->boolean('verified_payment')->default(false);
            $table->foreignUuid('verified_by_user_id')->nullable(); // Foreign key
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
