<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Ktu\KtuThesisExamController;
use App\Http\Controllers\Admin\ReviewNilaiUjianController;
use App\Http\Controllers\Student\MahasiswaThesisController;
use App\Http\Controllers\Lecturer\UjianSkripsiDosenController;

// Route Mahasiswa
Route::middleware(['auth', 'checkRole:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Kelola Skripsi
    Route::get('skripsi', [MahasiswaThesisController::class, 'index'])->name('thesis.index');
    Route::get('skripsi/create', [MahasiswaThesisController::class, 'create'])->name('thesis.create');
    Route::post('skripsi', [MahasiswaThesisController::class, 'store'])->name('thesis.store');
    Route::get('skripsi/{thesis}', [MahasiswaThesisController::class, 'show'])->name('thesis.show');
    // Pendaftaran Ujian
    Route::get('skripsi/{thesis}/exam/{exam}', [MahasiswaThesisController::class, 'showExamDetails'])->name('thesis.exam.show');
    Route::get('skripsi/{thesis}/register-exam', [MahasiswaThesisController::class, 'showExamRegistrationForm'])->name('thesis.exam.register');
    Route::post('skripsi/{thesis}/register-exam', [MahasiswaThesisController::class, 'registerExam'])->name('thesis.exam.store');

});

// Route KTU
Route::middleware(['auth', 'checkRole:ktu'])->prefix('ktu/skripsi')->name('ktu.thesis.')->group(function () {
    // Verifikasi Pendaftaran Ujian
    Route::get('/verifikasi-ujian', [KtuThesisExamController::class, 'index'])->name('exam.index');
    Route::get('/verifikasi-ujian/{exam}', [KtuThesisExamController::class, 'show'])->name('exam.show');
    Route::post('/verifikasi-ujian/{exam}/verifikasi', [KtuThesisExamController::class, 'verify'])->name('exam.verify');
    Route::post('/verifikasi-ujian/{exam}/tolak', [KtuThesisExamController::class, 'reject'])->name('exam.reject');

    // Penjadwalan Ujian (hanya jika sudah diverifikasi)
    Route::get('/jadwal', [KtuThesisExamController::class, 'indexSchedule'])->name('schedule.index');
    Route::get('{exam}/jadwal', [KtuThesisExamController::class, 'showSchedule'])->name('schedule.show');
    Route::get('{exam}/jadwal/form', [KtuThesisExamController::class, 'formSchedule'])->name('schedule.form');
    Route::post('{exam}/jadwal', [KtuThesisExamController::class, 'storeSchedule'])->name('schedule.store');
    Route::patch('/{exam}/jadwal/ubah', [KtuThesisExamController::class, 'updateReschedule'])->name('schedule.update');
});

Route::middleware(['auth', 'checkRole:dosen'])->prefix('nilai/')->name('nilai.')->group(function () {
    Route::get('ujian', [UjianSkripsiDosenController::class, 'index'])->name('examiner.exams.index');
    Route::get('ujian/{thesis_exam}', [UjianSkripsiDosenController::class, 'show'])->name('examiner.exams.show');
    Route::post('ujian/{thesis_exam}', [UjianSkripsiDosenController::class, 'storeScore'])->name('examiner.exams.storeScore');
    Route::get('ujian/{thesis_exam}/scores', [UjianSkripsiDosenController::class, 'getScores'])->name('examiner.exams.scores');
});


Route::middleware(['auth', 'checkRole:admin,kaprodi'])->prefix('review/nilai/ujian/')->name('review.nilai.ujian.')->group(function () {
    Route::get('/', [ReviewNilaiUjianController::class, 'index'])->name('index');
    Route::get('/{thesis_exam}', [ReviewNilaiUjianController::class, 'show'])->name('show');
    Route::post('/{thesis_exam}/keputusan', [ReviewNilaiUjianController::class, 'decide'])->name('decide');
});
