<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $user = Auth::user();
        $allowedRoles = [];
        foreach ($roles as $roleString) {
            $individualRoles = explode('|', $roleString);
            $allowedRoles = array_merge($allowedRoles, $individualRoles);
        }
        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        // Periksa apakah pengguna memiliki peran yang diizinkan atau merupakan alumni
        if ($user->hasAnyRole($allowedRoles) || ($allowedRoles[0] === 'alumni' && $user->isAlumni())) {
            return $next($request);
        }
        Log::warning('Middleware CheckRole: Access denied', ['user_id' => $user->id, 'attempted_roles' => $allowedRoles]);
        abort(403, 'Anda tidak memiliki akses ke halaman.');
    }

}
