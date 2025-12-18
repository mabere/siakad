<?php

namespace App\Http\Controllers\Backend;

use App\Models\Tuitionfee;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTuitionfeeRequest;
use App\Http\Requests\UpdateTuitionfeeRequest;

class TuitionfeeController extends Controller
{
    public function uploadPayment(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'payment_proof' => 'required|file|mimes:jpg,png,pdf|max:512', // Validasi file
        ]);

        $mahasiswa = $request->session()->get('mahasiswa');
        $ta = getCurrentAcademicYear();

        $tuitionfee = Tuitionfee::where('student_id', $mahasiswa->id)
            ->firstOrFail();

        // Simpan bukti pembayaran
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('payment_proofs', 'public'); // Simpan file dan ambil pathnya

            $tuitionfee->payment_proof = $path;
            $tuitionfee->save();
        }

        return redirect()->route('student.krs.index')->with('status', 'Bukti pembayaran berhasil diupload, menunggu verifikasi admin.');
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTuitionfeeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tuitionfee $tuitionfee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tuitionfee $tuitionfee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTuitionfeeRequest $request, Tuitionfee $tuitionfee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tuitionfee $tuitionfee)
    {
        //
    }
}
