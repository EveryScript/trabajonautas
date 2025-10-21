<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class LogoutOtherDevicesOnLogin
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event): void
    {
        $user = $event->user;

        // Elimina todas las sesiones activas del usuario excepto la actual
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();
    }
}
