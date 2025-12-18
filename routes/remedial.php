<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Nilai\GradesCorrectionController;

Route::middleware('auth')->group(function () {

    Route::middleware('checkRole:mahasiswa')->prefix('mhs/remedial')->name('mhs.remedial.')->controller(GradesCorrectionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{request}', 'show')->name('show');
    });

    Route::middleware('checkRole:dosen')->prefix('dosen/remedial')->name('dosen.remedial.')->controller(GradesCorrectionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{request}', 'show')->name('show');
        Route::post('/{request}/process', 'process')->name('process');
    });

    Route::middleware('checkRole:staff')->prefix('staff/remedial')->name('staff.remedial.')->controller(GradesCorrectionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{request}', 'show')->name('show');
        Route::post('/{request}/review', 'review')->name('review');
        Route::post('/{request}/validate', 'performValidate')->name('validate');
    });

    Route::middleware('checkRole:kaprodi')->prefix('kaprodi/remedial')->name('kaprodi.remedial.')->controller(GradesCorrectionController::class)->group(function () {
        Route::get('/', 'indexKaprodi')->name('index');
        Route::get('/{request}', 'show')->name('show');
        Route::post('/{request}/approve', 'approve')->name('approve');
    });
});