<?php

namespace App\Http\Controllers\Backend;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $role = $user->roles->pluck('name')->first(); // Ambil role utama (anggap satu role aktif)

        $activeYear = AcademicYear::where('status', true)->firstOrFail();
        $folder = $this->getPhotoFolderByRole($user);
        $backRoute = 'dashboard';

        switch ($role) {
            case 'ktu':
                $ktu = $user->employee;
                if (!$ktu)
                    abort(404, 'Data KTU tidak ditemukan.');

                return view('backend.profile.index', [
                    'titleName' => $ktu->nama,
                    'photo' => $user->photo ?? 'default.png',
                    'photoRoute' => route('profile.photo.update'),
                    'passwordRoute' => route('profile.password.update'),
                    'backRoute' => $backRoute,
                    'folder' => $folder,
                    'badges' => [
                        ['label' => 'NIP', 'value' => $ktu->nip, 'class' => 'success'],
                    ],
                    'fields' => [
                        'Nama Lengkap' => $ktu->nama,
                        'NIP' => $ktu->nip,
                        'Fakultas' => $ktu->department->faculty->nama ?? '-',
                        'Program Studi' => $ktu->department->nama ?? '-',
                        'Posisi' => $ktu->position,
                        'Alamat' => $ktu->alamat,
                        'Email' => $ktu->email,
                        'Tempat Lahir' => $ktu->tpl,
                        'Tanggal Lahir' => $ktu->tgl,
                    ],
                ]);
            case 'staff':
                $staff = $user->employee;
                if (!$staff)
                    abort(404, 'Data staff tidak ditemukan.');

                return view('backend.profile.index', [
                    'titleName' => $staff->nama,
                    'photo' => $user->photo ?? 'default.png',
                    'photoRoute' => route('profile.photo.update'),
                    'passwordRoute' => route('profile.password.update'),
                    'backRoute' => $backRoute,
                    'folder' => $folder,
                    'badges' => [
                        ['label' => 'NIP', 'value' => $staff->nip, 'class' => 'success'],
                    ],
                    'fields' => [
                        'Nama Lengkap' => $staff->nama,
                        'NIP' => $staff->nip,
                        'Fakultas' => $staff->department->faculty->nama ?? '-',
                        'Program Studi' => $staff->department->nama ?? '-',
                        'Posisi' => $staff->position,
                        'Alamat' => $staff->alamat,
                        'Email' => $staff->email,
                        'Tempat Lahir' => $staff->tpl,
                        'Tanggal Lahir' => $staff->tgl,
                    ],
                ]);
            case 'dosen':
            case 'kaprodi':
            case 'dekan':
                $lecturer = $user->lecturer;
                if (!$lecturer)
                    abort(404, 'Data dosen tidak ditemukan.');

                return view('backend.profile.index', [
                    'titleName' => $lecturer->nama_dosen,
                    'photo' => $user->photo ?? 'default.png',
                    'photoRoute' => route('profile.photo.update'),
                    'passwordRoute' => route('profile.password.update'),
                    'backRoute' => $backRoute,
                    'folder' => $folder,
                    'badges' => [
                        ['label' => 'NIDN', 'value' => $lecturer->nidn, 'class' => 'info'],
                    ],
                    'fields' => [
                        'Nama Lengkap' => $lecturer->nama_dosen,
                        'NIDN' => $lecturer->nidn,
                        'Fakultas' => $lecturer->department->faculty->nama ?? '-',
                        'Program Studi' => $lecturer->department->nama ?? '-',
                        'Jabatan Fungsional' => $lecturer->jafung,
                        'Email' => $lecturer->email,
                        'Google Scholar' => $lecturer->scholar_google
                            ? '<a href="https://scholar.google.com/citations?user=' . e($lecturer->scholar_google) . '" target="_blank" rel="noopener noreferrer">Lihat Profil</a>'
                            : '-',
                        'Telpon' => $lecturer->telp,
                        'Tempat Lahir' => $lecturer->tpl,
                        'Tanggal Lahir' => $lecturer->tgl ? \Carbon\Carbon::parse($lecturer->tgl)->translatedFormat('j F Y') : '-',
                    ],
                ]);

            case 'mahasiswa':
            case 'alumni':
                $student = $user->student ?? $user->alumni;
                if (!$student)
                    abort(404, 'Data mahasiswa tidak ditemukan.');

                return view('backend.profile.index', [
                    'titleName' => $student->nama_mhs,
                    'photo' => $user->photo ?? 'default.png',
                    'photoRoute' => route('profile.photo.update'),
                    'passwordRoute' => route('profile.password.update'),
                    'backRoute' => $backRoute,
                    'folder' => $folder,
                    'badges' => [
                        ['label' => 'NIM', 'value' => $student->nim, 'class' => 'primary'],
                    ],
                    'fields' => [
                        'Nama Lengkap' => $student->nama_mhs,
                        'NIM' => $student->nim,
                        'Fakultas' => $student->department->faculty->nama ?? '-',
                        'Program Studi' => $student->department->nama ?? '-',
                        'Email' => $student->email,
                        'Jenis Kelamin' => $student->gender ?? '-',
                        'Total SKS' => $student->total_sks ?? '-',
                        'Tahun Masuk' => $student->entry_year ?? '-',
                        'Tempat Lahir' => $student->tpl,
                        'Tanggal Lahir' => $student->tgl ? \Carbon\Carbon::parse($student->tgl)->translatedFormat('j F Y') : '-',
                    ],
                ]);

            case 'admin':
                return view('backend.profile.index', [
                    'titleName' => $user->name,
                    'photo' => $user->photo ?? 'admin.jpg',
                    'photoRoute' => route('profile.photo.update'),
                    'passwordRoute' => route('profile.password.update'),
                    'backRoute' => $backRoute,
                    'folder' => $folder,
                    'badges' => [['label' => 'ADMINISTRATOR', 'value' => '', 'class' => 'dark']],
                    'fields' => [
                        'Nama' => $user->name,
                        'Email' => $user->email,
                        'Username' => $user->username ?? '-',
                    ],
                ]);

            default:
                abort(403, 'Role tidak dikenali');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:256'
        ]);

        try {
            $user = $request->user();
            $photo = $request->file('photo');
            $fileName = time() . '.' . $photo->getClientOriginalExtension();
            $oldPhoto = $user->photo;
            $folder = $this->getPhotoFolderByRole($user);
            $storagePath = "public/images/{$folder}/";

            // Simpan file baru
            Storage::putFileAs($storagePath, $photo, $fileName);

            // Update kolom photo di tabel users
            $user->update(['photo' => $fileName]);

            // Hapus file lama jika bukan default
            if ($oldPhoto && !in_array($oldPhoto, ['admin.jpg', 'dosen.jpg', 'mhs.jpg'])) {
                Storage::delete($storagePath . $oldPhoto);
            }

            return back()->with('success', 'Foto profil berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update password: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan. Coba lagi nanti.');
        }
    }

    private function getPhotoFolderByRole($user)
    {
        return match (true) {
            $user->hasRole('admin') => 'admin',
            $user->hasRole('staff') => 'staff',
            $user->hasRole('dosen'), $user->hasRole('kaprodi'), $user->hasRole('dekan'), $user->hasRole('ujm') => 'dosen',
            $user->hasRole('mahasiswa'), $user->hasRole('alumni') => 'mhs',
            default => 'misc'
        };
    }

    public function updateBio(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first();

        $rules = [];
        $data = [];

        switch ($role) {
            case 'dosen':
            case 'kaprodi':
            case 'dekan':
                $rules = [
                    'nama_dosen' => 'required|string|max:255',
                    'jafung' => 'nullable|string|max:25',
                    'address' => 'nullable|string|max:255',
                    'scholar_google' => ['nullable', 'string', 'max:15', 'regex:/^[a-zA-Z0-9]{12}$/',],
                    'telp' => 'nullable|string|max:255',
                    'tpl' => 'nullable|string|max:255',
                    'tgl' => 'nullable|date',
                ];
                $data = $request->only(['nama_dosen', 'jafung', 'address', 'tpl', 'tgl', 'scholar_google']);
                $user->lecturer->update($data);
                break;

            case 'mahasiswa':
                $rules = [
                    'nama_mhs' => 'required|string|max:255',
                    'address' => 'nullable|string|max:255',
                    'tpl' => 'nullable|string|max:255',
                    'tgl' => 'nullable|date',
                ];
                $data = $request->only(['nama_mhs', 'address', 'tpl', 'tgl']);
                $user->student->update($data);
                break;

            case 'staff':
                $rules = [
                    'nama' => 'required|string|max:255',
                    'position' => 'nullable|string|max:55',
                    'alamat' => 'nullable|string|max:255',
                ];
                $data =
                    [
                        'nama' => $request->input('nama'),
                        'position' => $request->input('position'),
                        'alamat' => $request->input('alamat'),
                    ];
                $user->employee->update($data);
                break;

            default:
                return back()->with('error', 'Role tidak dikenali.');
        }

        $request->validate($rules);

        return back()->with('success', 'Data berhasil diperbarui.');
    }


}