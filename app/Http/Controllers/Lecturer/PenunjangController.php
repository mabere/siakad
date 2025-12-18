<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\Addition;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use Carbon\Carbon;

class PenunjangController extends Controller
{
    public function index(Request $request)
    {
        $query = Addition::query()
            ->where('lecturer_id', auth()->user()->lecturer->id);

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $penunjangs = $query->latest()->paginate(10);

        return view('dosen.tridharma.penunjang.index', [
            'penunjangs' => $penunjangs,
            'filters' => $request->all()
        ]);
    }

    public function create()
    {
        return view('dosen.tridharma.penunjang.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'level' => 'required|in:Nasional,Internasional,Regional',
            'peran' => 'required|string|max:255',
            'date' => 'required|date',
            'proof' => 'required_without:proof_url|file|mimes:pdf,doc,docx|max:2048',
            'proof_url' => 'required_without:proof|url|nullable'
        ]);

        // Set lecturer_id from authenticated user
        $validatedData['lecturer_id'] = auth()->user()->lecturer->id;

        // Handle file upload or URL
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/penunjang'), $filename);
            $validatedData['proof'] = $filename;
        } else if ($request->proof_url) {
            $validatedData['proof'] = $request->proof_url;
        }

        Addition::create($validatedData);

        return redirect()
            ->route('lecturer.penunjang.index')
            ->with('success', 'Data kegiatan Penunjang berhasil ditambahkan!');
    }

    public function show($id)
    {
        $penunjang = Addition::where('id', $id)
            ->where('lecturer_id', auth()->user()->lecturer->id)
            ->firstOrFail();

        return view('dosen.tridharma.penunjang.show', compact('penunjang'));
    }

    public function edit($id)
    {
        $penunjang = Addition::where('id', $id)
            ->where('lecturer_id', auth()->user()->lecturer->id)
            ->firstOrFail();
        return view('dosen.tridharma.penunjang.edit', compact('penunjang'));
    }

    public function update(Request $request, $id)
    {
        $penunjang = Addition::where('id', $id)
            ->where('lecturer_id', auth()->user()->lecturer->id)
            ->firstOrFail();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'level' => 'required|in:Nasional,Internasional,Regional',
            'peran' => 'required|string|max:255',
            'date' => 'required|date',
            'proof' => 'required_without:proof_url|file|mimes:pdf,doc,docx|max:2048:nullable',
            'proof_url' => 'required_without:proof|url|nullable'
        ]);

        // Handle file upload or URL
        if ($request->hasFile('proof')) {
            // Delete old file if it exists and is not a URL
            if ($penunjang->proof && !filter_var($penunjang->proof, FILTER_VALIDATE_URL)) {
                $oldFilePath = public_path('uploads/penunjang/' . $penunjang->proof);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file = $request->file('proof');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/penunjang'), $filename);
            $validatedData['proof'] = $filename;
        } else if ($request->proof_url) {
            // If it's a URL, store the URL directly
            $validatedData['proof'] = $request->proof_url;
        } else {
            // If neither file nor URL is provided, keep the existing proof
            $validatedData['proof'] = $penunjang->proof;
        }

        $penunjang->update($validatedData);

        return redirect()
            ->route('lecturer.penunjang.index')
            ->with('success', 'Data kegiatan Penunjang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penunjang = Addition::where('id', $id)
            ->where('lecturer_id', auth()->user()->lecturer->id)
            ->firstOrFail();

        // Delete proof file if exists
        if ($penunjang->proof && file_exists(public_path('uploads/penunjang/' . $penunjang->proof))) {
            unlink(public_path('uploads/penunjang/' . $penunjang->proof));
        }

        $penunjang->delete();

        return redirect()
            ->route('lecturer.penunjang.index')
            ->with('success', 'Data kegiatan Penunjang berhasil dihapus!');
    }

    public function exportPDF(Request $request)
    {
        $query = Addition::query()
            ->where('lecturer_id', auth()->user()->lecturer->id);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $penunjangs = $query->latest()->get();
        $lecturer = auth()->user()->lecturer;

        $pdf = PDF::loadView('dosen.tridharma.penunjang.export-pdf', [
            'penunjangs' => $penunjangs,
            'lecturer' => $lecturer,
            'filters' => $request->all()
        ]);

        return $pdf->stream('Kegiatan_Penunjang_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Addition::query()
            ->where('lecturer_id', auth()->user()->lecturer->id);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $penunjangs = $query->latest()->get();

        return Excel::download(new PenunjangExport($penunjangs), 'Kegiatan_Penunjang_' . date('Y-m-d') . '.xlsx');
    }

    public function dashboard()
    {
        $lecturer = auth()->user()->lecturer;

        // Statistik umum
        $totalKegiatan = Addition::where('lecturer_id', $lecturer->id)->count();
        $kegiatanTahunIni = Addition::where('lecturer_id', $lecturer->id)
            ->whereYear('date', date('Y'))
            ->count();

        // Status validasi
        $totalPending = Addition::where('lecturer_id', $lecturer->id)
            ->where('status', 'pending')
            ->count();
        $totalApproved = Addition::where('lecturer_id', $lecturer->id)
            ->where('status', 'approved')
            ->count();
        $totalRejected = Addition::where('lecturer_id', $lecturer->id)
            ->where('status', 'rejected')
            ->count();

        // Distribusi level
        $levelNasional = Addition::where('lecturer_id', $lecturer->id)
            ->where('level', 'Nasional')
            ->count();
        $levelInternasional = Addition::where('lecturer_id', $lecturer->id)
            ->where('level', 'Internasional')
            ->count();
        $levelRegional = Addition::where('lecturer_id', $lecturer->id)
            ->where('level', 'Regional')
            ->count();

        // Data trend - hanya bulan yang ada kegiatan
        $activities = Addition::where('lecturer_id', $lecturer->id)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(10)
            ->get();

        $trendData = [];
        $labels = [];

        foreach ($activities as $activity) {
            $date = Carbon::createFromDate($activity->year, $activity->month, 1);
            $labels[] = $date->format('M Y');
            $trendData[] = $activity->total;
        }

        // Kegiatan terbaru
        $kegiatanTerbaru = Addition::where('lecturer_id', $lecturer->id)
            ->latest('date')
            ->take(3)
            ->get();

        // Data untuk chart
        $chartData = [
            'labels' => $labels,
            'data' => $trendData
        ];

        return view('dosen.tridharma.penunjang.dashboard', compact(
            'totalKegiatan',
            'kegiatanTahunIni',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'levelNasional',
            'levelInternasional',
            'levelRegional',
            'kegiatanTerbaru',
            'chartData'
        ));
    }

    public function printPDF(Request $request)
    {
        $lecturer = auth()->user()->lecturer;

        // Query untuk kegiatan yang disetujui
        $penunjangs = Addition::where('lecturer_id', $lecturer->id)
            ->where('status', 'approved')
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request) {
                return $query->whereBetween('date', [$request->start_date, $request->end_date]);
            })
            ->when($request->filled('level'), function ($query) use ($request) {
                return $query->where('level', $request->level);
            })
            ->orderBy('date', 'desc')
            ->get();

        $pdf = PDF::loadView('dosen.tridharma.penunjang.print', [
            'penunjangs' => $penunjangs,
            'lecturer' => $lecturer,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'level' => $request->level,
            'print_date' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->stream('Kegiatan_Penunjang_Tervalidasi_' . date('Y-m-d') . '.pdf');
    }
}