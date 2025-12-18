<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Skripsi\ThesisExamController;
use App\Http\Controllers\Student\MhsController;
use App\Http\Controllers\Student\ThesisController;
use App\Http\Controllers\Student\NilaiMhsController;
use App\Http\Controllers\Student\StudyPlanController;
use App\Http\Controllers\Lecturer\PresensiKuliahController;
use App\Http\Controllers\Student\ThesisSupervisionController;

Route::middleware(['auth', 'checkRole:mahasiswa'])->prefix('mhs')->name('student.')->group(function () {
    Route::prefix('krs')->name('krs.')->group(function () {
        Route::get('/', [StudyPlanController::class, 'index'])->name('index');
        Route::post('/', [StudyPlanController::class, 'store'])->name('store');
        Route::delete('/{studyPlan}', [StudyPlanController::class, 'destroy'])->name('destroy');
        Route::get('/print', [StudyPlanController::class, 'print'])->name('print');
    });

    Route::get('jadwal', [StudyPlanController::class, 'jadwal'])->name('jadwal');
    Route::get('presensi', [NilaiMhsController::class, 'presensi'])->name('presensi');

    Route::prefix('nilai')->name('nilai.')->group(function () {
        Route::get('/', [NilaiMhsController::class, 'index'])->name('index');
        Route::get('/{id}/print', [NilaiMhsController::class, 'print'])->name('print');
    });

    Route::middleware(['checkSks'])->prefix('thesis')->name('thesis.')->group(function () {
        Route::get('/supervision', [ThesisSupervisionController::class, 'index'])->name('supervision.index');
        Route::get('/supervision/{id}', [ThesisSupervisionController::class, 'show'])->name('supervision.show');
        Route::get('/supervision/meeting/create/{supervisorRole}', [ThesisSupervisionController::class, 'create'])->name('supervision.meeting.create');
        Route::post('/supervision/meeting', [ThesisSupervisionController::class, 'store'])->name('supervision.meeting.store');
        Route::get('/supervision/meeting/{meeting}', [ThesisSupervisionController::class, 'showBimbingan'])->name('supervision.meeting.show');
        Route::get('/print-history', [ThesisSupervisionController::class, 'printHistory'])->name('print-history');
    });

    Route::get('scan-qr', [MhsController::class, 'showScanQR'])->name('scan.qr');
    Route::post('attendance/verify-qr', [PresensiKuliahController::class, 'verifyAttendanceQR'])->name('attendance.verify.qr');

    // TUGAS AKHIR
    Route::get('/ujian', [ThesisExamController::class, 'index'])->name('thesis.exam.index');
    Route::get('skripsi/{thesis}', [ThesisController::class, 'show'])->name('thesis.show');
    Route::get('/skripsi/{thesis}/revisi-ujian', [ThesisExamController::class, 'formRevision'])->name('thesis.exam.revision.form');
    Route::post('/skripsi/{thesis}/revisi-ujian', [ThesisExamController::class, 'submitRevision'])->name('thesis.exam.revision.submit');

    Route::get('skripsi/{exam}/detail-ujian', [ThesisExamController::class, 'show'])->name('thesis.exam.show');
});