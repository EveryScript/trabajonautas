<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionValidity
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookie_name = config('session.cookie');
        $session_cookie = $request->cookies->get($cookie_name);

        if (Auth::check()) {
            $session_id = $request->session()->getId();
            $exists = DB::table('sessions')
                ->where('id', $session_id)
                ->where('user_id', Auth::id())
                ->exists();

            if (!$exists) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('session_expired', 'Tu sesión se ha cerrado ya que iniciaste sesión desde otro dispositivo.');
            }

            return $next($request);
        }

        if ($session_cookie) {
            $exists = DB::table('sessions')
                ->where('id', $session_cookie)
                ->exists();

            if (! $exists) {
                return redirect()->route('login')
                    ->with('session_expired', 'Tu sesión ha sido cerrada ya que iniciaste sesión desde otro dispositivo.');
            }
        }

        return $next($request);
    }
}
