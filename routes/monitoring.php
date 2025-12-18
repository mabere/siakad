<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LearningMonitoringController;
use App\Http\Controllers\Lecturer\LecturerMonitoringController;

/*
|--------------------------------------------------------------------------
| MONITORING - Admin & GKM
|--------------------------------------------------------------------------
*/
Route::prefix('admin/monitoring')->middleware(['auth', 'checkRole:admin,gkm'])->name('admin.monitoring.')->controller(LearningMonitoringController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    // Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{monitoring}', 'show')->name('show');
    Route::get('/{monitoring}/edit', 'edit')->name('edit');
    Route::put('/{monitoring}', 'update')->name('update');
    Route::put('/{monitoring}/verify', 'verify')->name('verify');
    Route::put('/{monitoring}/revision', 'requestRevision')->name('revision');
});

/*
|--------------------------------------------------------------------------
| MONITORING - Dosen
|--------------------------------------------------------------------------
*/
Route::prefix('lecturer/monitoring')->middleware(['auth', 'checkRole:dosen'])->name('lecturer.monitoring.')->controller(LecturerMonitoringController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create/{schedule}', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{monitoring}', 'show')->name('show');
    Route::get('/{monitoring}/edit', 'edit')->name('edit');
    Route::put('/{monitoring}', 'update')->name('update');
});
