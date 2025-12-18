<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Edom\EdomMhsController;
use App\Http\Controllers\Edom\EdomReportController;
use App\Http\Controllers\Edom\EdomSettingController;
use App\Http\Controllers\edom\EdomQuestionController;
use App\Http\Controllers\Edom\EdomManagementController;
use App\Http\Controllers\Edom\EdomCategoryManagementController;


// ========== ROUTES UNTUK ADMIN ==========
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin/edom')->name('admin.edom.')->group(function () {

    // === KUISIONER ===
    Route::prefix('questionnaire')->name('questionnaire.')->group(function () {
        Route::get('/', [EdomManagementController::class, 'index'])->name('index');
        Route::get('/create', [EdomManagementController::class, 'create'])->name('create');
        Route::post('/', [EdomManagementController::class, 'store'])->name('store');
        Route::get('/{questionnaire}/edit', [EdomManagementController::class, 'edit'])->name('edit');
        Route::put('/{questionnaire}', [EdomManagementController::class, 'update'])->name('update');
        Route::delete('/{questionnaire}', [EdomManagementController::class, 'destroy'])->name('destroy');
        Route::put('/{questionnaire}/toggle', [EdomManagementController::class, 'toggleStatus'])->name('toggle');
        Route::get('/{questionnaire}/questions', [EdomManagementController::class, 'questions'])->name('questions');
    });

    // === PERTANYAAN ===
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/questionnaire/{questionnaire}', [EdomQuestionController::class, 'index'])->name('index');
        Route::get('/questionnaire/{questionnaire}/create', [EdomQuestionController::class, 'create'])->name('create');
        Route::post('/questionnaire/{questionnaire}', [EdomQuestionController::class, 'store'])->name('store');
        Route::get('/{question}/edit', [EdomQuestionController::class, 'edit'])->name('edit');
        Route::put('/{question}', [EdomQuestionController::class, 'update'])->name('update');
        Route::delete('/{question}', [EdomQuestionController::class, 'destroy'])->name('destroy');
    });

    // === KATEGORI ===
    Route::resource('categories', EdomCategoryManagementController::class);
    Route::post('/category', [EdomCategoryManagementController::class, 'storeCategory'])->name('category.store');

    // === PENGATURAN EDOM ===
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [EdomSettingController::class, 'index'])->name('index');
        Route::put('/', [EdomSettingController::class, 'update'])->name('update');
        Route::post('/toggle', [EdomSettingController::class, 'toggleEdom'])->name('toggle');
    });

    // === LAPORAN ===
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [EdomReportController::class, 'index'])->name('index');
        Route::get('/department', [EdomReportController::class, 'departmentsReport'])->name('departments');
        Route::get('/department/{department}', [EdomReportController::class, 'departmentReport'])->name('department');
        Route::get('/lecturer/{lecturer}', [EdomReportController::class, 'lecturerDetail'])->name('lecturer.detail');
        Route::get('/export-pdf', [EdomReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export-excel', [EdomReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/department/{department}/export', [EdomReportController::class, 'exportDepartmentPdf'])->name('department.export');
    });
});


// ========== ROUTES UNTUK MAHASISWA ==========
Route::middleware(['auth', 'checkRole:mahasiswa'])->prefix('mhs/edom')->name('student.edom.')->group(function () {
    Route::get('/', [EdomMhsController::class, 'index'])->name('index');
    Route::get('/{schedule}/create', [EdomMhsController::class, 'create'])->middleware('edom.active')->name('create');
    Route::post('/{schedule}', [EdomMhsController::class, 'store'])->middleware('edom.active')->name('store');
});


// ========== EXTRA ROUTE UMUM (PDF EXPORT) ==========
Route::get('/laporan', [EdomReportController::class, 'exportPdf'])->name('reports.export');