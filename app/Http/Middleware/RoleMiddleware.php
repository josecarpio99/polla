<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (auth()->user()->role === 'superadmin') {
            return $next($request);
        }

        if (($role === 'admin' || $role === 'pos') && auth()->user()->role === 'admin') {
            return $next($request);
        }

        if ($role === 'pos' && auth()->user()->role === 'pos') {
            return $next($request);
        }

        return response([
            'success' => False,
            'message' => 'Unauthorized'
        ], 401);
    }
}
