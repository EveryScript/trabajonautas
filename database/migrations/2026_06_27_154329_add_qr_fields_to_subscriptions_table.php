<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('qr_id')->nullable()->unique()->after('price'); // QR-ID from Baneco
            $table->longText('qr_image')->nullable()->after('qr_id'); // QR-Image to show 
            $table->timestamp('qr_expires_at')->nullable()->after('qr_image'); // QR-Expiration
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['qr_id', 'qr_image', 'qr_expires_at']);
        });
    }
};
