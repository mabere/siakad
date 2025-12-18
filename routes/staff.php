<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffDosenController;
use App\Http\Controllers\Staff\StaffKelasController;
use App\Http\Controllers\Staff\StaffCourseController;
use App\Http\Controllers\Staff\DashboardStaffController;
use App\Http\Controllers\Staff\StaffMahasiswaController;
use App\Http\Controllers\Staff\JadwalKuliahByStaffController;
use App\Http\Controllers\Surat\LetterRequestByKaprodiController;
use App\Http\Controllers\Kaprodi\GradeValidationByProdiController;

Route::middleware(['auth', 'checkRole:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::post('assign/{id}/user', [StaffMahasiswaController::class, 'assignUserToMahasiswa'])->name('assign');
        Route::post('unassign/{id}', [StaffMahasiswaController::class, 'unassignUserToMahasiswa'])->name('unassign');
        Route::post('assign-multiple', [
            StaffMahasiswaController::class,
            'assignMultipleUserFromMahasiswa'
        ])->name('assign-multiple');
        Route::get('advisor', [StaffMahasiswaController::class, 'show'])->name('advisor');
        Route::post('advisor', [StaffMahasiswaController::class, 'assignAdvisor'])->name('advisor.store');
    });

    Route::resources([
        'kelas' => StaffKelasController::class,
        'course' => StaffCourseController::class,
        'jadwal' => JadwalKuliahByStaffController::class,
    ]);

    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('{id}/add-students', [StaffKelasController::class, 'showAddStudents'])->name('add-students');
        Route::post('{id}/store-students', [StaffKelasController::class, 'storeStudents'])->name('store-students');
        Route::delete('{kelasId}/remove-student/{studentId}', [StaffKelasController::class, 'removeStudent'])->name('remove-student');
    });

    Route::prefix('nilai')->name('nilai.')->group(function () {
        Route::get('validasi', [GradeValidationByProdiController::class, 'showValidation'])->name('validasi');
        Route::post('{id}/lock', [GradeValidationByProdiController::class, 'lockGrades'])->name('lock');
    });

    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::post('assign/{id}/user', [StaffDosenController::class, 'assignUserToDosen'])->name('assign');
        Route::post('unassign/{id}', [StaffDosenController::class, 'unassignRoleToDosen'])->name('unassign');
        Route::post('assign-multiple', [StaffDosenController::class, 'assignMultipleUserFromDosen'])->name('assign-multiple');
    });

    Route::prefix('import')->name('import.')->group(function () {
        Route::post('course', [StaffCourseController::class, 'import'])->name('course');
        Route::post('jadwal', [JadwalKuliahByStaffController::class, 'import'])->name('jadwal');
        Route::post('lecturer', [StaffDosenController::class, 'import'])->name('lecturer');
        Route::post('students', [StaffMahasiswaController::class, 'import'])->name('students');
    });
});


$sharedStaffKtuRoutes = function () {
    Route::prefix('prodi')->name('department.')->group(function () {
        Route::get('/', [DashboardStaffController::class, 'department'])->name('index');
        Route::get('/{department}', [DashboardStaffController::class, 'show'])->name('show');
    });
    Route::resources([
        'dosen' => StaffDosenController::class,
        'mahasiswa' => StaffMahasiswaController::class,
    ]);
    Route::get('mahasiswa/{id}/detail', [StaffMahasiswaController::class, 'showDetail'])->name('mahasiswa.show.detail');
    Route::controller(LetterRequestByKaprodiController::class)->group(function () {
        Route::get('surat-masuk', 'index')->name('letter-request.index');
        Route::get('surat-masuk/{letterRequest}', 'show')->name('letter-request.show');
        Route::post('surat-masuk/{letterRequest}/review', 'review')->name('letter-request.review');
        Route::post('surat-masuk/{letterRequest}/reject', 'reject')->name('letter-request.reject');
    });
};

// // Route untuk staff (departemen)
Route::middleware(['auth', 'checkRole:staff'])->prefix('staff')->name('staff.')->group($sharedStaffKtuRoutes);

// Route untuk ktu (fakultas)
Route::middleware(['auth', 'checkRole:ktu'])->prefix('ktu')->name('ktu.')->group($sharedStaffKtuRoutes);