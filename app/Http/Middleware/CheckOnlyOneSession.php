<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckOnlyOneSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $sessionId = $request->session()->getId();
            $userId = auth()->id();

            DB::table('sessions')
                ->where('user_id', $userId)
                ->where('id', '!=', $sessionId)
                ->delete();
        }

        return $next($request);
    }
}
