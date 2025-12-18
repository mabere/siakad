<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Backend\CourseController;
use App\Http\Controllers\QrVerificationController;
use App\Http\Controllers\Akademik\AlumniController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\CurriculumController;
use App\Http\Controllers\Backend\MkduCourseController;
use App\Http\Controllers\Backend\AnnouncementController;

Route::middleware('auth')->group(function () {
    Route::get('/verify', [QrVerificationController::class, 'showVerificationForm'])->name('verify.form');
    Route::post('/verify-qr', [QrVerificationController::class, 'verifyQrCode'])->name('verify.qr');
    // Route notifications
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.all');
    Route::get('/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');

    Route::group(['prefix' => 'calendar'], function () {
        Route::get('/', [AcademicCalendarController::class, 'index'])->name('calendar.index');
        Route::get('/kegiatan', [AcademicCalendarController::class, 'indexAdmin'])->name('kegiatan.index');
        Route::post('/kegiatan', [AcademicCalendarController::class, 'store'])->name('calendar.store');
        Route::get('/{event}/edit', [AcademicCalendarController::class, 'edit'])->name('calendar.edit');
        Route::put('/{event}', [AcademicCalendarController::class, 'update'])->name('calendar.update');
        Route::post('/{event}/publish', [AcademicCalendarController::class, 'publish'])->name('calendar.publish');
        Route::post('/{event}/unpublish', [AcademicCalendarController::class, 'unpublish'])->name('calendar.unpublish');
        Route::delete('/{event}', [AcademicCalendarController::class, 'destroy'])->name('calendar.destroy');
    });

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/update-bio', [ProfileController::class, 'updateBio'])->name('profile.bio.update');
    Route::post('/user/set-role', [UserController::class, 'setRole'])->name('user.setRole');
    Route::resource('announcements', AnnouncementController::class);
    Route::patch('announcements/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');

});

// Include role-specific routes
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/dekan.php';
require __DIR__ . '/edom.php';
require __DIR__ . '/kaprodi.php';
require __DIR__ . '/lecturer.php';
require __DIR__ . '/letter.php';
require __DIR__ . '/monitoring.php';
require __DIR__ . '/remedial.php';
require __DIR__ . '/staff.php';
require __DIR__ . '/student.php';
require __DIR__ . '/skripsi.php';

Route::fallback(function () {
    return view('errors.404');
});


Route::middleware(['auth'])->prefix('jadwal-ujian')->group(function () {
    Route::get('/', [App\Http\Controllers\ThesisScheduleController::class, 'index'])->name('jadwal.ujian.index');
    Route::get('/cetak', [App\Http\Controllers\ThesisScheduleController::class, 'cetak'])->name('jadwal.ujian.cetak');
    Route::get('/cetak-sk', [App\Http\Controllers\ThesisScheduleController::class, 'cetakSk'])->name('jadwal.ujian.cetak.sk');
    // Kurikulum
    Route::resource('curriculums', CurriculumController::class);
    Route::resource('curriculums.courses', CourseController::class)->except(['show']);
    Route::get('curriculums/{curriculum}/courses/export', [CourseController::class, 'export'])->name('curriculums.courses.export');
    Route::post('curriculums/{curriculum}/courses/import', [CourseController::class, 'import'])->name('curriculums.courses.import');
    Route::post('curriculums/{curriculum}/copy', [CurriculumController::class, 'copy'])->name('curriculums.copy');
    Route::resource('mkdu', MkduCourseController::class)->except(['show']);
    // Alumni Management
    Route::get('alumni/reports', [AlumniController::class, 'reports'])->name('alumni.reports');
    Route::get('alumni/export', [AlumniController::class, 'export'])->name('alumni.export');
});
