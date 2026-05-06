<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'companies',
            'areas',
            'profesions',
            'announcements',
            'announcement_user',
            'accounts',
            'notification_logs',
            'notices',
            'subscriptions'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Apply onCascade
                $table->foreignUuid('user_id')
                    ->nullable()
                    ->change()
                    ->constrained('users')
                    ->cascadeOnDelete();

                if ($tableName == 'subscriptions') {
                    $table->foreignUuid('verified_by_user_id')
                        ->nullable()
                        ->change()
                        ->constrained('users')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'companies',
            'areas',
            'announcements',
            'announcement_user',
            'accounts',
            'notification_logs',
            'notices',
            'subscriptions'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->dropForeign(['user_id']);

                    if ($tableName == 'subscriptions') {
                        $table->dropForeign(['verified_by_user_id']);
                    }
                } catch (\Exception $e) {
                    // OK continue
                }
            });
        }
    }
};
