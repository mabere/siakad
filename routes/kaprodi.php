<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Skripsi\ThesisExamController;
use App\Http\Controllers\Kaprodi\EdomKaprodiController;
use App\Http\Controllers\Kaprodi\MhsByKaprodiController;
use App\Http\Controllers\Kaprodi\DosenByKaprodiController;
use App\Http\Controllers\Kaprodi\KelasByKaprodiController;
use App\Http\Controllers\Kaprodi\LetterTypeByKaprodiController;
use App\Http\Controllers\Kaprodi\GradeValidationByProdiController;

Route::middleware(['auth', 'checkRole:kaprodi'])->prefix('kaprodi')->name('kaprodi.')->group(function () {

    // ========= EDOM =========
    Route::prefix('edom')->name('edom.')->group(function () {
        Route::get('/', [EdomKaprodiController::class, 'index'])->name('index');
        Route::get('/reports', [EdomKaprodiController::class, 'reports'])->name('reports');
        Route::get('/reports/lecturer/{lecturer}', [EdomKaprodiController::class, 'lecturerDetail'])->name('reports.lecturer');
        Route::get('/reports/schedule/{schedule}', [EdomKaprodiController::class, 'scheduleDetail'])->name('reports.schedule');
        Route::get('/export', [EdomKaprodiController::class, 'export'])->name('export');
    });

    // ========= NILAI (Grades) =========
    Route::prefix('nilai')->name('nilai.')->group(function () {
        Route::get('validasi', [GradeValidationByProdiController::class, 'showValidation'])->name('validasi');
        Route::post('{id}/approve', [GradeValidationByProdiController::class, 'approveByProdi'])->name('approve.prodi');
    });

    // ========= TIPE SURAT =========
    Route::resource('letter-types', LetterTypeByKaprodiController::class);

    // ========= MANAJEMEN KELAS =========
    Route::resource('kelas', KelasByKaprodiController::class);
    Route::get('kelas/{id}/add-students', [KelasByKaprodiController::class, 'showAddStudents'])->name('kelas.add-students');
    Route::post('kelas/{id}/store-students', [KelasByKaprodiController::class, 'storeStudents'])->name('kelas.store-students');
    Route::delete('kelas/{kelasId}/remove-student/{studentId}', [KelasByKaprodiController::class, 'removeStudent'])->name('kelas.remove-student');

    // ========= MANAJEMEN DOSEN & MAHASISWA =========
    Route::resource('dosen', DosenByKaprodiController::class);
    Route::resource('mahasiswa', MhsByKaprodiController::class);


});

Route::middleware(['auth', 'checkRole:kaprodi'])->prefix('ujian/skripsi')->group(function () {
    // ========= MANAJEMEN UJIAN SKRIPSI =========
    Route::get('/', [ThesisExamController::class, 'indexForKaprodi'])->name('kaprodi.thesis.exam.index');
    Route::get('/{exam}', [ThesisExamController::class, 'showForKaprodi'])->name('kaprodi.thesis.exam.show');
    // PENETAPAN PENGUJI
    Route::get('/{exam}/penguji', [ThesisExamController::class, 'formExaminers'])->name('kaprodi.thesis.examiners.form');
    Route::post('/{exam}/penguji', [ThesisExamController::class, 'assignExaminers'])->name('kaprodi.thesis.examiners.assign');
    Route::put('/{exam}/penguji', [ThesisExamController::class, 'updateExaminers'])->name('kaprodi.thesis.examiners.update');
    // PENJADWALAN UJIAN
    Route::get('/{exam}/jadwal', [ThesisExamController::class, 'formSchedule'])->name('kaprodi.thesis.schedule.form');
    Route::post('/{exam}/jadwal', [ThesisExamController::class, 'storeSchedule'])->name('kaprodi.thesis.schedule.store');
    // NILAI UJIAN SKRIPSI
    Route::get('/{exam}/detail-nilai', [ThesisExamController::class, 'scoreDetails'])->name('kaprodi.exam.score.details');
    Route::post('/{exam}/revisi-nilai/{examiner}', [ThesisExamController::class, 'reviseScore'])->name('kaprodi.exam.score.revise');
});
Route::get('/sk-panitia/cetak', [ThesisExamController::class, 'printSkPanitiaUjian'])->name('thesis.exam.print.sk');