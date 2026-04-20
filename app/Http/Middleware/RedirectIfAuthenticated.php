<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Redirect user yang sudah login ke dashboard yang sesuai dengan role-nya.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->isSuperAdmin()) {
                    return redirect()->route('super-admin.dashboard');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
