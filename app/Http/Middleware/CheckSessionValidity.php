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
        // Allow all livewire requests (like upload files)    
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        if (Auth::check()) {
            $sessionId = $request->session()->getId();
            $userId = Auth::id();

            $exists = DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('session_expired', 'Sesión cerrada: se detectó un inicio en otro dispositivo.');
            }
        }

        return $next($request);
    }
}
