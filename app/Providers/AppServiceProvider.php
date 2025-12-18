<?php

namespace App\Providers;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\Publication;
use App\Policies\PublicationPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Services\LetterRequest\DepartmentLetterRequestService;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Publication::class => PublicationPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();


    }
}
