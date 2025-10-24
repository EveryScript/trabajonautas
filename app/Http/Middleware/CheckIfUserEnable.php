<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserEnable
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->actived) {
                if ($request->is('disabled')) {
                    return redirect()->route('dashboard');
                }
            } else {
                if (!$request->is('disabled')) {
                    return redirect()->route('disabled');
                }
            }
        }
        return $next($request);
    }
}
