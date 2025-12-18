<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dekan\LetterTypeByDekanController;
use App\Http\Controllers\Surat\LetterRequestByMhsController;
use App\Http\Controllers\Surat\LetterRequestByDekanController;
use App\Http\Controllers\Surat\LetterRequestByDosenController;
use App\Http\Controllers\Surat\LetterRequestByKaprodiController;
use App\Http\Controllers\Surat\KaprodiReviewDosenRequestController;

Route::middleware(['auth'])->group(function () {
    Route::middleware(['checkRole:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
        Route::resource('letter-types', LetterTypeByDekanController::class);

        Route::prefix('suratmasuk')->name('request.surat-masuk.')->controller(LetterRequestByDekanController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{letterRequest}', 'show')->name('show');
            Route::post('{letterRequest}/approve', 'approve')->name('approve');
            Route::post('{letterRequest}/reject', 'reject')->name('reject');
        });
    });

    Route::middleware(['checkRole:kaprodi'])->prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::prefix('request')->name('request.')->controller(LetterRequestByKaprodiController::class)->group(function () {
            Route::get('surat-masuk', 'index')->name('surat-masuk.index');
            Route::get('surat-masuk/{letterRequest}', 'show')->name('surat-masuk.show');
            Route::post('surat-masuk/{letterRequest}/review', 'review')->name('surat-masuk.review');
            Route::post('surat-masuk/{letterRequest}/reject', 'reject')->name('surat-masuk.reject');
        });

        Route::prefix('review/dosen')->name('review.dosen.')->controller(KaprodiReviewDosenRequestController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{letterRequest}', 'dosen')->name('show');
            Route::post('{letterRequest}/approve', 'approve')->name('approve');
        });
    });

    // Route::middleware(['checkRole:staff'])->prefix('staff')->name('staff.letter-request.')->controller(LetterRequestByKaprodiController::class)->group(function () {
    //     Route::get('surat-masuk', 'index')->name('index');
    //     Route::get('surat-masuk/{letterRequest}', 'show')->name('show');
    //     Route::post('surat-masuk/{letterRequest}/review', 'review')->name('review');
    //     Route::post('surat-masuk/{letterRequest}/reject', 'reject')->name('reject');
    // });

    Route::middleware(['checkRole:dosen'])->prefix('dosen')->name('lecturer.request.')->controller(LetterRequestByDosenController::class)->group(function () {
        Route::get('surat', 'index')->name('surat.index');
        Route::get('surat/create', 'create')->name('surat.create');
        Route::post('surat', 'store')->name('surat.store');
        Route::get('surat/{letterRequest}', 'show')->name('surat.show');
        Route::get('surat/{letterRequest}/download', 'download')->name('surat.download');
    });

    Route::middleware(['checkRole:mahasiswa'])->prefix('mhs')->name('student.request.')->controller(LetterRequestByMhsController::class)->group(function () {
        Route::get('surat', 'index')->name('surat.index');
        Route::get('surat/create', 'create')->name('surat.create');
        Route::post('surat', 'store')->name('surat.store');
        Route::get('surat/{letterRequest}/edit', 'edit')->name('surat.edit');
        Route::put('surat/{letterRequest}', 'update')->name('surat.update');
        Route::get('surat/{letterRequest}/show', 'show')->name('surat.show');
        Route::delete('surat/{letterRequest}', 'destroy')->name('surat.destroy');
        Route::get('surat/{letterRequest}/download', 'download')->name('surat.download');
    });
});

Route::get('surat/{letterRequest}/download', [LetterRequestByMhsController::class, 'download'])->name('letter.download');