<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionValidity
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::check()) {
                $sessionId = $request->session()->getId();

                $exists = DB::table('sessions')->where('id', $sessionId)->exists();

                if (!$exists) {
                    Auth::logout();
                    return redirect()->route('login');
                }
            }
        } catch (\Exception $e) {
            return $next($request);
        }

        return $next($request);
    }
}
