<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Kaprodi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;
        if ($userRole == 'kaprodi') {
            return $next($request);
        }

        // Redirect ke dashboard sesuai role
        return match ($userRole) {
            'admin' => redirect()->route('dashboard'),
            'dosen' => redirect()->route('dashboard'),
            'mahasiswa' => redirect()->route('dashboard'),
            'ujm' => redirect()->route('dashboard'),
            'dekan' => redirect()->route('dashboard'),
            default => redirect()->route('login')
        };
    }
}