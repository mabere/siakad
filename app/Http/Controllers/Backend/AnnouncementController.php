<?php

namespace App\Http\Controllers\Backend;

use App\Models\Kelas;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\AnnouncementLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Announcement::class, 'announcement');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (in_array($user->activeRole(), ['dosen', 'mahasiswa'])) {
            $announcements = $this->getFilteredAnnouncements($user);
        } else {
            $announcements = Announcement::with([
                'createdBy.roles',
                'createdBy.lecturer',
                'createdBy.employee.department',
            ])->latest()->paginate(6);
        }
        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        Auth::user()->unreadNotifications()
            ->where('data->announcement_id', $announcement->id)
            ->update(['read_at' => now()]);

        return view('announcements.show', compact('announcement'));
    }

    protected function getFilteredAnnouncements($user)
    {
        $query = Announcement::with('createdBy')
            ->where('is_active', 1)
            ->where(function ($q) use ($user) {
                $q->where('target_role', 'semua')
                    ->orWhere('target_role', $user->activeRole());
            });

        if ($user->activeRole() === 'dosen') {
            $query->where('faculty_id', $user->lecturer->faculty_id);
        }

        if ($user->activeRole() === 'mahasiswa') {
            $student = $user->student;

            if ($student) {
                $query->where(function ($q) use ($student) {
                    $q->whereNull('department_id')
                        ->orWhere('department_id', $student->department_id);
                });

                $query->where(function ($q) use ($student) {
                    $q->whereNull('kelas_id')
                        ->orWhere('kelas_id', $student->kelas_id);
                });
            }
        }

        return $query->latest()->paginate(6);
    }


    public function create(Request $request)
    {
        $user = $request->user();
        $role = $user->activeRole();

        if ($role === 'dekan') {
            $faculties = Faculty::where('id', $user->lecturer->faculty_id)->get();
            $departments = Department::where('faculty_id', $user->lecturer->faculty_id)->get();
            $kelas = Kelas::whereIn('department_id', $departments->pluck('id'))->get();
        } elseif ($role === 'kaprodi') {
            $dept = $user->lecturer->department;
            $faculties = Faculty::where('id', $dept->faculty_id)->get();
            $departments = collect([$dept]);
            $kelas = Kelas::where('department_id', $dept->id)->get();
        } elseif ($role === 'staff') {
            $dept = $user->employee->department;
            $faculties = Faculty::where('id', $dept->faculty_id)->get();
            $departments = collect([$dept]);
            $kelas = Kelas::where('department_id', $dept->id)->get();
        } else {
            $faculties = Faculty::all();
            $departments = Department::all();
            $kelas = Kelas::all();
        }


        return view('announcements.create', compact('faculties', 'departments', 'kelas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'target_role' => 'required|string',
            'faculty_id' => 'required|integer',
            'department_id' => 'nullable|integer',
            'kelas_id' => 'nullable|integer',
            'content' => 'required|string',
        ]);
        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $request->has('is_active');

        Announcement::create($data);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }



    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        $user = auth()->user();
        $role = $user->activeRole();

        if (in_array($role, ['dekan', 'kaprodi', 'staff'])) {
            $faculties = Faculty::where('id', $announcement->faculty_id)->get();

            if ($role === 'dekan') {
                // Dekan boleh akses semua prodi dan kelas di fakultas
                $departments = Department::where('faculty_id', $faculties->first()->id)->get();
                $kelas = Kelas::whereIn('department_id', $departments->pluck('id'))->get();
            } elseif ($role === 'kaprodi') {
                $departments = Department::where('id', $user->lecturer->department_id)->get();
                $kelas = Kelas::where('department_id', $user->lecturer->department_id)->get();
            } elseif ($role === 'staff') {
                $departments = Department::where('id', $user->employee->department_id)->get();
                $kelas = Kelas::where('department_id', $user->employee->department_id)->get();
            }
        } else {
            // Admin
            $faculties = Faculty::all();
            $departments = Department::all();
            $kelas = Kelas::all();
        }


        return view('announcements.edit', compact('announcement', 'faculties', 'departments', 'kelas'));
    }


    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'target_role' => 'required|string',
            'faculty_id' => 'required|integer',
            'department_id' => 'nullable|integer',
            'kelas_id' => 'nullable|integer',
            'content' => 'required|string',
        ]);
        $data['is_active'] = $request->has('is_active');

        $before = $announcement->only(array_keys($data)); // simpan sebelum
        $announcement->update($data);

        $after = $announcement->only(array_keys($data));

        AnnouncementLog::create([
            'announcement_id' => $announcement->id,
            'user_id' => auth()->id(),
            'action' => 'update',
            'changes' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggle(Announcement $announcement)
    {
        $this->authorize('toggle', $announcement);
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();
        return back()->with('success', 'Status pengumuman berhasil diperbarui.');
    }

}