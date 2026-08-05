<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') atau middleware('role:employee')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek role sesuai
        if (auth()->user()->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}