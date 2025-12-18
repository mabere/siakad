<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Addition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PenunjangValidationController extends Controller
{
    public function index()
    {
        $penunjangs = Addition::with(['lecturer'])
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when(request('search'), function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.penunjang.validation.index', compact('penunjangs'));
    }

    public function show(Addition $penunjang)
    {
        return view('admin.penunjang.validation.show', compact('penunjang'));
    }

    public function process(Request $request, Addition $penunjang)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected'
        ]);

        $penunjang->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'validated_at' => now(),
            'validated_by' => Auth::id()
        ]);

        return redirect()
            ->route('admin.penunjang.validation.list')
            ->with('success', 'Kegiatan penunjang berhasil divalidasi');
    }

    public function dashboard()
    {
        $query = Addition::with(['lecturer.department'])
            ->when(request('department_id'), function ($query, $deptId) {
                return $query->whereHas('lecturer', function ($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                });
            })
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when(request('start_date') && request('end_date'), function ($query) {
                return $query->whereBetween('date', [request('start_date'), request('end_date')]);
            });

        // Statistics
        $statistics = [
            'total' => Addition::count(),
            'approved' => Addition::where('status', 'approved')->count(),
            'pending' => Addition::where('status', 'pending')->count(),
            'rejected' => Addition::where('status', 'rejected')->count(),
        ];

        // Group by department
        $departmentStats = Addition::select('departments.nama as department_name', 'penunjangs.status')
            ->selectRaw('COUNT(*) as total')
            ->join('lecturers', 'penunjangs.lecturer_id', '=', 'lecturers.id')
            ->join('departments', 'lecturers.department_id', '=', 'departments.id')
            ->groupBy('departments.nama', 'penunjangs.status')
            ->get()
            ->groupBy('department_name')
            ->map(function ($items) {
                return [
                    'total' => $items->sum('total'),
                    'approved' => $items->where('status', 'approved')->sum('total'),
                    'pending' => $items->where('status', 'pending')->sum('total'),
                    'rejected' => $items->where('status', 'rejected')->sum('total'),
                ];
            });

        // Get departments for filter
        $departments = \App\Models\Department::all();

        // Get activities with explicit status
        $penunjangs = $query->select('penunjangs.*')
            ->selectRaw("CASE 
                WHEN status = 'approved' THEN 'approved'
                WHEN status = 'rejected' THEN 'rejected'
                ELSE 'pending'
            END as status_display")
            ->latest()
            ->take(3)
            ->get();

        return view('admin.penunjang.dashboard', compact(
            'penunjangs',
            'statistics',
            'departmentStats',
            'departments'
        ));
    }

    public function exportDashboardExcel(Request $request)
    {
        $query = Addition::with(['lecturer.department'])
            ->when($request->department_id, function ($query, $deptId) {
                return $query->whereHas('lecturer', function ($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                return $query->whereBetween('date', [$request->start_date, $request->end_date]);
            });

        $penunjangs = $query->latest()->get();

        return Excel::download(new \App\Exports\PenunjangExport($penunjangs), 'penunjang_activities.xlsx');
    }
}