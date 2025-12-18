<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAlumniRequest;
use App\Http\Requests\UpdateAlumniRequest;

class AlumniController extends Controller
{
    public function index()
    {
        $alumni = Alumni::with('student')->paginate(10); // Paginate untuk efisiensi
        return view('backend.alumni.index', compact('alumni'));
    }

    /**
     * Show the form for creating a new alumni entry (manual by admin).
     */
    public function create()
    {
        $students = Student::whereDoesntHave('alumni')->get(); // Hanya mahasiswa yang belum jadi alumni
        return view('backend.alumni.create', compact('students'));
    }

    /**
     * Store a newly created alumni in storage.
     */
    public function store(StoreAlumniRequest $request)
    {
        $alumni = new Alumni($request->validated());
        $alumni->save();

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil ditambahkan.');
    }

    /**
     * Display the specified alumni.
     */
    public function show($id)
    {
        $alumni = Alumni::with('student')->findOrFail($id);
        return view('backend.alumni.show', compact('alumni'));
    }

    /**
     * Show the form for editing the specified alumni.
     */
    public function edit($id)
    {
        $alumni = Alumni::with('student')->findOrFail($id);
        return view('backend.alumni.edit', compact('alumni'));
    }

    /**
     * Update the specified alumni in storage.
     */
    public function update(UpdateAlumniRequest $request, $id)
    {
        $alumni = Alumni::findOrFail($id);
        $alumni->update($request->validated());

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil diperbarui.');
    }

    /**
     * Remove the specified alumni from storage.
     */
    public function destroy($id)
    {
        $alumni = Alumni::findOrFail($id);
        $alumni->delete();

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil dihapus.');
    }


    // Metode untu User role = Mahasiswa yang sudah menjadi Alumni
    /**
     * Allow alumni to to access their dashboard page.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $user->load([
            'alumni',
            'student.department',
            'student.department.faculty'
        ]);
        $alumni = $user->alumni;
        if (!$alumni) {
            Log::error('Alumni data not found for user: ' . $user->email);
            abort(403, 'Data alumni tidak ditemukan. Silakan hubungi admin untuk memverifikasi data Anda.');
        }
        return view('backend.alumni.dashboard', compact('alumni', 'user'));
    }

    /**
     * Allow alumni to update their own profile.
     */
    public function alumniProfile()
    {
        $user = Auth::user();
        $user->load('student');
        $alumni = optional($user->student)->alumni;
        if (!$alumni) {
            abort(404, 'Data profil alumni Anda belum tersedia. Silakan hubungi administrator.');
        }
        return view('backend.alumni.profile', compact('alumni'));
    }

    /**
     * Update alumni profile (for alumni self-service).
     */
    public function updateAlumniProfile(UpdateAlumniRequest $request)
    {
        $user = Auth::user();
        $alumni = Alumni::where('student_id', $user->student->id)->firstOrFail();
        $validatedData = $request->validated();
        $validatedData['last_updated'] = now();
        $alumni->update($validatedData);
        return redirect()->route('alumni.dashboard')->with('success', 'Data Profil Alumni berhasil diperbaharui');
    }

    /**
     * Generate alumni statistics report.
     */
    public function reports()
    {
        $totalAlumni = Alumni::count();
        $employed = Alumni::whereNotNull('job_title')->count();
        $furtherEducation = Alumni::whereNotNull('further_education')->count();
        $contributing = Alumni::whereNotNull('contribution')->count();

        $industryDistribution = Alumni::select('industry', DB::raw('count(*) as count'))
            ->whereNotNull('industry')
            ->groupBy('industry')
            ->get();

        return view('backend.alumni.reports', compact(
            'totalAlumni',
            'employed',
            'furtherEducation',
            'contributing',
            'industryDistribution'
        ));
    }

    /**
     * Export alumni report to PDF/Excel.
     */
    public function export(Request $request)
    {
        $alumni = Alumni::with('student')->get();
        // Logika export ke PDF/Excel (gunakan package seperti Laravel Excel atau DomPDF)
        // Contoh: return Excel::download(new AlumniExport($alumni), 'alumni.xlsx');
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
