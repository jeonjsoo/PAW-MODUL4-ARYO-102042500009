<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memeriksa apakah pengguna memiliki role yang sesuai.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            return redirect()->route('home')
                ->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
        }

        return $next($request);
    }
}
