<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckOnlyOneSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $sessionId = $request->session()->getId();
            $userId = auth()->id();

            $session = DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', $userId)
                ->exists();

            if (!$session) {
                Auth::guard('web')->logout();
                $request->session()->forget('password_hash_web');
                return redirect('/login')->with('error', 'Se ha iniciado sesión en otro dispositivo');
            }
        }

        return $next($request);
    }
}
