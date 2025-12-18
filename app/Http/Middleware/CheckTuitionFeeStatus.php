<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTuitionFeeStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $student = user()->role->student; // Assuming the authenticated user is a student

        if ($student && $student->tuitionFee && $student->tuitionFee->status === 'LUNAS') {
            return $next($request);
        }

        return redirect()->route('mhs.dashboard')->with('error', 'Anda tidak dapat mengakses fitur ini karena status pembayaran belum lunas.');
    }
}