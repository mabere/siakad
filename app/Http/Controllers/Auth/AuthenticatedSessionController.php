<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    public function showLoginForm($role)
    {
        $validRoles = ['admin', 'dekan', 'kaprodi', 'dosen', 'mahasiswa', 'staff', 'alumni', 'ktu', 'ujm'];
        if (!in_array($role, $validRoles)) {
            abort(404);
        }
        return view('auth.login', compact('role'));
    }

    public function masuk(): View
    {
        return view('masuk');
    }

    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $credentials = $request->only('email', 'password');
    //     $selectedRole = $request->input('role');

    //     if (!Auth::attempt($credentials)) {
    //         return back()->withErrors([
    //             'email' => 'Email atau password tidak valid.',
    //         ])->onlyInput('email');
    //     }

    //     $user = Auth::user();

    //     if (!$user->hasRole(strtolower($selectedRole))) {
    //         // Periksa apakah pengguna bisa login sebagai alumni berdasarkan status lulus
    //         if ($selectedRole === 'alumni' && $user->isAlumni()) {
    //             // Izinkan login sebagai alumni
    //         } elseif ($selectedRole === 'mahasiswa' && $user->student && $user->student->alumni) {
    //             // Jika mahasiswa sudah lulus, arahkan ke login alumni
    //             $user->syncRoles(['alumni']);
    //             $user->save();
    //             $selectedRole = 'alumni';
    //         } else {
    //             Auth::logout();
    //             return back()
    //                 ->withErrors(['email' => 'Anda tidak memiliki akses untuk login sebagai ' . $selectedRole])
    //                 ->withInput($request->except('password'));
    //         }
    //     }

    //     // Verifikasi data berdasarkan role
    //     switch ($selectedRole) {
    //         case 'mahasiswa':
    //             if (!$user->relationLoaded('student')) {
    //                 $user->load('student');
    //             }
    //             if (!$user->student) {
    //                 Auth::logout();
    //                 return back()
    //                     ->withErrors(['email' => 'Data mahasiswa tidak ditemukan'])
    //                     ->withInput($request->except('password'));
    //             }
    //             if ($user->student->alumni && !$user->hasRole('alumni')) {
    //                 $user->syncRoles(['alumni']);
    //                 $user->save();
    //                 $selectedRole = 'alumni';
    //             }
    //             break;

    //         case 'dekan':
    //             if (!$user->relationLoaded('lecturer')) {
    //                 $user->load('lecturer');
    //             }
    //             if (!$user->lecturer) {
    //                 Auth::logout();
    //                 return back()
    //                     ->withErrors(['email' => 'Data Dekan tidak ditemukan.'])
    //                     ->withInput($request->except('password'));
    //             }
    //             break;
    //         case 'dosen':
    //         case 'ujm':
    //         case 'kaprodi':
    //             if (!$user->relationLoaded('lecturer')) {
    //                 $user->load('lecturer');
    //             }
    //             if (!$user->lecturer) {
    //                 Auth::logout();
    //                 return back()
    //                     ->withErrors(['email' => 'Data dosen tidak ditemukan'])
    //                     ->withInput($request->except('password'));
    //             }
    //             break;

    //         case 'staff':
    //             if (!$user->relationLoaded('employee')) {
    //                 $user->load('employee.department');
    //             }
    //             if (!$user->employee || !$user->employee->department) {
    //                 Auth::logout();
    //                 return back()
    //                     ->withErrors(['email' => 'Data Admin Prodi tidak ditemukan'])
    //                     ->withInput($request->except('password'));
    //             }
    //             break;

    //         case 'alumni':
    //             if (!$user->relationLoaded('alumni')) {
    //                 $user->load('alumni');
    //             }
    //             // Periksa dengan isAlumni() sebagai fallback
    //             if (!$user->alumni && !$user->isAlumni()) {
    //                 Auth::logout();
    //                 return back()
    //                     ->withErrors(['email' => 'Anda bukan alumni atau data alumni tidak ditemukan'])
    //                     ->withInput($request->except('password'));
    //             }
    //             break;
    //     }

    //     $request->session()->regenerate();

    //     // Redirect sesuai role
    //     return redirect()->route('dashboard');
    // }


    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $selectedRole = $request->input('role');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password tidak valid.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        // Load relasi roles untuk memastikan metode hasRole bekerja dengan data terbaru
        $user->load('roles');

        if (!$user->hasRole(strtolower($selectedRole))) {
            // Periksa apakah pengguna bisa login sebagai alumni berdasarkan status lulus
            if ($selectedRole === 'alumni' && $user->isAlumni()) {
                // Izinkan login sebagai alumni (sudah ditangani oleh isAlumni)
            } elseif ($selectedRole === 'mahasiswa' && $user->student && $user->student->alumni) {
                // Jika mahasiswa sudah lulus dan mencoba login sebagai mahasiswa, arahkan ke alumni
                $alumniRole = Role::where('name', 'alumni')->first();
                if ($alumniRole) {
                    $user->roles()->detach(); // Hapus semua peran yang ada
                    $user->roles()->attach($alumniRole->id); // Lampirkan peran 'alumni'
                }
                $selectedRole = 'alumni';
            } else {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Anda tidak memiliki akses untuk login sebagai ' . $selectedRole])
                    ->withInput($request->except('password'));
            }
        }

        // Verifikasi data berdasarkan role
        switch ($selectedRole) {
            case 'mahasiswa':
                if (!$user->relationLoaded('student')) {
                    $user->load('student');
                }
                if (!$user->student) {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Data mahasiswa tidak ditemukan'])
                        ->withInput($request->except('password'));
                }
                // Jika mahasiswa sudah lulus dan mencoba login sebagai mahasiswa, arahkan ke alumni
                if ($user->student->alumni && !$user->hasRole('alumni')) {
                    $alumniRole = Role::where('name', 'alumni')->first();
                    if ($alumniRole) {
                        $user->roles()->detach();
                        $user->roles()->attach($alumniRole->id);
                    }

                    $selectedRole = 'alumni';
                }
                break;

            case 'dekan':
                if (!$user->relationLoaded('lecturer')) {
                    $user->load('lecturer');
                }
                if (!$user->lecturer) {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Data Dekan tidak ditemukan.'])
                        ->withInput($request->except('password'));
                }
                break;
            case 'dosen':
            case 'ujm':
            case 'kaprodi':
                if (!$user->relationLoaded('lecturer')) {
                    $user->load('lecturer');
                }
                if (!$user->lecturer) {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Data dosen tidak ditemukan'])
                        ->withInput($request->except('password'));
                }
                break;

            case 'staff':
                if (!$user->relationLoaded('employee')) {
                    $user->load('employee.department');
                }
                if (!$user->employee || !$user->employee->department) {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Data Admin Prodi tidak ditemukan'])
                        ->withInput($request->except('password'));
                }
                break;

            case 'ktu':
                if (!$user->relationLoaded('employee')) {
                    $user->load('employee');
                }
                if (!$user->employee || $user->employee->level !== 'faculty') {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Data KTU tidak valid atau tidak ditemukan.'])
                        ->withInput($request->except('password'));
                }
                break;

            case 'alumni':
                if (!$user->relationLoaded('alumni')) {
                    $user->load('alumni');
                }
                if (!$user->alumni && !$user->isAlumni()) {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Anda bukan alumni atau data alumni tidak ditemukan'])
                        ->withInput($request->except('password'));
                }
                break;
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }



    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
