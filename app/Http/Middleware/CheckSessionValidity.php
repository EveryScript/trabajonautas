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
        // 1. Ignorar Livewire SIEMPRE
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        // 2. Usar un try-catch para evitar que errores de servidor detengan el sitio
        try {
            if (Auth::check()) {
                $sessionId = $request->session()->getId();

                // Si la tabla sessions no es accesible, esto lanzará una excepción
                $exists = DB::table('sessions')->where('id', $sessionId)->exists();

                if (!$exists) {
                    Auth::logout();
                    return redirect()->route('login');
                }
            }
        } catch (\Exception $e) {
            // Si hay error de permisos/DB, no interrumpas al usuario
            return $next($request);
        }

        return $next($request);
    }
}
