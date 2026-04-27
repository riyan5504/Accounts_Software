<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $user = auth()->user();

        if (!$user->role) {
            abort(403, 'Role not assigned');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $next($request);
    }
}
