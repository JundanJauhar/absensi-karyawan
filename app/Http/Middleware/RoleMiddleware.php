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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        // Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Jika karyawan mencoba mengakses halaman admin, arahkan ke portal karyawan
            if ($user->isKaryawan()) {
                return redirect()->route('portal.index')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk membuka halaman tersebut.');
            }

            // Jika admin mencoba mengakses halaman khusus karyawan, arahkan ke admin dashboard
            if ($user->isAdmin()) {
                return redirect()->route('karyawan.index')->with('error', 'Halaman ini khusus untuk portal karyawan.');
            }

            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk membuka halaman ini.');
        }

        return $next($request);
    }
}