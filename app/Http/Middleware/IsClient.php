<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsClient
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isAdmin === 0) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized (Client only)'], 403);
    }
}
