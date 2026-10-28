<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-Based Access Control (RBAC).
 * Pemakaian di route: ->middleware('role:admin') atau 'role:admin,staff'.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Belum login -> arahkan ke halaman login.
        if (! $user) {
            return redirect()->route('login');
        }

        // Sudah login tetapi role tidak diizinkan -> 403 Forbidden.
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
