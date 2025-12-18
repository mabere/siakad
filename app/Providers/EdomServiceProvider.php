<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class EdomServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Share EDOM settings to all views
        View::composer('*', function ($view) {
            $view->with('edomSettings', Setting::getEdomSettings());
        });

        // Add middleware for checking EDOM status
        $this->app['router']->aliasMiddleware('edom.active', \App\Http\Middleware\EdomActive::class);
    }
}