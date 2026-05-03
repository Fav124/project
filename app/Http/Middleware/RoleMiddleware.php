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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // super_admin bypasses role checks
        if ($request->user()->role === \App\Enums\Role::SUPER_ADMIN) {
            return $next($request);
        }

        if (! in_array($request->user()->role->value, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. You do not have the required role.'], 403);
            }
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
