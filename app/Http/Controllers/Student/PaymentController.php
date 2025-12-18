<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tuitionfee;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Tuitionfee::where('student_id', Auth::user()->mahasiswa->id)
            ->whereHas('academicYear', fn($q) => $q->where('status', true))
            ->get();
        return view('student.payments.index', compact('payments'));
    }

    public function history()
    {
        $payments = Tuitionfee::where('student_id', Auth::user()->mahasiswa->id)
            ->latest()
            ->get();
        return view('student.payments.history', compact('payments'));
    }

    public function uploadProof(Request $request)
    {
        $request->validate(['proof' => 'required|file|mimes:pdf,jpg,png|max:2048']);
        $path = $request->file('proof')->store('payment_proofs');
        Tuitionfee::where('student_id', Auth::user()->mahasiswa->id)
            ->where('status', 'BELUM LUNAS')
            ->update([
                'payment_proof' => $path,
                'verification_status' => 'PENDING',
                'payment_date' => now(),
            ]);
        return redirect()->route('student.payments.index')->with('success', 'Bukti pembayaran diupload.');
    }
}