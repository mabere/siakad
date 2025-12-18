<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dekan\KegiatanController;
use App\Http\Controllers\Skripsi\ThesisApprovalController;
use App\Http\Controllers\Dekan\EdomDekanController;
use App\Http\Controllers\Dekan\DekanDashboardController;
use App\Http\Controllers\Dekan\AcademicByDekanController;

Route::middleware(['auth', 'checkRole:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
    Route::prefix('edom')->name('edom.')->group(function () {
        Route::get('/', [EdomDekanController::class, 'index'])->name('index');
        Route::get('/reports/department/{department}', [EdomDekanController::class, 'departmentReport'])->name('reports.department');
    });
    Route::controller(DekanDashboardController::class)->group(function () {
        Route::get('/departments', 'indexProdi')->name('departments.index');
        Route::get('/departments/{id}', 'show')->name('departments.show');
        Route::get('/statistik/mhs', 'studentStatistics')->name('department.student-statistics');
        Route::get('/department/{department}/detail/mhs', 'studentDetails')->name('department.student-details');
    });
    Route::get('/akademik/report', [AcademicByDekanController::class, 'academicReport'])->name('academic.index');

    Route::prefix('ujian/skripsi')->name('thesis.exam.')->group(function () {
        Route::get('/', [ThesisApprovalController::class, 'index'])->name('index');
        Route::get('/{exam}', [ThesisApprovalController::class, 'show'])->name('show');
        Route::patch('/{exam}/approve', [ThesisApprovalController::class, 'approve'])->name('approve');
        Route::patch('/{exam}/revisi', [ThesisApprovalController::class, 'revisi'])->name('revisi');
    });
});

Route::prefix('kegiatan')->name('dekan.kegiatan.')->middleware('checkRole:dekan|kaprodi')->group(function () {
    Route::resource('/akademik', KegiatanController::class);
    Route::post('/akademik/{kegiatan}/publish', [KegiatanController::class, 'publishEvent'])->name('akademik.publish');

});
