<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isAdmin === 1) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized (Admin only)'], 403);
    }
}
