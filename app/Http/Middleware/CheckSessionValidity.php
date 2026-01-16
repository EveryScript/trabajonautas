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
        // 1. Omitir para Livewire y Debugbar (si existe)
        if ($request->is('livewire/*') || $request->has('_debugbar')) {
            return $next($request);
        }

        try {
            if (Auth::check()) {
                $sessionId = $request->session()->getId();

                $exists = DB::table('sessions')
                    ->where('id', $sessionId)
                    ->where('user_id', Auth::id())
                    ->exists();

                if (!$exists) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('session_expired', 'Sesión cerrada por seguridad.');
                }
            }
        } catch (\Exception $e) {
            // Si hay un error de base de datos o sesión, deja pasar la petición
            // para que no se bloquee el sitio.
            return $next($request);
        }

        return $next($request);
    }
}
