<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Ujm
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;
        if ($userRole == 'ujm') {
            return $next($request);
        }
        if ($userRole == 'admin') {
            return redirect()->route('dashboard');
        }
        if ($userRole == 'mahasiswa') {
            return redirect()->route('dashboard');
        }
    }
}