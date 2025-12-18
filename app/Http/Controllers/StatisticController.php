<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $category = $request->get('category', null);

        $baseQuery = Announcement::query()->whereYear('created_at', $year);

        if ($category) {
            $baseQuery->where('category', $category);
        }

        // Per bulan
        $byMonth = (clone $baseQuery)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Per target_role
        $byTargetRole = (clone $baseQuery)
            ->select('target_role', DB::raw('count(*) as total'))
            ->groupBy('target_role')
            ->pluck('total', 'target_role');

        // Status aktif/nonaktif
        $byStatus = (clone $baseQuery)
            ->select('is_active', DB::raw('count(*) as total'))
            ->groupBy('is_active')
            ->pluck('total', 'is_active');

        // Per role pembuat
        $byUserRole = (clone $baseQuery)->with('createdBy')->get()
            ->groupBy(fn($a) => $a->createdBy?->activeRole())
            ->map(fn($group) => $group->count());

        // Per fakultas
        $byFaculty = null;
        if (in_array(auth()->user()->activeRole(), ['admin', 'dekan'])) {
            $facultyQuery = Announcement::join('faculties', 'announcements.faculty_id', '=', 'faculties.id')
                ->whereYear('announcements.created_at', $year);

            if ($category) {
                $facultyQuery->where('announcements.category', $category);
            }

            $byFaculty = $facultyQuery
                ->select('faculties.nama', DB::raw('count(*) as total'))
                ->groupBy('faculties.nama')
                ->pluck('total', 'faculties.nama');
        }

        // Ambil semua kategori unik untuk filter
        $allCategories = Announcement::select('category')->distinct()->pluck('category');

        return view('statistik.index', compact(
            'year',
            'category',
            'byMonth',
            'byTargetRole',
            'byStatus',
            'byUserRole',
            'byFaculty',
            'allCategories'
        ));
    }


}