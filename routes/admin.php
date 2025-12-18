<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\Student\MhsController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Akademik\AlumniController;
use App\Http\Controllers\Admin\ThesisSupervisionController;
use App\Http\Controllers\Lecturer\{ResearchController, SupervisionMeetingController};
use App\Http\Controllers\Backend\{UnitController, UserController, KelasController, CourseController, FacultyController, SarprasController, ServiceController, StudentController, BuildingAndRoomController, LecturerController, EmployeeController, DepartmentController, AcademicYearController, PenunjangAdminController, AdminLetterTypeController, LetterManagementController, PenunjangValidationController, StudentSemesterStatusController};

// Group admin routes using macro
Route::roleGroup('admin', [], function () {
    Route::get('/statistik', [StatisticController::class, 'index'])->name('statistik.index');
    Route::resources(['faculty' => FacultyController::class, 'prodi' => DepartmentController::class, 'ta' => AcademicYearController::class, 'kelas' => KelasController::class, 'mk' => CourseController::class, 'gedung' => BuildingAndRoomController::class, 'sarpras' => SarprasController::class, 'mhs' => StudentController::class, 'dosen' => LecturerController::class, 'pegawai' => EmployeeController::class, 'status-mhs' => StudentSemesterStatusController::class, 'publication' => ResearchController::class, 'pkm' => ServiceController::class, 'penunjang' => PenunjangAdminController::class, 'letter-types' => AdminLetterTypeController::class, 'users' => UserController::class, 'alumni' => AlumniController::class, 'units' => UnitController::class,]);
    Route::get('/ruangan', [BuildingAndRoomController::class, 'indexRoom'])->name('ruangan.index');
    Route::post('/ruangan', [BuildingAndRoomController::class, 'storeRoom'])->name('ruangan.store');
    Route::put('/ruangan/{room}', [BuildingAndRoomController::class, 'updateRoom'])->name('ruangan.update');
    Route::delete('/ruangan/{id}', [BuildingAndRoomController::class, 'destroyRoom'])->name('ruangan.destroy');

    Route::post('faculty/{id}/assign-dean', [FacultyController::class, 'assignDekan'])->name('faculty.assign-dean');
    Route::put('department/{id}/assign-kaprodi', [DepartmentController::class, 'assignKaprodi'])->name('department.assignKaprodi');

    Route::get('kelas/department/{department_id}', [KelasController::class, 'indexByDepartment'])->name('kelas.byDepartment');
    Route::get('kelas/department/create/{department_id}', [KelasController::class, 'createKelasForDepartment'])->name('kelas.department.create');

    Route::get('dosen/department/{id}', [LecturerController::class, 'listDosen'])->name('dosen.by.department');
    Route::get('mhs/department/{id}', [StudentController::class, 'listMhsProdi'])->name('mhs.by.department');
    Route::get('mhs/create/{department_id}', [StudentController::class, 'create'])->name('mhs.dpt.create');
    Route::post('mhs/store/{department_id}', [StudentController::class, 'store'])->name('mhs.dpt.store');
    Route::get('mhs/edit/{id}', [StudentController::class, 'edit'])->name('mhs.dpt.edit');
    Route::put('mhs/update/{id}', [StudentController::class, 'update'])->name('mhs.dpt.update');

    Route::get('students/print-ktm', [MhsController::class, 'printKtm'])->name('print.ktm');

    Route::post('assign/{mhs}/user', [StudentController::class, 'assignUser'])->name('assign.mhs');
    Route::post('students/assign-multiple', [StudentController::class, 'assignMultipleUsers'])->name('mhs.assign-multiple');
    Route::post('mhs/unassign/{id}', [StudentController::class, 'unassignUser'])->name('mhs.unassign');
    Route::post('assign/user/{dosen}', [LecturerController::class, 'assignDosenUser'])->name('assign.dosen');

    // Mata Kuliah & Jadwal Perkuliahan
    Route::get('/mk/department/{department_id}', [CourseController::class, 'coursesByDepartment'])->name('mk.byDepartment');
    Route::prefix('jadwal')->name('list-jadwal.')->group(function () {
        Route::get('/prodi', [ScheduleController::class, 'index'])->name('prodi');
        Route::get('{department}/schedule', [ScheduleController::class, 'show'])->name('show');
        Route::get('{department}/schedule/create', [ScheduleController::class, 'create'])->name('create');
        Route::post('{department}/schedule', [ScheduleController::class, 'store'])->name('store');
        Route::get('{department}/schedule/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
        Route::put('{department}/schedule/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('{department}/schedule/{schedule}', [ScheduleController::class, 'destroy'])->name('delete');

    });

    // Penunjang Validation
    Route::prefix('penunjang')->name('penunjang.')->group(function () {
        Route::get('dashboard/detail', [PenunjangValidationController::class, 'dashboard'])->name('dashboard.detail');
        Route::get('dashboard/export', [PenunjangValidationController::class, 'exportDashboardExcel'])->name('dashboard.export');
        Route::get('validation/list', [PenunjangValidationController::class, 'index'])->name('validation.list');
        Route::get('validation/{penunjang}', [PenunjangValidationController::class, 'show'])->name('validation.show');
        Route::post('validation/{penunjang}', [PenunjangValidationController::class, 'process'])->name('validation.validate');
    });

    // Manajemen Surat
    Route::prefix('letter-requests')->name('letter-requests.')->group(function () {
        Route::get('', [LetterManagementController::class, 'index'])->name('index');
        Route::get('mhs', [LetterManagementController::class, 'indexMhs'])->name('index-mhs');
        Route::get('{letterRequest}', [LetterManagementController::class, 'show'])->name('show');
        Route::get('mhs/{letterRequest}', [LetterManagementController::class, 'showMhs'])->name('show-mhs');
        Route::post('{letterRequest}/process', [LetterManagementController::class, 'process'])->name('process');
        Route::post('{letterRequest}/reject', [LetterManagementController::class, 'reject'])->name('reject');
        Route::get('{letterRequest}/download', [LetterManagementController::class, 'download'])->name('download');
    });

    // Thesis Supervision
    Route::prefix('thesis/supervision')->name('thesis.supervision.')->group(function () {
        Route::get('/', [ThesisSupervisionController::class, 'index'])->name('index');
        Route::get('/assign', [ThesisSupervisionController::class, 'assignForm'])->name('assign');
        Route::post('/assign', [ThesisSupervisionController::class, 'store'])->name('store');
        Route::get('/{supervision}/edit', [ThesisSupervisionController::class, 'edit'])->name('edit');
        Route::put('/{supervision}', [ThesisSupervisionController::class, 'update'])->name('update');
        Route::delete('/{supervision}', [ThesisSupervisionController::class, 'destroy'])->name('destroy');

        Route::prefix('meetings')->name('meetings.')->group(function () {
            Route::get('/', [SupervisionMeetingController::class, 'index'])->name('index');
            Route::get('/{meeting}', [SupervisionMeetingController::class, 'show'])->name('show');
            Route::put('/{meeting}', [SupervisionMeetingController::class, 'update'])->name('update');
            Route::delete('/{meeting}', [SupervisionMeetingController::class, 'destroy'])->name('destroy');
        });
    });

    // Import & Export
    Route::post('import/course', [CourseController::class, 'import'])->name('course.import');
    Route::post('import/lecturer', [LecturerController::class, 'import'])->name('lecturer.import');
    Route::post('import/mhs', [StudentController::class, 'import'])->name('mhs.import');

    Route::get('export/course', [CourseController::class, 'export'])->name('course.export');
    Route::get('export/mhs', [StudentController::class, 'export'])->name('mhs.export');
    Route::get('dosen/department/{id}/export', [LecturerController::class, 'export'])->name('lecturer.export');

    // Signature Unit
    Route::put('units/{id}/signature', [UnitController::class, 'ubahGambar'])->name('units.signature.update');

    // Tahun Akademik Aktif
    Route::post('academic-years/{id}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');
    // Import jadwal
    Route::get('get-courses-by-type', [ScheduleController::class, 'getCoursesByType'])->name('get-courses-by-type');
    Route::post('list-jadwal/{department}/import', [ScheduleController::class, 'import'])->name('list-jadwal.import');
});

// Alumni Self Management
Route::roleGroup('alumni', [], function () {
    Route::get('/dashboard', [AlumniController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AlumniController::class, 'alumniProfile'])->name('profile');
    Route::put('/profile', [AlumniController::class, 'updateAlumniProfile'])->name('profile.update');
});
