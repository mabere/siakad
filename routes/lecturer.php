<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lecturer\BapController;
use App\Http\Controllers\Lecturer\DosenController;
use App\Http\Controllers\Lecturer\GradesController;
use App\Http\Controllers\Lecturer\ServiceController;
use App\Http\Controllers\Lecturer\ResearchController;
use App\Http\Controllers\Lecturer\PenunjangController;
use App\Http\Controllers\Lecturer\ThesisExamController;
use App\Http\Controllers\Lecturer\KrsApprovalController;
use App\Http\Controllers\Lecturer\PresensiKuliahController;
use App\Http\Controllers\Kaprodi\GradeValidationByProdiController;
use App\Http\Controllers\Lecturer\LecturerThesesSupervisionController;

Route::middleware(['auth', 'checkRole:dosen,ujm,dekan,kaprodi'])
    ->prefix('dosen')
    ->name('lecturer.')
    ->group(function () {

        Route::prefix('edom')->name('edom.')->group(function () {
            Route::get('/', [DosenController::class, 'indexEdom'])->name('index');
            Route::get('/schedule/{schedule}', [DosenController::class, 'scheduleDetail'])->name('schedule.detail');
            Route::get('/schedule/excel', [DosenController::class, 'exportExcel'])->name('export.excel');
            Route::get('/schedule/pdf', [DosenController::class, 'exportPdf'])->name('export.pdf');
        });
        Route::get('riwayat-mengajar', [DosenController::class, 'teachingHistory'])->name('riwayat.mengajar');

        Route::resources(['publication' => ResearchController::class, 'pkm' => ServiceController::class, 'penunjang' => PenunjangController::class,]);
        Route::post('/dosen/publications/import', [ResearchController::class, 'import'])->name('publications.import');
        Route::post('/dosen/pkm/import', [ResearchController::class, 'importPkm'])->name('pkm.import');
        Route::post('/dosen/penunjang/import', [ResearchController::class, 'importPenunjang'])->name('penunjang.import');

        // Penunjang tambahan
        Route::get('/dashboard/penunjang', [PenunjangController::class, 'dashboard'])->name('penunjang.dashboard');
        Route::prefix('penunjang')->name('penunjang.')->group(function () {
            Route::get('/export-pdf/file', [PenunjangController::class, 'exportPDF'])->name('export-pdf');
            Route::get('/export-excel', [PenunjangController::class, 'exportExcel'])->name('export-excel');
            Route::get('/print-pdf', [PenunjangController::class, 'printPDF'])->name('print-pdf');
        });

        Route::prefix('krs-approval')->name('krs.')->group(function () {
            Route::get('/', [KrsApprovalController::class, 'index'])->name('index');
            Route::post('/{id}/approve', [KrsApprovalController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [KrsApprovalController::class, 'reject'])->name('reject');
            Route::get('/student/{studentId}', [KrsApprovalController::class, 'showKrs'])->name('show');
            Route::post('/student/{studentId}/bulk-approve', [KrsApprovalController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/student/{studentId}/bulk-reject', [KrsApprovalController::class, 'bulkReject'])->name('bulk-reject');
        });

        Route::resource('attendance', PresensiKuliahController::class);
        Route::get('schedules', [PresensiKuliahController::class, 'schedules'])->name('schedules');
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('{id}/input/{pertemuan}', [PresensiKuliahController::class, 'showPresensiForm'])->name('input');
            Route::post('{id}/input/{pertemuan}', [PresensiKuliahController::class, 'storePresensi'])->name('stores');
            Route::get('{id}/qr/{pertemuan}', [PresensiKuliahController::class, 'generateAttendanceQR'])->name('qr');
            Route::get('{id}/qr/{pertemuan}/scan', [PresensiKuliahController::class, 'scanAttendanceQR'])->name('scan');
            Route::get('{id}/print', [PresensiKuliahController::class, 'print'])->name('print');
        });

        Route::resource('nilai', GradesController::class);
        Route::prefix('nilai')->name('nilai.')->group(function () {
            Route::get('{id}/print', [GradesController::class, 'print'])->name('print');
            Route::post('{id}/validate', [GradesController::class, 'validatedByDosen'])->name('validate.dosen');
        });

        Route::prefix('bap')->name('bap.')->group(function () {
            Route::get('/', [BapController::class, 'index'])->name('index');
            Route::get('/{id}', [BapController::class, 'show'])->name('show');
            Route::get('/{id}/pertemuan/{pertemuan}', [BapController::class, 'create'])->name('create');
            Route::post('/{id}/pertemuan/{pertemuan}', [BapController::class, 'store'])->name('store');
            Route::get('/{id}/laporan', [BapController::class, 'printLaporan'])->name('laporan');
        });

        Route::prefix('bimbingan/skripsi')->name('thesis.supervision.')->group(function () {
            Route::get('/', [LecturerThesesSupervisionController::class, 'index'])->name('index');
            Route::get('/{supervision}', [LecturerThesesSupervisionController::class, 'show'])->name('show');
            Route::post('/meeting/{meeting}/respond', [LecturerThesesSupervisionController::class, 'respondToMeeting'])->name('meeting.respond');
        });
    });

Route::post('attendance/verify-qr', [PresensiKuliahController::class, 'verifyAttendanceQR'])
    ->name('lecturer.attendance.verify.qr')
    ->middleware('web');

Route::middleware(['auth', 'checkRole:dosen'])->prefix('dosen/ujian-skripsi')->group(function () {
    Route::get('/{thesis}/input-nilai', [ThesisExamController::class, 'inputScoreForm'])->name('lecturer.exam.score.form');
    Route::post('/{thesis}/input-nilai', [ThesisExamController::class, 'storeScore'])->name('lecturer.exam.score.store');
});
