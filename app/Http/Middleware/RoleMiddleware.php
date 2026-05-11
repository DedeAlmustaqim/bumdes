<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            abort(403, 'Akses Ditolak. Anda belum login.');
        }

        // Pecah role berdasarkan pemisah |
        $allowedRoles = explode('|', $roles);

        // Cek apakah role user ada dalam daftar
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            abort(403, 'Akses Ditolak. Peran Anda tidak diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }
}
