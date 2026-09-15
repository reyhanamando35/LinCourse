<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoReadOnlyMiddleware
{
    // Aksi tulis yang tetap boleh dicoba akun demo: tidak menghapus data, tidak mengubah akun/konten orang lain
    private const ALLOWED_ROUTES = ['logout', 'admin.payments.verify'];

    /**
     * Password akun demo admin ditampilkan publik, jadi akun ini hanya boleh melihat (GET).
     * Tanpa ini pengunjung bisa menghapus user, subject, modul, atau soal dan merusak portofolio.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isDemoAdmin()
            && !$request->isMethodSafe()
            && !$request->routeIs(...self::ALLOWED_ROUTES)) {
            return redirect()->back()->with('error', 'This action is disabled for the demo admin account.');
        }

        return $next($request);
    }
}
