<?php

namespace App\Providers;

use App\Models\Kelas;
use App\Models\Course;
use App\Models\Thesis;
use App\Models\Schedule;
use App\Models\Curriculum;
use App\Models\ThesisExam;
use App\Models\Announcement;
use App\Models\Grade;
use App\Models\LetterRequest;
use App\Policies\GradePolicy;
use App\Policies\KelasPolicy;
use App\Policies\CoursePolicy;
use App\Policies\RequestPolicy;
use App\Policies\SkripsiPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\CurriculumPolicy;
use App\Policies\ThesisExamPolicy;
use App\Policies\AnnouncementPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        LetterRequest::class => RequestPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
        Kelas::class => KelasPolicy::class,
        Thesis::class => SkripsiPolicy::class,
        ThesisExam::class => ThesisExamPolicy::class,
        Curriculum::class => CurriculumPolicy::class,
        Course::class => CoursePolicy::class,
        Schedule::class => SchedulePolicy::class,
        Grade::class => GradePolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates here if needed
        Gate::define('create-remedial', function ($user) {
            return $user->hasRole('mahasiswa');
        });

        Gate::define('process-remedial', function ($user) {
            return $user->hasRole('dosen');
        });

        Gate::define('review-remedial', function ($user) {
            return $user->hasRole('staff');
        });

        Gate::define('validate-remedial', function ($user) {
            return $user->hasRole('staff');
        });

        Gate::define('approve-remedial', function ($user) {
            return $user->hasRole('kaprodi');
        });
    }
}