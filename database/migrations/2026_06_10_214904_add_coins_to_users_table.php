<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('coins')->default(0)->after('grade_profile_id');
        });

        // Add 7 coins to all PRO-MAX clients (by role and account_type_id)
        $clientRoleName = config('app.client_role');
        $role = DB::table('roles')->where('name', $clientRoleName)->first();
        if ($role) {
            DB::table('users')
                ->join('accounts', 'users.id', '=', 'accounts.user_id')
                ->join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', '=', 'App\Models\User'); // O tu namespace de User
                })
                ->where('model_has_roles.role_id', $role->id)
                ->where('accounts.account_type_id', 3)
                ->update(['users.coins' => 7]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('coins');
        });
    }
};
