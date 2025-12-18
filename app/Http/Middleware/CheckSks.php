<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSks
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Pastikan pengguna adalah mahasiswa
        if (!$user || !$user->hasRole('mahasiswa')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai mahasiswa');
        }

        // Ambil data mahasiswa
        $student = $user->student;
        if (!$student) {
            return redirect()->route('login')
                ->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Ambil total SKS dari kolom total_sks
        $totalSks = $student->total_sks;

        // Batasi akses jika SKS kurang dari 120
        if ($totalSks < 120) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum memenuhi syarat untuk mengakses Bimbingan Skripsi. Minimal sudah lulus 120 SKS.');
        }

        return $next($request);
    }
}