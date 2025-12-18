<?php

use App\Http\Middleware\CheckSks;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\Dekan;
use App\Http\Middleware\Kaprodi;
use App\Http\Middleware\Ujm;
use App\Http\Middleware\CheckTuitionFeeStatus;
use App\Http\Middleware\EdomMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tuition' => CheckTuitionFeeStatus::class,
            'checkRole' => CheckRole::class,
            'ujm' => Ujm::class,
            'dekan' => Dekan::class,
            'kaprodi' => Kaprodi::class,
            'edom.active' => EdomMiddleware::class,
            'checkSks' => CheckSks::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();